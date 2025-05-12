<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeSiteJob;
use Illuminate\Console\Command;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Log;

class ScrapeSites extends Command
{
    protected $signature   = 'scrape:sites';
    protected $description = 'Crawl each tourism site for attractions';

    public function handle(SitemapService $sitemap)
    {
        $sites    = config('scrapers.sites');
        $sitemaps = config('scrapers.sitemaps', []);
        $patterns = config('scrapers.detail_pattern', []);

        Log::info("Starting scrape process", [
            'sites' => array_keys($sites),
            'sitemaps' => array_keys($sitemaps)
        ]);

        foreach ($sites as $key => $baseUrl) {
            $this->info("Fetching sitemap for {$key}");

            if (!isset($sitemaps[$key])) {
                $this->warn("No sitemap defined for {$key}, skipping...");
                continue;
            }

            $urls = $sitemap->getUrls($sitemaps[$key]);
            Log::info("Found URLs for {$key}", ['count' => count($urls)]);

            foreach ($urls as $url) {
                if (preg_match($patterns[$key] ?? '//', $url)) {
                    Log::info("Dispatching job", ['url' => $url, 'site' => $key]);
                    ScrapeSiteJob::dispatch($url, $key);
                    $this->line("Dispatched: {$url}");
                }
            }
        }

        $this->info("Dispatching completed.");
    }
}
