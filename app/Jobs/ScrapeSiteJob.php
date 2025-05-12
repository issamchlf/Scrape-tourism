<?php

namespace App\Jobs;

use App\Models\Scraped;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        try {
            $siteSelectors     = config("scrapers.selectors.{$this->siteKey}") ?? [];
            $fallbackSelectors = config("scrapers.fallback_selectors") ?? [];
        
            // Merge fallback values *first* so site-specific ones override them
            $selectors = array_merge($fallbackSelectors, $siteSelectors);
        
            // Get both structured and raw data
            $raw = $scraper->scrapeWithDomCrawler($this->url, $selectors);
            $structured = $scraper->scrape($this->url, $selectors);
            
            // Log the data we're getting
            Log::info("Scraped data for {$this->url}", [
                'raw' => $raw,
                'structured' => $structured
            ]);
            
            // Combine both data sets, preferring structured data
            $combinedData = array_merge($raw, $structured);
            
            // Clean and validate the data
            $cleanedData = array_map(function($value) {
                if (is_string($value)) {
                    $value = trim($value);
                    return !empty($value) ? $value : null;
                }
                return $value;
            }, $combinedData);
            
            // Remove null values
            $cleanedData = array_filter($cleanedData, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Store the raw data
            $scraped = Scraped::create([
                'url'             => $this->url,
                'site_key'        => $this->siteKey,
                'status'          => 'success',
                'data_raw'        => $cleanedData,
                'last_scraped_at' => now(),
            ]);
            
            Log::info("Created scraped record", ['id' => $scraped->id, 'data' => $cleanedData]);
            
            // Create attraction record if we have enough data
            if (!empty($cleanedData['name'])) {
                // Extract category from URL if not found
                $categoryName = null;
                if (empty($cleanedData['category'])) {
                    if (preg_match('#/que-ver-y-hacer/([^/]+)/#', $this->url, $matches)) {
                        $categoryName = ucfirst(str_replace('-', ' ', $matches[1]));
                    }
                } else {
                    $categoryName = $cleanedData['category'];
                }
                
                // Extract coordinates from URL if not found
                if (empty($cleanedData['latitude']) && empty($cleanedData['longitude'])) {
                    if (preg_match('#data-lat="([^"]+)"[^>]*data-lng="([^"]+)"#', $scraped->data_raw['html'] ?? '', $matches)) {
                        $cleanedData['latitude'] = $matches[1];
                        $cleanedData['longitude'] = $matches[2];
                    }
                }

                // Extract images from various sources
                $images = [];
                
                // 1. Try to get images from meta tags
                if (!empty($cleanedData['image'])) {
                    if (is_array($cleanedData['image'])) {
                        $images = array_merge($images, $cleanedData['image']);
                    } else {
                        $images[] = $cleanedData['image'];
                    }
                }

                // 2. Try to get images from gallery
                if (!empty($cleanedData['gallery'])) {
                    if (is_array($cleanedData['gallery'])) {
                        $images = array_merge($images, $cleanedData['gallery']);
                    } else {
                        $images[] = $cleanedData['gallery'];
                    }
                }

                // 3. Try to get images from HTML content
                if (!empty($scraped->data_raw['html'])) {
                    preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $scraped->data_raw['html'], $matches);
                    if (!empty($matches[1])) {
                        $images = array_merge($images, $matches[1]);
                    }
                }

                // Clean and validate image URLs
                $images = array_filter(array_map(function($url) {
                    // Convert relative URLs to absolute
                    if (strpos($url, 'http') !== 0) {
                        $url = rtrim($this->url, '/') . '/' . ltrim($url, '/');
                    }
                    return $url;
                }, $images));

                // Remove duplicates
                $images = array_unique($images);
                
                $attraction = \App\Models\Attraction::create([
                    'name' => $cleanedData['name'],
                    'description' => $cleanedData['description'] ?? $cleanedData['meta_description'] ?? '',
                    'address' => $cleanedData['address'] ?? '',
                    'latitude' => $cleanedData['latitude'] ?? '',
                    'longitude' => $cleanedData['longitude'] ?? '',
                    'website_url' => $cleanedData['website'] ?? $this->url,
                    'images' => $images
                ]);
                
                // Handle category relationship
                if ($categoryName) {
                    $category = \App\Models\Category::firstOrCreate(
                        ['name' => $categoryName],
                        ['slug' => \Str::slug($categoryName)]
                    );
                    $attraction->categories()->attach($category->id);
                }
                
                Log::info("Created attraction record", ['id' => $attraction->id, 'name' => $attraction->name, 'images' => $images]);
            } else {
                Log::warning("Not enough data to create attraction", ['url' => $this->url, 'data' => $cleanedData]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to scrape URL: {$this->url}", [
                'site_key' => $this->siteKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Scraped::create([
                'url'             => $this->url,
                'site_key'        => $this->siteKey,
                'status'          => 'error',
                'data_raw'        => ['error' => $e->getMessage()],
                'last_scraped_at' => now(),
            ]);
        }
    }
}
