<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    public function update(Request $r)
    {
        $user = $r->user();

        $d = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        // ✅ Update Password
        if (!empty($d['password'])) {
            if (!Hash::check($d['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Password បច្ចុប្បន្នមិនត្រឹមត្រូវ',
                ], 422);
            }
            $user->password = Hash::make($d['password']);
        }

        // ✅ Update Avatar ទៅ Cloudinary
        if ($r->hasFile('avatar')) {
            $uploaded = Cloudinary::upload(
                $r->file('avatar')->getRealPath(),
                ['folder' => 'imusic/avatars']
            );
            $user->avatar_url = $uploaded->getSecurePath();
        }

        // ✅ Update Fields
        $user->name = $d['name'];
        $user->email = $d['email'];
        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->name,
            'role' => $user->role,
            'avatar_url' => $user->avatar_url,
        ]);
    }
}