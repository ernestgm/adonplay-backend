<?php

// app/Models/LoginCode.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginCode extends Model
{
    protected $fillable = ['code', 'device_id', 'user_id', 'expires_at'];
    protected $dates = ['expires_at'];
}
