<?php

namespace App\Http\Controllers;

use App\Models\scarped;
use App\Models\Scraped;
use App\Models\attraction;
use Illuminate\Http\Request;
use App\Services\ScraperService;

class AttractionController extends Controller
{
   protected ScraperService $scraper;

    public function __construct(ScraperService $scraper)
    {
         $this->scraper = $scraper;
    }
    public function createFromScrape($pageId)
    {
        $page = Scraped::findOrFail($pageId);
        $data = $page->data_raw;
        return view('attractions.create', compact('data'));
    }
}
