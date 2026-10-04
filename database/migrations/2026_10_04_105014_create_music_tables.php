<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('user');   // user | admin
            $t->boolean('is_active')->default(true);
            $t->string('avatar_url')->nullable();
        });
        Schema::create('artists', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->text('bio')->nullable();
            $t->string('image_url')->nullable();
            $t->timestamps();
        });
        Schema::create('genres', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });
        Schema::create('albums', function (Blueprint $t) {
            $t->id();
            $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('cover_url')->nullable();
            $t->date('release_date')->nullable();
            $t->timestamps();
        });
        Schema::create('songs', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $t->foreignId('album_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('genre_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('file_url');
            $t->string('cover_url')->nullable();
            $t->unsignedInteger('duration')->nullable();
            $t->unsignedInteger('play_count')->default(0);
            $t->timestamps();
        });
        Schema::create('playlists', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->boolean('is_public')->default(false);
            $t->timestamps();
        });
        Schema::create('playlist_song', function (Blueprint $t) {
            $t->id();
            $t->foreignId('playlist_id')->constrained()->cascadeOnDelete();
            $t->foreignId('song_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
            $t->unique(['playlist_id', 'song_id']);
        });
        Schema::create('favorites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('song_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['user_id', 'song_id']);
        });
    }

    public function down(): void
    {
        foreach (['favorites', 'playlist_song', 'playlists', 'songs', 'albums', 'genres', 'artists'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn(Blueprint $t) => $t->dropColumn(['role', 'is_active', 'avatar_url']));
    }
};
