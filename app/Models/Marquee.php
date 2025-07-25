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
        'business_id',
        'message', // nuevo campo obligatorio
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
