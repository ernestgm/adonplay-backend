<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'device_id',
        'portrait',
        'as_presentation',
        'user_id',
        'slide_id',
        'marquee_id',
        'qr_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function slide()
    {
        return $this->belongsTo(Slide::class);
    }

    public function marquee()
    {
        return $this->belongsTo(Marquee::class);
    }

    public function qr()
    {
        return $this->belongsTo(Qr::class);
    }
}

