<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PasteController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\SyntaxHighlightController;
use App\Http\Controllers\Web\ExpirationTimeController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\StatisticsController;
use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

// Public homepage with popular/trending pastes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public paste creation page
Route::get('/create', [PasteController::class, 'create'])->name('pastes.create');

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
    
    // Comments
    Route::post('/pastes/{paste}/comments', [PasteController::class, 'storeComment'])->name('pastes.comments.store');
    Route::post('/comments/{comment}/like', [PasteController::class, 'toggleCommentLike'])->name('comments.like');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
    
    // Admin routes (requires is_admin = true)
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // Statistics dashboard
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
        
        // User management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        
        // Comment management
        Route::get('/comments', [CommentController::class, 'index'])->name('comments');
        Route::get('/comments/{id}', [CommentController::class, 'show'])->name('comments.show');
        Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });
    
    // Syntax Highlights management (admin only)
    Route::resource('syntax-highlights', SyntaxHighlightController::class)->except(['show'])->middleware('admin');
    
    // Expiration Times management (admin only)
    Route::resource('expiration-times', ExpirationTimeController::class)->except(['show'])->middleware('admin');
});

// Public paste viewing (no auth required)
Route::get('/archive', [PasteController::class, 'archive'])->name('pastes.archive');
Route::get('/pastes/{id}', [PasteController::class, 'show'])->name('pastes.show');
Route::get('/pastes/{id}/raw', [PasteController::class, 'raw'])->name('pastes.raw');
