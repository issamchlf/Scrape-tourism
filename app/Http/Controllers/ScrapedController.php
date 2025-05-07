<?php

namespace App\Http\Controllers;

use App\Models\Scraped;
use App\Services\ScraperService;
use Illuminate\Http\Request;

class ScrapedController extends Controller
{
    public function create()
    {
        // Show the form
        return view('scraped_pages.create');
    }

    public function store(Request $request, ScraperService $scraper)
    {
        // Validate site key + URL
        $request->validate([
            'site_key' => 'required|string|in:'.implode(',',array_keys(config('scrapers.sites'))),
            'url'      => 'required|url',
        ]);

        $siteKey   = $request->site_key;
        $url       = $request->url;
        $selectors = config("scrapers.{$siteKey}.selectors", []);

        // Record a pending scrape
        $page = Scraped::create([
            'site_key'       => $siteKey,
            'url'            => $url,
            'status'         => 'pending',
            'last_scraped_at'=> now(),
        ]);

        // Perform the layered scrape
        try {
            $data = $scraper->scrape($url, $selectors);
            $page->update([
                'status'   => 'success',
                'data_raw' => $data,
            ]);
        } catch (\Throwable $e) {
            $page->update(['status'=>'error']);
            return back()
                ->withErrors(['url'=>'Scrape failed: '.$e->getMessage()])
                ->withInput();
        }

        // Return the form view with the results
        return view('scraped_pages.create', [
            'scrapedData'=> $data,
            'scrapedUrl' => $url,
        ]);
    }
}
