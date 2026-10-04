<?php

namespace App\Http\Controllers;

use App\Models\{Song, Artist, User};
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard Stats
     */
    public function stats()
    {
        $songs = Song::count();
        $artists = Artist::count();
        $users = User::count();
        $plays = Song::sum('play_count') ?? 0;

        // Signups 14 ថ្ងៃចុងក្រោយ
        $signups = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($row) => [
                'date' => $row->date,
                'count' => (int) $row->count,
            ]);

        // Top 5 Songs ពេញនិយម
        $top_songs = Song::with('artist')
            ->orderByDesc('play_count')
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'play_count' => $s->play_count ?? 0,
                'artist' => $s->artist ? [
                    'id' => $s->artist->id,
                    'name' => $s->artist->name,
                ] : null,
            ]);

        // ✅ 5 Users ថ្មីៗ (ប្រើ `name`)
        $recent_users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'songs' => $songs,
            'artists' => $artists,
            'users' => $users,
            'plays' => (int) $plays,
            'signups' => $signups,
            'top_songs' => $top_songs,
            'recent_users' => $recent_users,
        ]);
    }

    /**
     * ✅ List Users ទាំងអស់ (ប្រើ `name`)
     */
    public function users()
    {
        return User::select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->latest()
            ->get();
    }
}
