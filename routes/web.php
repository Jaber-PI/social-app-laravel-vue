<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

Route::get('/profile/{user:username}', [ProfileController::class, 'show'])->name('profile.show');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('/posts')->controller(PostController::class)->name('posts.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/latest', 'latest')->name('latest');
        Route::get('/{post}', 'show')->name('show');
        Route::post('', 'store')->name('store');
        Route::put('/{post}', 'update')->name('update');
        Route::delete('/{post}', 'destroy')->name('delete');
    });

    Route::post('/posts/{post}/reaction', [PostController::class, 'react']);

    // group routes
    Route::prefix('/groups')->controller(GroupController::class)->name('groups.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/{group:slug}', 'show')->name('show');
        Route::post('', 'store')->name('store');
        Route::put('/{group}', 'update')->name('update');
        Route::delete('/{group}', 'destroy')->name('delete');
        Route::post('/{group}/join', 'join')->name('join');
        Route::post('/{group}/leave', 'leave')->name('leave');

        Route::post('/{group}/image', 'saveImage')->name('image.store');

        Route::get('/{group}/posts', 'posts')->name('posts');

    });

    // comments routes
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store')
        ->middleware('can:create,App\Models\Comment');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.delete')
        ->middleware('can:delete,comment');

    Route::put('/comments/{comment}', [CommentController::class, 'update'])
        ->name('comments.update')
        ->middleware('can:update,comment');

    Route::post('/comments/{comment}/reaction', [CommentController::class, 'react'])
        ->name('comments.react');


    Route::get('/attachments/{attachment}/download', [PostController::class, 'downloadAttachment'])
        ->name('attachments.download');

    Route::prefix('/profile')->controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('', 'edit')->name('edit');
        Route::patch('', 'update')->name('update');
        Route::delete('', 'destroy')->name('delete');
        // route to update and save image (avatar or cover )
        Route::post('/{user}/image', 'saveImage')->name('image.store');
    });
});

require __DIR__ . '/auth.php';
