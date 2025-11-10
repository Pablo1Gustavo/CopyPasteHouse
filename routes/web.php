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
use App\Http\Controllers\Web\TagController;
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
    Route::get('/comments/{id}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    
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
    
    // My Tags (user's own tags)
    Route::get('/my-tags', [TagController::class, 'myTags'])->name('tags.my');
    
    // Tags management
    Route::get('/admin/tags', [TagController::class, 'index'])->name('tags.index')->middleware('admin'); // Admin: view all tags
    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create'); // Anyone: create tag
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store'); // Anyone: store tag
    Route::get('/admin/tags/{tag}', [TagController::class, 'show'])->name('tags.show')->middleware('admin'); // Admin: view tag details
    Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit'); // Owner/Admin: edit (checked in controller)
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update'); // Owner/Admin: update (checked in controller)
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy'); // Owner/Admin: delete (checked in controller)
});

// Public tag viewing (outside auth group)
Route::get('/tags/browse', [TagController::class, 'publicIndex'])->name('tags.public');
Route::get('/tags/{slug}', [TagController::class, 'publicShow'])->name('tags.public.show');

// Public paste viewing (no auth required)
Route::get('/archive', [PasteController::class, 'archive'])->name('pastes.archive');
Route::get('/pastes/{id}', [PasteController::class, 'show'])->name('pastes.show');
Route::get('/pastes/{id}/raw', [PasteController::class, 'raw'])->name('pastes.raw');
