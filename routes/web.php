<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/prices', [PriceController::class, 'index'])->name('prices');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

Route::get('/articles', [PostController::class, 'index'])->name('posts.index');
Route::get('/articles/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Admin login is provided by Filament at /admin/login
