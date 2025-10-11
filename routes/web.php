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

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HealthCheckController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication choice page
Route::get('/auth', function () {
    return view('auth.auth');
})->name('auth')->middleware('guest');

// Custom authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Article routes
Route::resource('articles', ArticleController::class);


// Tag routes
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');

// User profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
});

// Public user profiles
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// Contact routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// API routes for tags
Route::prefix('api')->group(function () {
    Route::get('/tags/search', [TagController::class, 'search'])->name('api.tags.search');
    Route::post('/tags', [TagController::class, 'store'])->name('api.tags.store');
});

// SEO routes
Route::get('/sitemap.xml', function () {
    return response()->file(public_path('sitemap.xml'));
})->name('sitemap');

Route::get('/robots.txt', function () {
    return response()->file(public_path('robots.txt'));
})->name('robots');

// Health check routes
Route::get('/health', [HealthCheckController::class, 'index'])->name('health');
Route::get('/health/detailed', [HealthCheckController::class, 'detailed'])->name('health.detailed');
