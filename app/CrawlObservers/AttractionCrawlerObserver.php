<?php
namespace App\CrawlObservers;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use App\Jobs\ScrapeSiteJob;

class AttractionCrawlerObserver extends CrawlObserver
{
    protected string $siteKey;

    public function __construct(string $siteKey)
    {
        $this->siteKey = $siteKey;
    }

    public function crawled(
        
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null
    ): void {
        $html    = (string) $response->getBody();
        $crawler = new Crawler($html);
    
        // ==== Replace the placeholder selector with the real one ====
    
        // For esMadrid listing pages:
        if ($crawler->filter('ul.listing-teasers')->count()) {
            // find every <a> inside those teaser items
            $crawler->filter('ul.listing-teasers li.teaser-item a')
                ->each(function (Crawler $node) {
                    $detailUrl = $node->link()->getUri();
                    ScrapeSiteJob::dispatch($detailUrl, $this->siteKey);
                });
        }
    
        // DETAIL PAGES: detect via JSON‑LD presence or URL pattern
        if ($crawler->filter('script[type="application/ld+json"]')->count()) {
            ScrapeSiteJob::dispatch((string) $url, $this->siteKey);
        }
    }
    

    public function crawlFailed(
        UriInterface $url,
        \Throwable $exception,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null
    ): void {
        // Log or ignore failures
    }
    
}
