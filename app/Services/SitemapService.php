<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SitemapService
{
    public function getUrls(string $sitemapUrl): array
    {
        try {
            Log::info("Fetching sitemap from: {$sitemapUrl}");
            $response = Http::get($sitemapUrl);
            
            if (!$response->successful()) {
                Log::error("Failed to fetch sitemap: {$sitemapUrl}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $body = $response->body();
            Log::debug("Sitemap content length: " . strlen($body));
            
            $xml = new \SimpleXMLElement($body);
            $urls = [];
            
            // Check if this is a sitemap index
            if (isset($xml->sitemap)) {
                Log::info("Found sitemap index, processing nested sitemaps");
                foreach ($xml->sitemap as $sitemap) {
                    $nestedUrls = $this->getUrls((string)$sitemap->loc);
                    $urls = array_merge($urls, $nestedUrls);
                }
            } else {
                // Regular sitemap
                foreach ($xml->url as $node) {
                    $urls[] = (string)$node->loc;
                }
            }
            
            Log::info("Extracted " . count($urls) . " URLs from sitemap");
            return $urls;
        } catch (\Exception $e) {
            Log::error("Error processing sitemap: {$sitemapUrl}", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
