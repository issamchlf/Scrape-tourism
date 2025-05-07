<?php

namespace App\Http\Controllers;

use App\Models\scarped;
use App\Services\ScraperService;
use Illuminate\Http\Request;
use spatie\Crawler\Crawler;


class ScrapedController extends Controller
{
    protected ScraperService $scraper;
    public function __construct(ScraperService $scraper)
    {
        $this->scraper = $scraper;
    }
    public function create()
    {
        return view('scraped_pages.create');
    }
    public function store(Request $request)
    {
        $request->validate([
          'url'      => 'required|url',
          'site_key' => 'required|string',
        ]);
    
        $siteConfig = config("scrapers.{$request->site_key}");
        if (! $siteConfig) {
            return back()->withErrors(['site_key' => 'Unknown site key.']);
        }
    
        $selectors = $siteConfig['selectors'];
        $data      = $this->scraper->scrapeWithDomCrawler($request->url, $selectors);
    
        // return the form view again, injecting the scraped data
        return view('scraped_pages.create', [
            'scrapedUrl'  => $request->url,
            'scrapedData' => $data,
        ]);
    }
    
}
