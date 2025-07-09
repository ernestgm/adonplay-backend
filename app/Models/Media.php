<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'slide_id',
        'type',
        'file_path',
        'audio_path',
        'description',
        'description_position',
        'description_size',
        'qr_info',
        'qr_position',
        'duration',
    ];

    public function slide()
    {
        return $this->belongsTo(Slide::class);
    }
}
