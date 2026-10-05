<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ArtistController extends Controller
{
    public function index()
    {
        return Artist::withCount('songs')->latest()->get();
    }

    public function show(Artist $artist)
    {
        return $artist->load(['songs' => function ($q) {
            $q->with('genre')->latest();
        }])->loadCount('songs');
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255|unique:artists,name',
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        unset($d['image']);

        // ✅ Save Image ទៅ Cloudinary
        $imageUrl = null;
        if ($r->hasFile('image')) {
            $uploaded = Cloudinary::upload(
                $r->file('image')->getRealPath(),
                ['folder' => 'imusic/artists']
            );
            $imageUrl = $uploaded->getSecurePath();
        }

        $artist = Artist::create([
            'name' => trim($d['name']),
            'bio' => $d['bio'] ?? null,
            'image_url' => $imageUrl,
        ]);

        return response()->json($artist, 201);
    }

    public function update(Request $r, Artist $artist)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255|unique:artists,name,' . $artist->id,
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        unset($d['image']);

        // ✅ Update Image ទៅ Cloudinary
        if ($r->hasFile('image')) {
            $uploaded = Cloudinary::upload(
                $r->file('image')->getRealPath(),
                ['folder' => 'imusic/artists']
            );
            $d['image_url'] = $uploaded->getSecurePath();
        }

        $artist->update($d);
        return $artist->loadCount('songs');
    }

    public function destroy(Artist $artist)
    {
        $artist->delete();
        return ['ok' => true];
    }

    // ===== Public Methods =====
    public function publicList()
    {
        return Artist::withCount('songs')
            ->orderByDesc('songs_count')
            ->get();
    }

    public function publicShow(Artist $artist)
    {
        return $artist->load(['songs' => function ($q) {
            $q->with('genre')->latest();
        }])->loadCount('songs');
    }
}