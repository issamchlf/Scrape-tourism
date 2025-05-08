<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Crawler\Crawler as SpatieCrawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ScraperService
{
    /**
     * Fetch page HTML, detect encoding via <meta> or headers, normalize to UTF-8, then return Crawler.
     */
    public function fetchCrawler(string $url): Crawler
    {
        $client   = HttpClient::create();
        $response = $client->request('GET', $url);
        $html     = $response->getContent();

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

        // Normalize to UTF-8
        $html = mb_convert_encoding($html, 'UTF-8', $sourceCharset);
        // Decode HTML entities (e.g. &aacute; → á)
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return new Crawler($html);
    }

    /**
     * Extract JSON-LD data for tourism types.
     */
    protected function extractJsonLd(Crawler $crawler): array
    {
        $data = [];
        $crawler->filter('script[type="application/ld+json"]')->each(function (Crawler $node) use (&$data) {
            $jsonText = $node->text();
            $json     = json_decode($jsonText, true);
            if (! $json) {
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
                    return; // stop after first match
                }
            }
        });
        return $data;
    }

    /**
     * Main scrape method: JSON-LD → OpenGraph → Microdata → CSS
     */
    public function scrape(string $url, array $selectors): array
    {
        $crawler = $this->fetchCrawler($url);
        $data    = $this->extractJsonLd($crawler);

        // OpenGraph fallback
        $data['name']        ??= $this->getMeta($crawler, 'property', 'og:title');
        $data['description'] ??= $this->getMeta($crawler, 'name',     'description');

        // Microdata fallback
        $data['name']    ??= $this->getItemProp($crawler, 'name');
        $data['address'] ??= $this->getItemProp($crawler, 'address');

        // CSS selectors fallback
        foreach ($selectors as $key => $css) {
            if (empty($data[$key])) {
                try {
                    $node = $crawler->filter($css);
                    $data[$key] = $node->count() ? trim($node->first()->text()) : null;
                } catch (\InvalidArgumentException $e) {
                    $data[$key] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Simple CSS-only scrape
     */
    public function scrapeWithDomCrawler(string $url, array $selectors): array
    {
        $crawler = $this->fetchCrawler($url);
        $data    = [];
        foreach ($selectors as $key => $css) {
            try {
                $node = $crawler->filter($css);
                $data[$key] = $node->count() ? trim($node->first()->text()) : null;
            } catch (\InvalidArgumentException $e) {
                $data[$key] = null;
            }
        }
        return $data;
    }

    /**
     * Concurrent crawler
     */
    public function scrapeWithSpatie(string $startUrl, CrawlObserver $observer): void
    {
        SpatieCrawler::create()
            ->setCrawlObserver($observer)
            ->startCrawling($startUrl);
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
