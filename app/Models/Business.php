<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function slides()
    {
        return $this->hasMany(Slide::class);
    }

    public function marquees()
    {
        return $this->hasMany(Marquee::class);
    }

    public function qrs()
    {
        return $this->hasMany(Qr::class);
    }
}
