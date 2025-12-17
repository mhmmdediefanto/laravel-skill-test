<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// --- Route Private (Wajib Login) ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update')->middleware('can:update,post');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update.patch')->middleware('can:update,post');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('can:update,post');
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
