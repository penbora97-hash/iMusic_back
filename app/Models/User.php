<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // មិនដាក់ role ក្នុង fillable ដើម្បីកុំឱ្យ user ដាក់ខ្លួនជា admin បាន
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'is_active' => 'boolean'];

    public function favorites()
    {
        return $this->belongsToMany(Song::class, 'favorites')->withTimestamps();
    }
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }
    public function following()
{
    return $this->belongsToMany(Artist::class, 'follows')
        ->withTimestamps();
}
}
    