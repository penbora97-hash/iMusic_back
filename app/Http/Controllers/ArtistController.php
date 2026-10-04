<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtistController extends Controller
{
    // ===== Admin Methods (ត្រូវការ Login) =====

    public function index()
    {
        return Artist::withCount('songs')->latest()->get();
    }

    public function show(Artist $artist)
    {
        return $artist->load(['songs' => function ($q) {
            $q->with('genre')->latest();
        }]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255|unique:artists,name',
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $imageUrl = null;
        if ($r->hasFile('image')) {
            $imageUrl = '/storage/' . $r->file('image')->store('artists', 'public');
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

        if ($r->hasFile('image')) {
            if ($artist->image_url) {
                Storage::disk('public')->delete(Str::after($artist->image_url, '/storage/'));
            }
            $d['image_url'] = '/storage/' . $r->file('image')->store('artists', 'public');
        }

        $artist->update($d);
        return $artist->loadCount('songs');
    }

    public function destroy(Artist $artist)
    {
        if ($artist->image_url) {
            Storage::disk('public')->delete(Str::after($artist->image_url, '/storage/'));
        }
        $artist->delete();
        return ['ok' => true];
    }

    // ===== Public Methods (មិនត្រូវការ Login) =====

    /**
     * Public: List Artists ទាំងអស់ដែលមានចម្រៀង
     */
    public function publicList()
    {
        return Artist::withCount('songs')
            ->orderByDesc('songs_count')
            ->orderBy('name')
            ->get();
    }

    public function publicShow(Artist $artist)
    {
        return $artist->load(['songs' => function ($q) {
            $q->with('genre')->latest();
        }])->loadCount('songs');
    }
}
