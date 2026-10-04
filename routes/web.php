<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ✅ Storage Proxy — ត្រូវដាក់នៅខាងលើ!
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404, 'File not found: ' . $path);
    }
    
    return response()->file($filePath);
})->where('path', '.*');

// ✅ Root Route
Route::get('/', function () {
    return response()->json([
        'name' => 'iMusic API',
        'status' => 'running',
    ]);
});

// ✅ Create Storage Symlink
Route::get('/create-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// ✅ Clear Cache
Route::get('/clear-all-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'All caches cleared!';
});

// ✅ Debug Info
Route::get('/debug-info', function () {
    return response()->json([
        'storage_link_exists' => file_exists(public_path('storage')),
        'covers_exists' => file_exists(storage_path('app/public/covers')),
        'artists_exists' => file_exists(storage_path('app/public/artists')),
        'songs_exists' => file_exists(storage_path('app/public/songs')),
    ]);
});