<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $guarded = [];
    public function artist() { return $this->belongsTo(Artist::class); }
    public function songs() { return $this->hasMany(Song::class); }
}