<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }
}
