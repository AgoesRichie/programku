<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

// Route untuk halaman input data menu
Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
// Route untuk halaman input data menu ke db
Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');

// Route untuk halaman daftar menu
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Route untuk hapus daftar menu
Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

Route::get('/', function () {
    return view('welcome');
});


