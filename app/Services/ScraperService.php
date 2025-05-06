<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

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
}