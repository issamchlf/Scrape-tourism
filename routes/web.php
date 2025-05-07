<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapedController;
use App\Http\Controllers\AttractionController;


Route::get('/', fn() => redirect()->route('scraped-pages.create'));

Route::get('/scraped-pages/create', [ScrapedController::class, 'create'])
     ->name('scraped-pages.create');

Route::post('/scraped-pages', [ScrapedController::class, 'store'])
     ->name('scraped-pages.store');


Route::resource('attractions', AttractionController::class)
     ->only(['create','store','show']);