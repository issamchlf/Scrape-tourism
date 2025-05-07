<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Crawler\Crawler as SpatieCrawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ScraperService
{
    /**
     * Create a new class instance.
     */
    public function scrapeWithDomCrawler(string $url, array $selectors): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $url);
        $html = $response->getContent();
        $crawler = new Crawler($html);

        $data = [];
        foreach ($selectors as $key => $css) {
            $node        = $crawler->filter($css);
            $data[$key]  = $node->count() ? trim($node->first()->text()) : null;

        }
        return $data;
    }
    public function scrapeWithSpatie(string $startUrl, CrawlObserver $observer): void
    {
        SpatieCrawler::create()
            ->setCrawlObserver($observer)
            ->startCrawling($startUrl);
    }
}