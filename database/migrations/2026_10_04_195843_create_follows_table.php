// database/migrations/xxxx_create_follows_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['user_id', 'artist_id']); // មិនអាច Follow ដដែល
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};