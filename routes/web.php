<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefundController;
use Illuminate\Support\Facades\Route;
use Onecentlin\Adminer\Http\Controllers\AdminerController;

// Redirect root URL to /refund
Route::redirect('/', '/refunds');



// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::resource('products', controller: ProductController::class);

Route::any('adminer', 'Onecentlin\Adminer\Http\Controllers\AdminerController@index')->middleware(['auth', 'verified']);

Route::resource('refunds', RefundController::class)
    ->only(['index', 'store', 'show', 'edit', 'update'])
    ->middleware(['auth', 'verified']);

require __DIR__ . '/auth.php';
