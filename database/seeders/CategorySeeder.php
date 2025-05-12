<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cultural',
                'slug' => 'cultural',
            ],
            [
                'name' => 'Events',
                'slug' => 'events',
            ],
            [
                'name' => 'Museums',
                'slug' => 'museums',
            ],
            [
                'name' => 'Activities',
                'slug' => 'activities',
            ],
            [
                'name' => 'Food & Drink',
                'slug' => 'food-drink',
            ],
            [
                'name' => 'Shopping',
                'slug' => 'shopping',
            ],
            [
                'name' => 'Transportation',
                'slug' => 'transportation',
            ],
            [
                'name' => 'Beaches',
                'slug' => 'beaches',
            ],
            [
                'name' => 'Historical Sites',
                'slug' => 'historical-sites',
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 