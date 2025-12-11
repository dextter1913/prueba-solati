<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('docs');
})->name('docs');

Route::get('/openapi.yaml', function () {
    $path = base_path('openapi.yaml');

    abort_unless(file_exists($path), 404);

    return response()->make(
        file_get_contents($path),
        200,
        ['Content-Type' => 'application/yaml; charset=utf-8']
    );
})->name('openapi');
