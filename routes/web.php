<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowTransactionController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

// Public Borrowing for Students
Route::get('borrow-transactions/create', [BorrowTransactionController::class, 'create'])->name('borrow-transactions.create');
Route::post('borrow-transactions', [BorrowTransactionController::class, 'store'])->name('borrow-transactions.store');
Route::get('students/{student}/borrow-history', [BorrowTransactionController::class, 'studentHistory'])->name('borrow-transactions.student-history');
Route::get('borrow-transactions/{borrow_transaction}', [BorrowTransactionController::class, 'show'])->name('borrow-transactions.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student Management
    Route::resource('students', StudentController::class);

    // Author Management
    Route::resource('authors', AuthorController::class)->except(['index', 'show']);

    // Book Management
    Route::resource('books', BookController::class)->except(['index', 'show']);

    // Admin Borrow Management
    Route::get('borrow-transactions', [BorrowTransactionController::class, 'index'])->name('borrow-transactions.index');
    Route::get('borrow-transactions/overdue', [BorrowTransactionController::class, 'overdue'])->name('borrow-transactions.overdue');
    Route::delete('borrow-transactions/{borrow_transaction}', [BorrowTransactionController::class, 'destroy'])->name('borrow-transactions.destroy');
    Route::get('borrow-transactions/{borrow_transaction}/edit', [BorrowTransactionController::class, 'edit'])->name('borrow-transactions.edit');
    Route::patch('borrow-transactions/{borrow_transaction}', [BorrowTransactionController::class, 'update'])->name('borrow-transactions.update');
});

require __DIR__.'/auth.php';
