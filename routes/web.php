<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/deploy', function (\Illuminate\Http\Request $request) {
    if ($request->header('Authorization') !== 'Bearer ' . env('DEPLOY_TOKEN')) {
        abort(401, 'Unauthorized');
    }

    // Run migrations and clear caches securely
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    return response()->json([
        'status' => 'Deploy successful',
        'migrations' => \Illuminate\Support\Facades\Artisan::output()
    ]);
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
