<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    // ✅ Toggle Follow/Unfollow
    public function toggle(Request $r, Artist $artist)
    {
        $user = $r->user();
        $result = $user->following()->toggle($artist->id);

        return [
            'following' => count($result['attached']) > 0,
            'followers_count' => $artist->followers()->count(),
        ];
    }

    // ✅ Check Follow Status
    public function check(Request $r, Artist $artist)
    {
        return [
            'following' => $r->user()
                ? $r->user()->following()->where('artist_id', $artist->id)->exists()
                : false,
            'followers_count' => $artist->followers()->count(),
        ];
    }

    // ✅ List Followed Artists
    public function list(Request $r)
    {
        return $r->user()->following()->withCount('songs')->get();
    }
}
