<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index(Request $r)
    {
        return $r->user()->playlists()
            ->withCount('songs')
            ->latest()
            ->get();
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $playlist = $r->user()->playlists()->create([
            'name' => trim($d['name']),
            'is_public' => $d['is_public'] ?? false,
        ]);

        return response()->json($playlist->loadCount('songs'), 201);
    }

    public function show(Request $r, Playlist $playlist)
    {
        // ឆែកថា User ជាម្ចាស់ ឬ Playlist ជា Public
        if ($playlist->user_id !== $r->user()->id && !$playlist->is_public) {
            abort(403);
        }

        return $playlist->load('songs.artist')->loadCount('songs');
    }

    public function update(Request $r, Playlist $playlist)
    {
        if ($playlist->user_id !== $r->user()->id) abort(403);

        $d = $r->validate([
            'name' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $playlist->update($d);
        return $playlist->loadCount('songs');
    }

    public function destroy(Request $r, Playlist $playlist)
    {
        if ($playlist->user_id !== $r->user()->id) abort(403);
        $playlist->delete();
        return ['ok' => true];
    }

    public function addSong(Request $r, Playlist $playlist)
    {
        if ($playlist->user_id !== $r->user()->id) abort(403);

        $d = $r->validate(['song_id' => 'required|exists:songs,id']);
        $playlist->songs()->syncWithoutDetaching([$d['song_id']]);
        return ['ok' => true];
    }

    public function removeSong(Request $r, Playlist $playlist, $songId)
    {
        if ($playlist->user_id !== $r->user()->id) abort(403);
        $playlist->songs()->detach($songId);
        return ['ok' => true];
    }
}