<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowTransactionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Root route: show the login page (welcome) for guests, redirect to dashboard for logged-in admins
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// All routes require authentication — this is a physical library admin tool
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile management
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Books (full CRUD, admin only)
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Authors (full CRUD, admin only)
    Route::resource('authors', AuthorController::class);

    // Students (full CRUD, admin only)
    Route::resource('students', StudentController::class);

    // Borrow Transactions (admin processes all borrowing/returning)
    Route::get('/borrow-transactions', [BorrowTransactionController::class, 'index'])->name('borrow-transactions.index');
    Route::get('/borrow-transactions/create', [BorrowTransactionController::class, 'create'])->name('borrow-transactions.create');
    Route::post('/borrow-transactions', [BorrowTransactionController::class, 'store'])->name('borrow-transactions.store');
    Route::get('/borrow-transactions/{borrow_transaction}', [BorrowTransactionController::class, 'show'])->name('borrow-transactions.show');
    Route::get('/borrow-transactions/{borrow_transaction}/edit', [BorrowTransactionController::class, 'edit'])->name('borrow-transactions.edit');
    Route::patch('/borrow-transactions/{borrow_transaction}', [BorrowTransactionController::class, 'update'])->name('borrow-transactions.update');
    Route::delete('/borrow-transactions/{borrow_transaction}', [BorrowTransactionController::class, 'destroy'])->name('borrow-transactions.destroy');
});

require __DIR__.'/auth.php';
