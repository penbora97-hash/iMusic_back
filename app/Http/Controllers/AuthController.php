<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function pub(User $u): array
    {
        return ['id' => $u->id, 'username' => $u->name, 'email' => $u->email, 'role' => $u->role, 'avatar_url' => $u->avatar_url];
    }

    public function register(Request $r)
    {
        $d = $r->validate([
            'username' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $u = User::create([
            'name' => $d['username'],
            'email' => $d['email'],
            'password' => Hash::make($d['password']),
        ]);
        return response()->json(['token' => $u->createToken('app')->plainTextToken, 'user' => $this->pub($u->refresh())], 201);
    }

    public function login(Request $r)
    {
        $u = User::where('email', (string) $r->input('email'))->first();
        if (!$u || !$u->is_active || !Hash::check((string) $r->input('password'), $u->password)) {
            return response()->json(['message' => 'Email ឬ Password ខុស'], 401);
        }
        return response()->json(['token' => $u->createToken('app')->plainTextToken, 'user' => $this->pub($u)]);
    }

    public function me(Request $r) { return $this->pub($r->user()); }
}