<?php
namespace App\Http\Controllers;

use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    /**
     * Show the “create” form, pre‑filled with scraped data.
     */
    public function create(Request $request)
    {
        $data = session('scrapedData', []);
        $url  = session('scrapedUrl', '');
        return view('attractions.create', compact('data','url'));
    }

    /**
     * Persist the attraction to the database.
     */
    public function store(Request $request)
    {
        $attrs = $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string',
            'hours'       => 'nullable|string',
            'website_url' => 'required|url',
        ]);

        // Upsert by URL
        $attraction = Attraction::updateOrCreate(
            ['website_url' => $attrs['website_url']],
            $attrs
        );

        return redirect()
            ->route('attractions.show', $attraction)
            ->with('success','Attraction saved!');
    }

    /**
     * Optional: show a saved attraction.
     */
    public function show(Attraction $attraction)
    {
        return view('attractions.show', compact('attraction'));
    }
}

