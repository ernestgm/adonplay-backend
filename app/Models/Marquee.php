<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marquee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'background_color',
        'text_color',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

