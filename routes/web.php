<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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

        Route::post('/{group}/users/request', 'requestToJoin')->name('request');
        Route::delete('/{group}/users/request', 'cancelRequest')->name('cancelRequest');
        Route::delete('/{group}/users/leave', 'leave')->name('leave');

        Route::put('/{group}/users/remove', 'removeMember')->name('remove-member');

        Route::post('/{group}/users/{request}/approve', 'approveRequest')->name('approve');
        Route::post('/{group}/users/{request}/reject', 'rejectRequest')->name('reject');

        Route::post('/{group}/users/invite', 'inviteMember')->name('invite');
        Route::get('/{group}/users/invitations/{token}', 'acceptInvitation')->name('accept-invitation');

        Route::put('/{group}/change-role', 'changeRole')->name('changeRole');

        Route::post('/{group}/image', 'saveImage')->name('image.store');

        Route::get('/{group}/posts', 'posts')->name('posts');
    });

    // comments routes
    Route::prefix('/comments')->controller(CommentController::class)->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::post('/', [CommentController::class, 'store'])->name('store')
            ->middleware('can:create,App\Models\Comment');

        Route::delete('/{comment}', [CommentController::class, 'destroy'])
            ->name('delete')
            ->middleware('can:delete,comment');

        Route::put('/{comment}', [CommentController::class, 'update'])
            ->name('update')
            ->middleware('can:update,comment');

        Route::post('/{comment}/reaction', [CommentController::class, 'react'])
            ->name('react');
    });


    Route::get('/attachments/{attachment}/download', [PostController::class, 'downloadAttachment'])
        ->name('attachments.download');

    Route::prefix('/profile')->controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('', 'edit')->name('edit');
        Route::patch('', 'update')->name('update');
        Route::delete('', 'destroy')->name('delete');

        Route::get('/{user}/posts', 'posts')->name('posts');
        Route::get('/{user}/followers', 'followers')->name('followers');
        Route::get('/{user}/followings', 'followings')->name('followings');

        // route to update and save image (avatar or cover )
        Route::post('/{user}/image', 'saveImage')->name('image.store');
    });

    Route::post('/follow/{user}', [UserController::class, 'follow'])->name('users.follow');
    Route::delete('/unfollow/{user}', [UserController::class, 'unfollow'])->name('users.unfollow');
});

require __DIR__ . '/auth.php';
