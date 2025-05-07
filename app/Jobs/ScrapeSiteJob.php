<?php

namespace App\Jobs;

use App\Models\Scraped;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScrapeSiteJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $url,
        public string $siteKey
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\ScraperService $scraper)
    {
        $selectors = config("scrapers.{$this->siteKey}.selectors");
        $raw       = $scraper->scrapeWithDomCrawler($this->url, $selectors);
        Scraped::create([
            'url'      => $this->url,
            'site_key'=> $this->siteKey,
            'status'   => 'success',
            'data_raw' => $raw,
            'last_scraped_at' => now(),
        ]);
    }
    
}
