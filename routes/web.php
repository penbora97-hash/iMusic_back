<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

// ✅ Debug Info — ឆែក Storage Files
Route::get('/debug-info', function () {
    $publicPath = public_path();
    $storagePath = storage_path();
    $coversPath = storage_path('app/public/covers');
    $songsPath = storage_path('app/public/songs');
    
    return response()->json([
        'public_path' => $publicPath,
        'storage_path' => $storagePath,
        'storage_link_exists' => file_exists($publicPath . '/storage'),
        'storage_app_public_exists' => file_exists($storagePath . '/app/public'),
        'covers_exists' => file_exists($coversPath),
        'songs_exists' => file_exists($songsPath),
        'covers_files' => file_exists($coversPath) ? scandir($coversPath) : [],
        'songs_files' => file_exists($songsPath) ? scandir($songsPath) : [],
        'php_version' => phpversion(),
        'laravel_version' => app()->version(),
    ]);
});

// ✅ Test Image Route
Route::get('/test-image', function () {
    $path = storage_path('app/public/covers');
    if (file_exists($path)) {
        $files = array_diff(scandir($path), ['.', '..']);
        return response()->json([
            'covers_folder' => $path,
            'files_count' => count($files),
            'files' => array_values($files),
        ]);
    }
    return response()->json(['error' => 'Covers folder not found']);
});