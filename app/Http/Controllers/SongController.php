<?php

namespace App\Http\Controllers;

use App\Models\{Artist, Genre, Song};
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SongController extends Controller
{
    public function index(Request $r)
    {
        $q = Song::with(['artist', 'genre'])->latest();
        if ($s = $r->query('q')) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%' . mb_strtolower($s) . '%']);
        }
        return $q->get();
    }

    public function play($id)
    {
        Song::whereKey($id)->increment('play_count');
        return ['ok' => true];
    }

    public function favorites(Request $r)
    {
        return $r->user()->favorites()->with(['artist', 'genre'])->get();
    }

    public function toggleFavorite(Request $r, Song $song)
    {
        $res = $r->user()->favorites()->toggle($song->id);
        return ['liked' => count($res['attached']) > 0];
    }

    // ===== Admin ប៉ុណ្ណោះ =====
    public function store(Request $r)
    {
        $d = $r->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'audio' => 'required|file|mimes:mp3,mpga,mp4,wav|max:30720',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'artist_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $artist = Artist::firstOrCreate(['name' => trim($d['artist'])]);

        // ✅ Save Artist Image ទៅ Cloudinary
        if ($r->hasFile('artist_image')) {
            $uploaded = Cloudinary::upload(
                $r->file('artist_image')->getRealPath(),
                ['folder' => 'imusic/artists']
            );
            $artist->update([
                'image_url' => $uploaded->getSecurePath(),
            ]);
        }

        $genre = !empty($d['genre'])
            ? Genre::firstOrCreate(['name' => trim($d['genre'])])
            : null;

        // ✅ Save Cover ទៅ Cloudinary
        $coverUrl = null;
        if ($r->hasFile('cover')) {
            $uploaded = Cloudinary::upload(
                $r->file('cover')->getRealPath(),
                ['folder' => 'imusic/covers']
            );
            $coverUrl = $uploaded->getSecurePath();
        }

        // ✅ Save Audio ទៅ Cloudinary (resource_type video)
        $audioUrl = null;
        if ($r->hasFile('audio')) {
            $uploaded = Cloudinary::uploadVideo(
                $r->file('audio')->getRealPath(),
                ['folder' => 'imusic/songs']
            );
            $audioUrl = $uploaded->getSecurePath();
        }

        $song = Song::create([
            'title' => $d['title'],
            'artist_id' => $artist->id,
            'genre_id' => $genre?->id,
            'uploaded_by' => $r->user()->id,
            'file_url' => $audioUrl,
            'cover_url' => $coverUrl,
        ]);

        return response()->json($song->load(['artist', 'genre']), 201);
    }

    public function update(Request $r, Song $song)
    {
        $d = $r->validate([
            'title' => 'required|string|max:255',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        unset($d['cover']);

        // ✅ Update Cover ទៅ Cloudinary
        if ($r->hasFile('cover')) {
            $uploaded = Cloudinary::upload(
                $r->file('cover')->getRealPath(),
                ['folder' => 'imusic/covers']
            );
            $d['cover_url'] = $uploaded->getSecurePath();
        }

        $song->update($d);
        return $song->load(['artist', 'genre']);
    }

    public function destroy(Song $song)
    {
        $song->delete();
        return ['ok' => true];
    }

    public function top()
    {
        $top_songs = Song::with(['artist', 'genre'])
            ->orderByDesc('play_count')
            ->limit(10)
            ->get();

        $top_artists = Artist::withCount('songs')
            ->withSum('songs', 'play_count')
            ->orderByDesc('songs_sum_play_count')
            ->limit(10)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'image_url' => $a->image_url,
                'songs_count' => $a->songs_count,
                'total_plays' => (int) ($a->songs_sum_play_count ?? 0),
            ]);

        return response()->json([
            'top_songs' => $top_songs,
            'top_artists' => $top_artists,
        ]);
    }
}