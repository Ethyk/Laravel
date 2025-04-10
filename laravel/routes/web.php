<?php

use App\Http\Controllers\SalonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TatoueurController;

 

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::resource('salons', SalonController::class)->middleware(['auth:web']);
Route::post('/salons/{id}/restore', [SalonController::class, 'restore'])->name('salons.restore');
Route::delete('/salons/{id}/force-delete', [SalonController::class, 'forceDelete'])->name('salons.forceDelete');

Route::resource('tatoueurs', TatoueurController::class);
// Routes supplémentaires si besoin
Route::get('tatoueurs/{tatoueur}/flashs', [TatoueurController::class, 'getFlashs']);


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
