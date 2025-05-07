<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:sites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch scraping jobs for all configured tourism sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sites = config('scraper.sites');

        foreach ($sites as $key => $url) {
            \App\Jobs\ScrapeSiteJob::dispatch($url, $key);
        }
        $this->info('Dispatched scrape jobs for '.count($sites).' sites.');
    }
}
