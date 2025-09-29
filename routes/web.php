<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController; // ←ファイルの先頭にこの行があるかも確認
use App\Http\Controllers\CartController; // ★★★ この行があるか確認！ ★★★
use App\Http\Controllers\OrderController; // ★★★ この行があるか確認！ ★★★
use App\Http\Controllers\CafeteriaController; // ★忘れずに追加

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [MenuController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/menu/{menu}', [MenuController::class, 'show'])->name('menu.show');
    Route::post('/cart/add/{menu}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/complete/{order}', [OrderController::class, 'complete'])->name('order.complete');
    Route::get('/cafeteria/select', [CafeteriaController::class, 'select'])->name('cafeteria.select');
    Route::post('/cafeteria/select', [CafeteriaController::class, 'store'])->name('cafeteria.store');
    //Route::get('/order/complete', [OrderController::class, 'complete'])->name('order.complete');
    Route::get('/order/history', [OrderController::class, 'history'])->name('order.history');
});

require __DIR__.'/auth.php';
