<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Crawler\Crawler as SpatieCrawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    /**
     * Fetch page HTML, detect encoding via <meta> or headers, normalize to UTF-8, then return Crawler.
     */
    public function fetchCrawler(string $url): Crawler
    {
        $maxAttempts = 3;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            try {
                $client = HttpClient::create([
                    'timeout' => 30,
                    'http_version' => '2.0',
                    'max_redirects' => 5,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                    ]
                ]);

                Log::info("Fetching URL: {$url} (Attempt " . ($attempt + 1) . "/{$maxAttempts})");
                $response = $client->request('GET', $url);
                $html = $response->getContent();

                // 1) Try to detect charset from <meta charset="...">
                if (preg_match('/<meta[^>]+charset=["\']?([^"\'>]+)/i', $html, $matches)) {
                    $sourceCharset = strtoupper($matches[1]);
                }
                // 2) Fallback: detect from Content-Type header
                elseif (!empty($response->getHeaders()['content-type'][0]) 
                    && preg_match('/charset=([^;\s]+)/i', $response->getHeaders()['content-type'][0], $matches2))
                {
                    $sourceCharset = strtoupper($matches2[1]);
                } else {
                    $sourceCharset = 'UTF-8';
                }

                Log::debug("Detected charset: {$sourceCharset}");

                // Normalize to UTF-8
                $html = mb_convert_encoding($html, 'UTF-8', $sourceCharset);
                // Decode HTML entities (e.g. &aacute; → á)
                $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                return new Crawler($html);
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                if ($attempt < $maxAttempts) {
                    $waitTime = pow(2, $attempt); // Exponential backoff: 2, 4, 8 seconds
                    Log::warning("Failed to fetch URL (Attempt {$attempt}/{$maxAttempts}), retrying in {$waitTime} seconds", [
                        'url' => $url,
                        'error' => $e->getMessage()
                    ]);
                    sleep($waitTime);
                }
            }
        }

        // If we get here, all attempts failed
        Log::error("Failed to fetch URL after {$maxAttempts} attempts", [
            'url' => $url,
            'error' => $lastException->getMessage(),
            'trace' => $lastException->getTraceAsString()
        ]);
        throw $lastException;
    }

    /**
     * Extract JSON-LD data for tourism types.
     */
    protected function extractJsonLd(Crawler $crawler): array
    {
        $data = [];
        try {
            $crawler->filter('script[type="application/ld+json"]')->each(function (Crawler $node) use (&$data) {
                $jsonText = $node->text();
                $json = json_decode($jsonText, true);
                if (! $json) {
                    Log::warning("Invalid JSON-LD found");
                    return; // invalid JSON, skip
                }

                $items = $json['@graph'] ?? [$json];
                foreach ($items as $item) {
                    $type = $item['@type'] ?? '';
                    if (in_array($type, ['TouristAttraction','LocalBusiness','Hotel','LodgingBusiness','TouristDestination'])) {
                        $data['name']        = $item['name']        ?? null;
                        $data['description'] = $item['description'] ?? null;
                        if (!empty($item['address'])) {
                            $addr = $item['address'];
                            $data['address'] = is_array($addr)
                                ? ($addr['streetAddress'] ?? null)
                                : $addr;
                        }
                        if (!empty($item['geo'])) {
                            $data['latitude']  = $item['geo']['latitude']  ?? null;
                            $data['longitude'] = $item['geo']['longitude'] ?? null;
                        }
                        Log::info("Found JSON-LD data", ['type' => $type, 'data' => $data]);
                        return; // stop after first match
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error("Error extracting JSON-LD", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        return $data;
    }

    /**
     * Main scrape method: JSON-LD → OpenGraph → Microdata → CSS
     */
    public function scrape(string $url, array $selectors): array
    {
        try {
            $crawler = $this->fetchCrawler($url);
            $data    = $this->extractJsonLd($crawler);

            // Store the raw HTML for image extraction
            $data['html'] = $crawler->html();

            // OpenGraph fallback
            $data['name']        ??= $this->getMeta($crawler, 'property', 'og:title');
            $data['description'] ??= $this->getMeta($crawler, 'name',     'description');
            $data['image']       ??= $this->getMeta($crawler, 'property', 'og:image');

            // Microdata fallback
            $data['name']    ??= $this->getItemProp($crawler, 'name');
            $data['address'] ??= $this->getItemProp($crawler, 'address');
            $data['image']   ??= $this->getItemProp($crawler, 'image');

            // CSS selectors fallback
            foreach ($selectors as $key => $css) {
                if (empty($data[$key])) {
                    try {
                        $node = $crawler->filter($css);
                        if ($key === 'image') {
                            // For images, get the src attribute
                            $data[$key] = $node->count() ? $node->first()->attr('src') : null;
                        } else {
                            $data[$key] = $node->count() ? trim($node->first()->text()) : null;
                        }
                        if ($data[$key]) {
                            Log::debug("Found data via CSS selector", ['key' => $key, 'selector' => $css]);
                        }
                    } catch (\InvalidArgumentException $e) {
                        Log::warning("Invalid CSS selector", ['key' => $key, 'selector' => $css]);
                        $data[$key] = null;
                    }
                }
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("Failed to scrape URL: {$url}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Simple CSS-only scrape
     */
    public function scrapeWithDomCrawler(string $url, array $selectors): array
    {
        try {
            $crawler = $this->fetchCrawler($url);
            
            // Debug: Log the HTML structure
            Log::debug("HTML structure for {$url}", [
                'html' => $crawler->html(),
                'title' => $crawler->filter('title')->count() ? $crawler->filter('title')->text() : 'No title found',
                'h1_count' => $crawler->filter('h1')->count(),
                'h1_texts' => $crawler->filter('h1')->each(function ($node) { return $node->text(); }),
                'meta_description' => $crawler->filter('meta[name="description"]')->count() ? $crawler->filter('meta[name="description"]')->attr('content') : 'No meta description',
                'og_title' => $crawler->filter('meta[property="og:title"]')->count() ? $crawler->filter('meta[property="og:title"]')->attr('content') : 'No og:title',
                'og_description' => $crawler->filter('meta[property="og:description"]')->count() ? $crawler->filter('meta[property="og:description"]')->attr('content') : 'No og:description',
                'og_image' => $crawler->filter('meta[property="og:image"]')->count() ? $crawler->filter('meta[property="og:image"]')->attr('content') : 'No og:image'
            ]);
            
            $data    = [];
            foreach ($selectors as $key => $css) {
                try {
                    $node = $crawler->filter($css);
                    $data[$key] = $node->count() ? trim($node->first()->text()) : null;
                    if ($data[$key]) {
                        Log::debug("Found data via CSS selector", ['key' => $key, 'selector' => $css, 'value' => $data[$key]]);
                    } else {
                        Log::debug("No data found for selector", ['key' => $key, 'selector' => $css]);
                    }
                } catch (\InvalidArgumentException $e) {
                    Log::warning("Invalid CSS selector", ['key' => $key, 'selector' => $css, 'error' => $e->getMessage()]);
                    $data[$key] = null;
                }
            }
            return $data;
        } catch (\Exception $e) {
            Log::error("Failed to scrape URL with DOM crawler: {$url}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Concurrent crawler
     */
    public function scrapeWithSpatie(string $startUrl, CrawlObserver $observer): void
    {
        try {
            SpatieCrawler::create()
                ->setCrawlObserver($observer)
                ->startCrawling($startUrl);
        } catch (\Exception $e) {
            Log::error("Failed to start Spatie crawler", [
                'url' => $startUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /** Helper: read meta tag content **/
    protected function getMeta(Crawler $crawler, string $attr, string $key): ?string
    {
        try {
            return $crawler->filter("meta[{$attr}='{$key}']")->attr('content');
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /** Helper: read microdata **/
    protected function getItemProp(Crawler $crawler, string $prop): ?string
    {
        try {
            $node = $crawler->filter("[itemprop='{$prop}']");
            return $node->count() ? trim($node->first()->text()) : null;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
