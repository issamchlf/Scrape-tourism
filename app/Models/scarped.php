<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class scarped extends Model
{
    protected $fillable = [
        'url',
        'status',
        'last_scraped_at',
    ];
}
