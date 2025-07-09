<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Role::class);
    }

    /**
     * Relación: Un usuario puede tener muchos negocios (businesses)
     */
    public function businesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    /**
     * Relación: Un usuario puede tener muchos Marquee
     */
    public function marquees()
    {
        return $this->hasMany(Marquee::class, 'owner_id');
    }

    /**
     * Relación: Un usuario puede tener muchos Qrs
     */
    public function qrs()
    {
        return $this->hasMany(Qr::class);
    }

    /**
     * Relación: Un usuario puede tener un dispositivo (device)
     */
    public function device()
    {
        return $this->hasOne(Device::class);
    }
}
