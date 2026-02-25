<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks      = Book::count();
        $totalAuthors    = Author::count();
        $totalStudents   = Student::count();
        $activeBorrowings = Borrowing::whereIn('status', ['borrowed', 'overdue'])->count();
        $overdueBorrowings = Borrowing::where('status', 'overdue')->count();
        $recentBorrowings = Borrowing::with(['student', 'book'])
            ->latest()
            ->take(5)
            ->get();

        // Update overdue statuses
        Borrowing::where('status', 'borrowed')
            ->whereDate('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return view('dashboard', compact(
            'totalBooks',
            'totalAuthors',
            'totalStudents',
            'activeBorrowings',
            'overdueBorrowings',
            'recentBorrowings'
        ));
    }
}
