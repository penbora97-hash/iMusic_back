<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $guarded = [];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    // ✅ បន្ថែម Relation Followers
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows')
            ->withTimestamps();
    }
}
