<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/article/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category');


/**
 * Protected routes
 */
Route::middleware([AdminMiddleware::class])->group(function() {
    Route::get('/iviblg-admin', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('article/store', [ArticleController::class, 'store'])->name('article.store');
    Route::get('article/{article}/edit', [ArticleController::class, 'edit'])->name('article.edit');
    Route::patch('article/{article}/edit', [ArticleController::class, 'update'])->name('article.update');
    Route::delete('article/{article}', [ArticleController::class, 'destroy'])->name('article.destroy');
});
