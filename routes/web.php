<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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
        'storage' => 'Cloudinary',
    ]);
});

// ✅ Debug Info (Cloudinary)
Route::get('/debug-info', function () {
    return response()->json([
        'storage' => 'Cloudinary',
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY') ? 'Set ✅' : 'Missing ❌',
        'api_secret' => env('CLOUDINARY_API_SECRET') ? 'Set ✅' : 'Missing ❌',
        'php_version' => phpversion(),
        'laravel_version' => app()->version(),
    ]);
});

// ✅ Clear Cache
Route::get('/clear-all-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'All caches cleared!';
});
