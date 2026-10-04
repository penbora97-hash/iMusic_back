<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $r)
    {
        $u = $r->user();
        $d = $r->validate([
            'name' => 'required|string|max:50',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($u->id)],
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3072',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        // ប្តូរ password (ត្រូវបញ្ជាក់ password ចាស់)
        if (!empty($d['password'])) {
            if (!Hash::check($d['current_password'] ?? '', $u->password)) {
                return response()->json(['message' => 'Password បច្ចុប្បន្នមិនត្រឹមត្រូវ'], 422);
            }
            $u->password = Hash::make($d['password']);
            // ចេញពី session ផ្សេងៗ ទុកតែ session នេះ
            $u->tokens()->where('id', '!=', $u->currentAccessToken()->id)->delete();
        }

        // រូប Profile: លុបរូបចាស់ រួចរក្សាទុករូបថ្មី
        if ($r->hasFile('avatar')) {
            if ($u->avatar_url) {
                Storage::disk('public')->delete(Str::after($u->avatar_url, '/storage/'));
            }
            $u->avatar_url = '/storage/' . $r->file('avatar')->store('avatars', 'public');
        }

        $u->name = $d['name'];
        $u->email = $d['email'];
        $u->save();

        return ['id' => $u->id, 'username' => $u->name, 'email' => $u->email, 'role' => $u->role, 'avatar_url' => $u->avatar_url];
    }
}
