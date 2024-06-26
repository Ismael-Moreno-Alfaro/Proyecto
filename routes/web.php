<?php

use App\Http\Controllers\ActorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WatchlistMovieController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('role:user')->group(function () {
Route::get('/movies/{id}', [MovieController::class, 'show'])->middleware(['auth', 'verified'])->name('movies.show');
Route::get('/movies', [MovieController::class, 'index'])->middleware(['auth', 'verified'])->name('movies.index');
Route::post('/movies', [MovieController::class, 'store'])->middleware(['auth', 'verified'])->name('movies.store');

Route::get('/actors/{id}', [ActorController::class, 'show'])->middleware(['auth', 'verified'])->name('actors.show');

Route::get('/watchlist-movies', [WatchlistMovieController::class, 'index'])->middleware(['auth', 'verified'])->name('watchlist-movies.index');
Route::get('/watchlist-movies/pending', [WatchlistMovieController::class, 'getPending'])->middleware(['auth', 'verified'])->name('watchlist-movies.pending');
Route::get('/watchlist-movies/completed', [WatchlistMovieController::class, 'getCompleted'])->middleware(['auth', 'verified'])->name('watchlist-movies.completed');
Route::patch('/watchlist-movies/update-status', [WatchlistMovieController::class, 'updateStatus'])->name('watchlist-movies.updateStatus');
Route::post('/watchlist-movies', [WatchlistMovieController::class, 'store'])->middleware(['auth', 'verified'])->name('watchlist-movies.store');
Route::delete('/watchlist-movies/{id}', [WatchlistMovieController::class, 'destroy'])->middleware(['auth', 'verified'])->name('watchlist-movies.destroy');
Route::get('/watchlist-movies/check-status/{movieId}', [WatchlistMovieController::class, 'checkWatchStatus']);


Route::get('/inicio', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/movies/{movieId}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/movies/{movieId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/movies/{movieId}/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');    
    
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::put('/reviews/{review}/report', [ReviewController::class, 'report'])->name('reviews.report'); 
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('admin/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::patch('admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::delete('admin/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
    });
    
    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::patch('/admin/reported-reviews/{review}', [ReviewController::class, 'approveReportedReview'])->name('admin.reportedReviews.approve');
    Route::get('admin/reported-reviews', [ReviewController::class, 'reportedReviews'])->name('admin.reportedReviews.index');
    Route::delete('admin/reported-reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reportedReviews.destroy');

    Route::get('admin/movies', [MovieController::class, 'adminIndex'])->name('admin.movies.index');
    Route::post('admin/movies', [MovieController::class, 'adminManualStore'])->name('admin.movies.store');
    Route::put('admin/movies/{movie}', [MovieController::class, 'adminManualUpdate'])->name('admin.movies.update');
    Route::delete('admin/movies/{movie}', [MovieController::class, 'adminMovieDestroy'])->name('admin.movies.destroy');

});

require __DIR__.'/auth.php';
