<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlShortenerController;

// Group all routes under the API middleware and v1 prefix
Route::middleware('api')
->prefix('v1')
->group(function () {
    Route::post('/encode', [UrlShortenerController::class, 'encode']);
    Route::post('/decode', [UrlShortenerController::class, 'decode']);
    
});

Route::get('/test', function () {
    return response()->json(['message' => 'API route working']);
});