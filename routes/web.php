<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class,'index'])->middleware(['auth', 'verified'])->name('home');

Route::get('/profile/{user:username}', [ProfileController::class,'show'])->name('profile.show');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // route to update and save image (avatar or cover )
    Route::post('/profile/{user}/image', [ProfileController::class, 'saveImage'])->name('profile.image.store');

});

require __DIR__.'/auth.php';
