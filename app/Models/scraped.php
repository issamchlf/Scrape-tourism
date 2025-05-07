<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class scraped extends Model
{
    protected $fillable = [
        'site_key','url','status','data_raw','last_scraped_at'
    ];
    protected $casts = [
        'data_raw'       => 'array',
        'last_scraped_at'=> 'datetime',
    ];
}
