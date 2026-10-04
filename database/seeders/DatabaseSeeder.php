<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('role', 'admin')->exists()) {
            User::forceCreate([
                'name' => 'Admin',
                'email' => 'admin@imusic.com',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]);
        }
    }
}