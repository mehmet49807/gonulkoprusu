<?php

use App\Http\Controllers\Web\AuthPageController;
use App\Http\Controllers\Web\FeedPageController;
use App\Http\Controllers\Web\PostPageController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LegalPageController;
use App\Http\Controllers\Web\LocationUsersPageController;
use App\Http\Controllers\Web\MessagePageController;
use App\Http\Controllers\Web\NotificationPageController;
use App\Http\Controllers\Web\PremiumPageController;
use App\Http\Controllers\Web\ProfilePageController;
use App\Http\Controllers\Web\StoryPageController;
use App\Http\Controllers\Web\UserProfilePageController;
use Illuminate\Support\Facades\Route;

// Public landing
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gizlilik-politikasi', [LegalPageController::class, 'privacy'])->name('privacy');
Route::get('/kullanim-kosullari', [LegalPageController::class, 'terms'])->name('terms');
Route::get('/sikayet-ve-engelleme', [LegalPageController::class, 'complaints'])->name('complaints');
Route::get('/guvenli-tanisma', [LegalPageController::class, 'safeMeeting'])->name('safe-meeting');
Route::get('/hakkimizda', [LegalPageController::class, 'about'])->name('about');

// Auth pages
Route::get('/register', [AuthPageController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthPageController::class, 'register']);
Route::get('/login', [AuthPageController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthPageController::class, 'login']);
Route::post('/logout', [AuthPageController::class, 'logout'])->name('logout');

// Authenticated web pages
Route::middleware(['auth', 'last.active'])->group(function () {
    Route::get('/feed', [FeedPageController::class, 'index'])->name('feed');
    Route::post('/posts', [PostPageController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostPageController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [PostPageController::class, 'toggleLike'])->name('posts.like');
    Route::post('/stories', [StoryPageController::class, 'store'])->name('stories.store');
    Route::delete('/stories/{story}', [StoryPageController::class, 'destroy'])->name('stories.destroy');
    Route::get('/profile', [ProfilePageController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfilePageController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfilePageController::class, 'uploadPhoto'])->name('profile.photo');
    Route::get('/users/{username}', [UserProfilePageController::class, 'show'])->name('users.show');
    Route::post('/users/{username}/report', [UserProfilePageController::class, 'report'])->name('users.report');
    Route::get('/locations/{country}/{city}/{district?}', [LocationUsersPageController::class, 'index'])
        ->name('locations.users')
        ->where(['country' => '[^/]+', 'city' => '[^/]+', 'district' => '[^/]*']);
    Route::get('/premium', [PremiumPageController::class, 'index'])->name('premium');
    Route::get('/notifications', [NotificationPageController::class, 'index'])->name('notifications.index');
    Route::get('/messages', [MessagePageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{username}', [MessagePageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{username}', [MessagePageController::class, 'store'])->name('messages.store');
});

// Admin panel — adminlogin.gonulkoprusu.com alt alan adinda
Route::any('/admin/{path?}', function (?string $path = null) {
    $base = rtrim(config('app.admin_url'), '/');

    if (!$path) {
        return redirect()->away($base);
    }

    return redirect()->away($base.'/'.ltrim($path, '/'));
})->where('path', '.*');
