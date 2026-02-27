<x-app-layout>
    <div class="space-y-12 py-6">
        <!-- Header -->
        <header class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 text-white p-12 md:p-16 shadow-xl border border-white/5">
            <div class="relative z-10 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white/60 text-[10px] font-bold uppercase tracking-widest">
                    Library Management System
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">
                    Welcome back, <span class="text-primary-content bg-primary px-3 py-1 rounded-2xl">{{ Auth::user()->name }}</span>
                </h1>
                <p class="text-lg opacity-60 max-w-2xl font-medium">Your library is currently healthy. Here's what's happening today.</p>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure text-primary text-4xl mb-4 opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">📚</div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest">Books in Catalog</div>
                <div class="stat-value text-4xl font-extrabold mt-1 text-white">{{ $stats['total_books'] ?? 0 }}</div>
                <div class="stat-desc text-primary font-semibold mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    24 new this week
                </div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure text-secondary text-4xl mb-4 opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">👥</div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest">Active Students</div>
                <div class="stat-value text-4xl font-extrabold mt-1 text-white">{{ $stats['total_students'] ?? 0 }}</div>
                <div class="stat-desc text-secondary font-semibold mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    12 new members
                </div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure text-accent text-4xl mb-4 opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">📤</div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest">Active Borrows</div>
                <div class="stat-value text-4xl font-extrabold mt-1 text-white">{{ $stats['active_borrows'] ?? 0 }}</div>
                <div class="stat-desc text-accent font-semibold mt-2">Books currently out</div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure text-error text-4xl mb-4 opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">⚠️</div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest">Overdue Items</div>
                <div class="stat-value text-4xl font-extrabold mt-1 text-white">{{ $stats['overdue_items'] ?? 0 }}</div>
                <div class="stat-desc text-error font-semibold mt-2 hover:underline cursor-pointer">View overdue details</div>
            </div>
        </div>

        <!-- Layout Grid -->
        <div class="space-y-8 dashboard-padding">
            <!-- Main Content: Overdue & Transactions -->
            <div class="space-y-8">
                <!-- Overdue Alert Section -->
                @if($overdueTransactions->count() > 0)
                <div class="glass glass-card overflow-hidden border-error/30 shadow-2xl">
                    <div class="p-8 flex justify-between items-center bg-error/10 border-b border-error/20">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-error rounded-2xl flex items-center justify-center text-error-content shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white tracking-tight">Overdue Alerts</h3>
                        </div>
                        <a href="{{ route('borrow-transactions.index', ['filter' => 'overdue']) }}" class="btn btn-error btn-sm rounded-xl font-bold">Process Returns</a>
                    </div>
                    <div class="p-8 space-y-4">
                        @foreach($overdueTransactions as $ot)
                        <div class="flex items-center justify-between p-4 bg-base-100 rounded-2xl border border-error/10 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-4">
                                <div class="avatar placeholder">
                                    <div class="bg-error/10 text-error rounded-xl w-12 h-12 font-bold">
                                        {{ substr($ot->student->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-base-content">{{ $ot->student->name }}</span>
                                    <span class="text-xs opacity-60 font-medium">Book: <span class="italic">{{ $ot->book->title }}</span></span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="badge badge-error badge-sm font-bold py-3 px-4 rounded-lg">Overdue {{ $ot->due_date->diffForHumans() }}</span>
                                <span class="text-[10px] opacity-40 mt-1 uppercase tracking-widest font-bold">Due: {{ $ot->due_date->format('M d') }}</span>
                            </div>
                        </div>
                        @endforeach
                        @if($overdueTransactions->count() >= 3)
                        <div class="text-center pt-2">
                             <a href="{{ route('borrow-transactions.index', ['filter' => 'overdue']) }}" class="text-xs font-bold text-error uppercase tracking-widest hover:underline">+ See all overdue items</a>
                         </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="glass-card overflow-hidden">
                    <div class="p-8 flex justify-between items-center border-b border-base-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Recent Transactions</h3>
                        </div>
                        <a href="{{ route('borrow-transactions.index') }}" class="btn btn-ghost btn-sm text-primary font-bold">View History</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-lg">
                            <thead>
                                <tr class="bg-base-200/30 text-white/70">
                                    <th class="py-4">Student</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentTransactions as $transaction)
                                    <tr class="hover:bg-primary/5 transition-colors border-b border-base-200/50">
                                        <td class="py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="avatar placeholder">
                                                    <div class="bg-primary/10 text-primary rounded-xl w-10 font-bold">
                                                        {{ substr($transaction->student->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white">{{ $transaction->student->name }}</div>
                                                    <div class="text-xs text-white/50">{{ Str::limit($transaction->book->title, 25) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($transaction->status === 'borrowed')
                                                <div class="badge badge-info badge-sm rounded-lg py-3 px-4 font-bold">Borrowed</div>
                                            @elseif ($transaction->status === 'returned')
                                                <div class="badge badge-success badge-sm rounded-lg py-3 px-4 font-bold">Returned</div>
                                            @else
                                                <div class="badge badge-warning badge-sm rounded-lg py-3 px-4 font-bold">Overdue</div>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('borrow-transactions.show', $transaction) }}" class="btn btn-sm btn-ghost hover:bg-primary/20 hover:text-primary transition-all duration-300 rounded-lg group" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-20 opacity-50">No recent transactions</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
