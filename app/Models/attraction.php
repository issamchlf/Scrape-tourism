<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class attraction extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'website_url',
        'images',
        'status'
    ];
    protected $casts = [
        'images' => 'array',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
