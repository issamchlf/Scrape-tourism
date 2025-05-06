<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    protected $fillable = [
        'attraction_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
