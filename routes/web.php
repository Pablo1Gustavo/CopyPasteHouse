<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PasteController;
use Illuminate\Support\Facades\Route;

// Public landing page allows anyone to create a paste
Route::get('/', [PasteController::class, 'create'])->name('pastes.create');

// Allow guests to submit new pastes
Route::post('/pastes', [PasteController::class, 'store'])->name('pastes.store');

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Convenience alias for logged-in users to access the creator
    Route::get('/dashboard', [PasteController::class, 'create'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Paste management
    Route::get('/pastes', [PasteController::class, 'index'])->name('pastes.index');
    Route::get('/pastes/{paste}/edit', [PasteController::class, 'edit'])->name('pastes.edit');
    Route::put('/pastes/{paste}', [PasteController::class, 'update'])->name('pastes.update');
    Route::delete('/pastes/{paste}', [PasteController::class, 'destroy'])->name('pastes.destroy');
    Route::post('/pastes/{paste}/like', [PasteController::class, 'toggleLike'])->name('pastes.like');
});

// Public paste viewing (no auth required)
Route::get('/pastes/{id}', [PasteController::class, 'show'])->name('pastes.show');
