<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Crawler\Crawler as SpatieCrawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ScraperService
{
    /**
     * Fetch page HTML and return a Crawler instance.
     */
    public function fetchCrawler(string $url): Crawler
    {
        $client = HttpClient::create();
        $html   = $client->request('GET', $url)->getContent();

        return new Crawler($html);
    }

    /**
     * Layered scrape: JSON-LD → OpenGraph → Microdata → CSS selectors
     */
    public function scrape(string $url, array $selectors): array
    {
        $crawler = $this->fetchCrawler($url);
        $data    = [];

        // 1) JSON-LD
        try {
            $node = $crawler->filter('script[type="application/ld+json"]')->first();
            if ($node->count()) {
                $json  = json_decode($node->text(), true);
                $items = $json['@graph'] ?? [$json];
                foreach ($items as $item) {
                    $type = $item['@type'] ?? '';
                    if (in_array($type, ['TouristAttraction','LocalBusiness','Hotel','LodgingBusiness'])) {
                        $data['name']        = $item['name']        ?? null;
                        $data['description'] = $item['description'] ?? null;
                        if (isset($item['address'])) {
                            $addr = $item['address'];
                            $data['address'] = is_array($addr)
                                ? ($addr['streetAddress'] ?? null)
                                : $addr;
                        }
                        if (isset($item['geo'])) {
                            $data['latitude']  = $item['geo']['latitude']  ?? null;
                            $data['longitude'] = $item['geo']['longitude'] ?? null;
                        }
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore JSON-LD errors
        }

        // 2) OpenGraph
        $data['name']        ??= $this->getMeta($crawler, 'property', 'og:title');
        $data['description'] ??= $this->getMeta($crawler, 'name',     'description');

        // 3) Microdata
        $data['name']    ??= $this->getItemProp($crawler, 'name');
        $data['address'] ??= $this->getItemProp($crawler, 'address');

        // 4) CSS selectors fallback
        foreach ($selectors as $key => $css) {
            if (empty($data[$key])) {
                try {
                    $node = $crawler->filter($css);
                    $data[$key] = $node->count()
                        ? trim($node->first()->text())
                        : null;
                } catch (\InvalidArgumentException $e) {
                    $data[$key] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Simple CSS-only scrape (DomCrawler)
     */
    public function scrapeWithDomCrawler(string $url, array $selectors): array
    {
        $crawler = $this->fetchCrawler($url);
        $data    = [];

        foreach ($selectors as $key => $css) {
            try {
                $node = $crawler->filter($css);
                $data[$key] = $node->count()
                    ? trim($node->first()->text())
                    : null;
            } catch (\InvalidArgumentException $e) {
                $data[$key] = null;
            }
        }

        return $data;
    }

    /**
     * Dispatch a Spatie Crawler for deep or concurrent crawling
     */
    public function scrapeWithSpatie(string $startUrl, CrawlObserver $observer): void
    {
        SpatieCrawler::create()
            ->setCrawlObserver($observer)
            ->startCrawling($startUrl);
    }

    /**
     * Helper: get meta content by attribute
     */
    protected function getMeta(Crawler $crawler, string $attr, string $key): ?string
    {
        try {
            return $crawler->filter("meta[{$attr}='{$key}']")->attr('content');
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Helper: get microdata text by itemprop
     */
    protected function getItemProp(Crawler $crawler, string $prop): ?string
    {
        try {
            $node = $crawler->filter("[itemprop='{$prop}']");
            return $node->count()
                ? trim($node->first()->text())
                : null;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
