<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\Providers\DramaBoxController;
use App\Http\Controllers\Providers\ReelShortController;
use App\Http\Controllers\Providers\ShortMaxController;
use App\Http\Controllers\Providers\NetShortController;
use App\Http\Controllers\Providers\MeloloController;
use App\Http\Controllers\Providers\FreeReelsController;
use App\Http\Controllers\Providers\DramaNovaController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Global Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

// ── DramaBox ──────────────────────────────────────────────────────────────────
Route::prefix('dramabox')->name('dramabox.')->group(function () {
    Route::get('/', [DramaBoxController::class, 'index'])->name('index');
    Route::get('/detail', [DramaBoxController::class, 'detail'])->name('detail');
    Route::get('/watch', [DramaBoxController::class, 'watch'])->name('watch');
    Route::get('/decrypt', [DramaBoxController::class, 'decrypt'])->name('decrypt');
});

// ── ReelShort ─────────────────────────────────────────────────────────────────
Route::prefix('reelshort')->name('reelshort.')->group(function () {
    Route::get('/', [ReelShortController::class, 'index'])->name('index');
    Route::get('/detail', [ReelShortController::class, 'detail'])->name('detail');
    Route::get('/watch', [ReelShortController::class, 'watch'])->name('watch');
});

// ── ShortMax ──────────────────────────────────────────────────────────────────
Route::prefix('shortmax')->name('shortmax.')->group(function () {
    Route::get('/', [ShortMaxController::class, 'index'])->name('index');
    Route::get('/detail', [ShortMaxController::class, 'detail'])->name('detail');
    Route::get('/watch', [ShortMaxController::class, 'watch'])->name('watch');
    Route::get('/hls', [ShortMaxController::class, 'hlsProxy'])->name('hls');
});

// ── NetShort ──────────────────────────────────────────────────────────────────
Route::prefix('netshort')->name('netshort.')->group(function () {
    Route::get('/', [NetShortController::class, 'index'])->name('index');
    Route::get('/detail', [NetShortController::class, 'detail'])->name('detail');
    Route::get('/watch', [NetShortController::class, 'watch'])->name('watch');
});

// ── Melolo ────────────────────────────────────────────────────────────────────
Route::prefix('melolo')->name('melolo.')->group(function () {
    Route::get('/', [MeloloController::class, 'index'])->name('index');
    Route::get('/detail', [MeloloController::class, 'detail'])->name('detail');
    Route::get('/watch', [MeloloController::class, 'watch'])->name('watch');
    Route::get('/stream', [MeloloController::class, 'stream'])->name('stream');
});

// ── FreeReels ─────────────────────────────────────────────────────────────────
Route::prefix('freereels')->name('freereels.')->group(function () {
    Route::get('/', [FreeReelsController::class, 'index'])->name('index');
    Route::get('/detail', [FreeReelsController::class, 'detail'])->name('detail');
    Route::get('/watch', [FreeReelsController::class, 'watch'])->name('watch');
    Route::get('/hls', [FreeReelsController::class, 'hlsProxy'])->name('hls');
});

// ── DramaNova ─────────────────────────────────────────────────────────────────
Route::prefix('dramanova')->name('dramanova.')->group(function () {
    Route::get('/', [DramaNovaController::class, 'index'])->name('index');
    Route::get('/detail', [DramaNovaController::class, 'detail'])->name('detail');
    Route::get('/watch', [DramaNovaController::class, 'watch'])->name('watch');
    Route::get('/getvideo', [DramaNovaController::class, 'getVideo'])->name('getvideo');
});

// ── Watchlist & History (Authenticated) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist/toggle', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');
    Route::get('/history', [WatchlistController::class, 'history'])->name('history.index');
    Route::post('/history/save', [WatchlistController::class, 'saveHistory'])->name('history.save');
    Route::delete('/history/{id}', [WatchlistController::class, 'removeHistory'])->name('history.remove');
    Route::post('/history/clear', [WatchlistController::class, 'clearHistory'])->name('history.clear');
});

// Watchlist check (works for guests too)
Route::get('/watchlist/check', [WatchlistController::class, 'check'])->name('watchlist.check');

// Auth routes from Breeze
require __DIR__.'/auth.php';
