<?php

use App\Http\Controllers\{AdminController, ArtistController, AuthController, FollowController, PlaylistController, ProfileController, SongController};
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/songs', [SongController::class, 'index']);
Route::get('/songs/top', [SongController::class, 'top']); 
Route::post('/songs/{id}/play', [SongController::class, 'play']);
Route::get('/artists', [ArtistController::class, 'publicList']);
Route::get('/artists/{artist}', [ArtistController::class, 'publicShow']);
Route::get('/artists/{artist}/follow', [FollowController::class, 'check']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/favorites', [SongController::class, 'favorites']);
    Route::post('/favorites/{song}', [SongController::class, 'toggleFavorite']);
    Route::get('/admin/artists', [ArtistController::class, 'index']);
    Route::post('/admin/artists', [ArtistController::class, 'store']);
    Route::get('/admin/artists/{artist}', [ArtistController::class, 'show']);
    Route::post('/admin/artists/{artist}', [ArtistController::class, 'update']);
    Route::delete('/admin/artists/{artist}', [ArtistController::class, 'destroy']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::get('/playlists', [PlaylistController::class, 'index']);
    Route::post('/playlists', [PlaylistController::class, 'store']);
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show']);
    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update']);
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy']);
    Route::post('/playlists/{playlist}/songs', [PlaylistController::class, 'addSong']);
    Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong']);
    Route::post('/artists/{artist}/follow', [FollowController::class, 'toggle']);
    Route::get('/following', [FollowController::class, 'list']);
    Route::prefix('admin')->middleware(AdminOnly::class)->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser']);    
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::post('/songs', [SongController::class, 'store']);
        Route::put('/songs/{song}', [SongController::class, 'update']);
        Route::delete('/songs/{song}', [SongController::class, 'destroy']);
    });
});
