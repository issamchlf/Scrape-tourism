<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function attractions()
    {
        return $this->belongsToMany(Attraction::class);
    }
}
