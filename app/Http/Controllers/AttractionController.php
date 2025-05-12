<?php
namespace App\Http\Controllers;

use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function index()
    {
        $attractions = Attraction::with('categories')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('attractions.index', compact('attractions'));
    }

    public function show(Attraction $attraction)
    {
        return view('attractions.show', compact('attraction'));
    }
}
