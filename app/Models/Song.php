<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $guarded = [];
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
