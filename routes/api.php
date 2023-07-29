<?php

use App\Http\Controllers\API\MobilController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/register', [UserController::class, 'register'])->name('register');

Route::get('/mobil', [MobilController::class, 'index'])->name('mobil.index');
Route::get('/mobil/{id}', [MobilController::class, 'show'])->name('mobil.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mobilku', [MobilController::class, 'getByUser'])->name('mobilku');
    Route::post('/mobil', [MobilController::class, 'store'])->name('mobil.store');
    Route::patch('/mobil/{id}', [MobilController::class, 'update'])->name('mobil.update');
    Route::delete('/mobil/{id}', [MobilController::class, 'destroy'])->name('mobil.destroy');

    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::patch('/peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian', [PengembalianController::class, 'store'])->name('pengembalian.store');
    Route::get('/pengembalian/{id}', [PengembalianController::class, 'show'])->name('pengembalian.show');
    Route::patch('/pengembalian/{id}', [PengembalianController::class, 'update'])->name('pengembalian.update');
    Route::delete('/pengembalian/{id}', [PengembalianController::class, 'destroy'])->name('pengembalian.destroy');
});
