<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class scraped extends Model
{
    protected $fillable = [
        'url',
        'status',
        'last_scraped_at',
    ];
}
