<?php

namespace App\Http\Controllers;

use App\Models\BorrowTransaction;
use App\Models\Book;
use App\Models\Student;
use App\Services\CacheService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with optimized queries.
     */
    public function index(Request $request)
    {
        // Use cached stats for better performance
        $stats = CacheService::getDashboardStats();
        
        // Get recent overdue transactions with eager loading
        $overdueTransactions = BorrowTransaction::with(['student', 'book'])
            ->whereIn('status', ['borrowed', 'partially_returned'])
            ->where('due_date', '<', now())
            ->latest()
            ->limit(3)
            ->get();

        // Get recent transactions with eager loading
        $recentTransactions = BorrowTransaction::with(['student', 'book'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'overdueTransactions', 'recentTransactions'));
    }

    /**
     * Get dashboard statistics (API endpoint)
     */
    public function stats(Request $request)
    {
        if ($request->ajax()) {
            $stats = CacheService::getDashboardStats();
            return response()->json($stats);
        }
        
        return redirect()->route('dashboard');
    }
}
