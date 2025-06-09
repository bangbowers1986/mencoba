<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman welcome (opsional)
Route::get('/', function () {
    return view('welcome');
});

// Jika butuh halaman web lainnya
Route::get('/dashboard', function () {
    return 'Dashboard Admin';
})->middleware('auth');
