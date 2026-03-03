<x-app-layout>
    <div class="space-y-12 py-6" x-data="{
        showDetailsModal: false,
        showReturnModal: false,
        isLoadingDetails: false,
        isLoadingReturn: false,
        btDetailsContent: '',
        btReturnContent: '',
        countdown: { days: 0, hours: 0, mins: 0, secs: 0, isOverdue: false },
        timer: null,

        async fetchDetails(url) {
            this.isLoadingDetails = true;
            this.showDetailsModal = true;
            this.btDetailsContent = '';
            if (this.timer) clearInterval(this.timer);
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.btDetailsContent = await response.text();
                
                this.$nextTick(() => {
                    const dueDateInput = document.getElementById('details-due-date');
                    const statusInput = document.getElementById('details-status');
                    if (dueDateInput && statusInput && statusInput.value !== 'returned') {
                        this.startCountdown(dueDateInput.value);
                    } else {
                        this.countdown.isOverdue = false;
                    }
                });
            } catch (error) {
                console.error('Fetch failed:', error);
                this.btDetailsContent = '<div class=\'alert alert-error\'>Failed to load transaction details.</div>';
            } finally {
                this.isLoadingDetails = false;
            }
        },
        async fetchReturnForm(url) {
            this.isLoadingReturn = true;
            this.showReturnModal = true;
            this.btReturnContent = '';
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.btReturnContent = await response.text();
            } catch (error) {
                console.error('Fetch failed:', error);
                this.btReturnContent = '<div class=\'alert alert-error\'>Failed to load return form.</div>';
            } finally {
                this.isLoadingReturn = false;
            }
        },
        startCountdown(dueStr) {
            const update = () => {
                const now = new Date().getTime();
                // dueStr is 2026-03-01T23:59:59Z
                const due = new Date(dueStr).getTime();
                const diff = due - now;
                if (diff <= 0) {
                    this.countdown = { days: 0, hours: 0, mins: 0, secs: 0, isOverdue: true };
                    clearInterval(this.timer);
                    return;
                }
                this.countdown = {
                    days: Math.floor(diff / (1000 * 60 * 60 * 24)),
                    hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                    mins: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
                    secs: Math.floor((diff % (1000 * 60)) / 1000),
                    isOverdue: false
                };
            };
            update();
            this.timer = setInterval(update, 1000);
        }
    }">
        <!-- Header -->
        <header class="relative overflow-hidden glass-card text-white p-12 md:p-16">
            <div class="relative z-10 space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white/60 text-[10px] font-bold uppercase tracking-widest shadow-lg">
                    Library Management System
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">
                    Welcome back, <span class="text-primary-content bg-primary/80 backdrop-blur-md px-4 py-2 rounded-2xl">{{ Auth::user()->name }}</span>
                </h1>
                <p class="text-lg opacity-60 max-w-2xl font-medium">Your library is currently healthy. Here's what's happening today.</p>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure mb-4">
                    <img src="{{ asset('icons/books-icon.png') }}" alt="Books" class="w-20 h-20 mx-auto">
                </div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest text-center">Books in Catalog</div>
                <div class="stat-value text-4xl font-extrabold mt-2 text-white text-center">{{ $stats['total_books'] ?? 0 }}</div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure mb-4">
                    <img src="{{ asset('icons/students-icon.png') }}" alt="Students" class="w-20 h-20 mx-auto">
                </div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest text-center">Active Students</div>
                <div class="stat-value text-4xl font-extrabold mt-2 text-white text-center">{{ $stats['total_students'] ?? 0 }}</div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure mb-4">
                    <img src="{{ asset('icons/active-borrow-icon.png') }}" alt="Active Borrows" class="w-20 h-20 mx-auto">
                </div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest text-center">Active Borrows</div>
                <div class="stat-value text-4xl font-extrabold mt-2 text-white text-center">{{ $stats['active_borrows'] ?? 0 }}</div>
            </div>

            <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500">
                <div class="stat-figure mb-4">
                    <img src="{{ asset('icons/overdue-icon.png') }}" alt="Overdue" class="w-20 h-20 mx-auto">
                </div>
                <div class="stat-title text-white/60 text-xs font-bold uppercase tracking-widest text-center">Overdue Items</div>
                <div class="stat-value text-4xl font-extrabold mt-2 text-white text-center">{{ $stats['overdue_items'] ?? 0 }}</div>
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
                    <div class="p-8 flex justify-between items-center border-b border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Recent Transactions</h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-lg">
                            <thead>
                                <tr class="bg-white/5 text-white/70">
                                    <th class="py-4">Student</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentTransactions as $transaction)
                                    <tr class="hover:bg-white/5 transition-colors border-b border-white/5">
                                        <td class="py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="avatar shadow-sm border border-white/10 rounded-full overflow-hidden w-10 h-10">
                                                    @if($transaction->student->profile_image)
                                                        <img src="{{ $transaction->student->profile_image_url }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="bg-primary text-primary-content w-full h-full flex items-center justify-center font-bold text-xs">
                                                            {{ substr($transaction->student->name, 0, 1) }}
                                                        </div>
                                                    @endif
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
                                            <button @click="fetchDetails('{{ route('borrow-transactions.show', $transaction) }}')" class="btn btn-sm btn-ghost hover:bg-primary/20 hover:text-primary text-white transition-all duration-300 rounded-lg group" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
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

                {{-- Bar Chart Section --}}
                <div class="glass-card p-8">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-success/10 rounded-xl flex items-center justify-center text-success">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">Library Activity Metrics</h3>
                    </div>
                    <div class="h-[300px] w-full">
                        <canvas id="libraryMetricsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>



        <script>
            (function() {
                function initChart() {
                    const canvas = document.getElementById('libraryMetricsChart');
                    if (!canvas) return;

                    // If Chart.js isn't loaded yet, load it dynamically
                    if (typeof Chart === 'undefined') {
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                        script.crossOrigin = 'anonymous';
                        script.onload = function() { renderChart(canvas); };
                        document.head.appendChild(script);
                    } else {
                        renderChart(canvas);
                    }
                }

                function renderChart(canvas) {
                    const ctx = canvas.getContext('2d');
                    
                    // Destroy existing chart instance to prevent memory leaks and hover issues
                    if (window.libraryMetricsChartInstance) {
                        window.libraryMetricsChartInstance.destroy();
                    }

                    // Create gradient for the bars
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(56, 189, 248, 0.6)');   // Primary blue
                    gradient.addColorStop(1, 'rgba(129, 140, 248, 0.1)');  // Fades to indigo

                    window.libraryMetricsChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Books', 'Authors', 'Students', 'Transactions', 'Fines (₱)'],
                            datasets: [{
                                label: 'Metrics',
                                data: [
                                    {{ $stats['total_books'] ?? 0 }},
                                    {{ $stats['total_authors'] ?? 0 }},
                                    {{ $stats['total_students'] ?? 0 }},
                                    {{ $stats['total_transactions'] ?? 0 }},
                                    {{ $stats['total_fines'] ?? 0 }}
                                ],
                                backgroundColor: [
                                    'rgba(56, 189, 248, 0.3)',
                                    'rgba(129, 140, 248, 0.3)',
                                    'rgba(168, 85, 247, 0.3)',
                                    'rgba(236, 72, 153, 0.3)',
                                    'rgba(34, 197, 94, 0.3)'
                                ],
                                borderColor: [
                                    '#38bdf8',
                                    '#818cf8',
                                    '#a855f7',
                                    '#ec4899',
                                    '#22c55e'
                                ],
                                borderWidth: 2,
                                borderRadius: 12,
                                hoverBackgroundColor: [
                                    'rgba(56, 189, 248, 0.6)',
                                    'rgba(129, 140, 248, 0.6)',
                                    'rgba(168, 85, 247, 0.6)',
                                    'rgba(236, 72, 153, 0.6)',
                                    'rgba(34, 197, 94, 0.6)'
                                ],
                                hoverBorderWidth: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'x',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: { size: 14, weight: 'bold', family: 'Outfit' },
                                    bodyFont: { size: 14, family: 'Outfit' },
                                    padding: 16,
                                    borderRadius: 16,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.label === 'Fines (₱)') {
                                                label += '₱' + context.parsed.y.toLocaleString();
                                            } else {
                                                label += context.parsed.y.toLocaleString();
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: 'rgba(255, 255, 255, 0.4)',
                                        font: {
                                            family: 'Outfit',
                                            size: 11,
                                            weight: '600'
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'rgba(255, 255, 255, 0.7)',
                                        font: {
                                            family: 'Outfit',
                                            size: 12,
                                            weight: '700'
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart'
                            }
                        }
                    });
                }

                // Initialize on Turbo page loads
                document.addEventListener('turbo:load', initChart);
                
                // Cleanup on before-cache to avoid ghost charts
                document.addEventListener('turbo:before-cache', () => {
                    if (window.libraryMetricsChartInstance) {
                        window.libraryMetricsChartInstance.destroy();
                        window.libraryMetricsChartInstance = null;
                    }
                });
            })();
        </script>
    <!-- Details Modal -->
    <template x-teleport="body">
        <div class="modal backdrop-blur-md" :class="{ 'modal-open': showDetailsModal }" style="background-color: rgba(0,0,0,0.4); z-index: 1000;">
            <div class="modal-box max-w-5xl max-h-[90vh] glass text-white rounded-[2.5rem] p-0 border border-white/10 shadow-2xl overflow-hidden flex flex-col">
                <div class="p-8 md:p-12 overflow-y-auto flex-grow custom-scrollbar">
                    <div class="flex justify-between items-start mb-10">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center border border-primary/20 shadow-lg shadow-primary/10 text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black tracking-tight">Transaction Details</h3>
                                <p class="text-[11px] uppercase tracking-[0.3em] font-black opacity-40 mt-1">Borrowing Records System</p>
                            </div>
                        </div>
                        <button @click="showDetailsModal = false" class="btn btn-circle btn-ghost text-white/40 hover:text-white hover:rotate-90 transition-all duration-300">✕</button>
                    </div>
                    
                    <div x-show="isLoadingDetails" class="flex flex-col items-center justify-center py-20 space-y-6">
                        <div class="relative">
                            <span class="loading loading-spinner w-16 h-16 text-primary"></span>
                        </div>
                        <p class="text-xl font-black opacity-40 animate-pulse uppercase tracking-[0.3em]">Synchronizing...</p>
                    </div>

                    <div x-show="!isLoadingDetails">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                            <!-- Left: Core Information (Static from fetch) -->
                            <div class="lg:col-span-8">
                                <div x-html="btDetailsContent"></div>
                            </div>

                            <!-- Right: Live Interactivity & Status -->
                            <div class="lg:col-span-4 h-full">
                                <!-- Live Countdown Card -->
                                <template x-if="countdown && !countdown.isOverdue && btDetailsContent.includes('details-active')">
                                    <div class="bg-white/5 rounded-[2.5rem] p-8 border border-white/10 shadow-2xl flex flex-col items-center justify-center h-full min-h-[220px]">
                                        <h4 class="text-[10px] uppercase font-black tracking-[0.3em] text-primary mb-8 opacity-60">Time Remaining</h4>
                                        <div class="grid grid-cols-4 gap-3 w-full">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                    <span class="text-2xl font-black text-slate-900" x-text="countdown.days">0</span>
                                                </div>
                                                <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Days</span>
                                            </div>
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                    <span class="text-2xl font-black text-slate-900" x-text="countdown.hours">0</span>
                                                </div>
                                                <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Hrs</span>
                                            </div>
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                    <span class="text-2xl font-black text-slate-900" x-text="countdown.mins">0</span>
                                                </div>
                                                <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Mins</span>
                                            </div>
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-full aspect-square bg-white rounded-2xl flex items-center justify-center shadow-2xl">
                                                    <span class="text-2xl font-black text-slate-900" x-text="countdown.secs">0</span>
                                                </div>
                                                <span class="text-[8px] uppercase font-black tracking-tighter text-white/40">Secs</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Overdue Alert (Live) -->
                                <template x-if="countdown.isOverdue && btDetailsContent.includes('details-active')">
                                    <div class="bg-error/10 rounded-[2.5rem] p-8 border border-error/20 flex flex-col items-center text-center space-y-4 animate-pulse h-full justify-center min-h-[220px]">
                                        <div class="w-16 h-16 bg-error text-white rounded-3xl flex items-center justify-center shadow-2xl shadow-error/40">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-error font-black uppercase tracking-[0.3em] text-sm">Overdue Item</h4>
                                            <p class="text-[11px] font-bold opacity-40 mt-1">Fines are accumulating</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer for Actions -->
                <div class="px-12 py-6 bg-white/5 border-t border-white/10 flex justify-between items-center shrink-0">
                    <p class="text-[11px] opacity-40 font-medium italic tracking-tight">Live Sync: <span x-text="new Date().toLocaleTimeString()"></span></p>
                    <button @click="showDetailsModal = false" class="text-white text-lg font-black hover:text-white/70 transition-colors tracking-tight">Close Window</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Return Modal -->
    <template x-teleport="body">
        <div class="modal modal-bottom sm:modal-middle backdrop-blur-md" :class="{ 'modal-open': showReturnModal }" style="background-color: rgba(0,0,0,0.6); z-index: 2000;">
            <div class="modal-box max-w-lg glass text-white rounded-[2.5rem] p-0 border border-white/20 shadow-2xl relative overflow-hidden">
                {{-- Decorative background glow --}}
                <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/20 blur-[100px] rounded-full"></div>
                
                <div class="p-8 relative z-10">
                    <div x-show="isLoadingReturn" class="flex flex-col items-center justify-center py-12 space-y-4">
                        <span class="loading loading-spinner loading-lg text-primary"></span>
                        <p class="text-[10px] uppercase font-black tracking-widest opacity-40 animate-pulse">Initializing Return Form...</p>
                    </div>

                    <div x-show="!isLoadingReturn" x-html="btReturnContent"></div>
                </div>
            </div>
        </div>
    </template>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</div>
</x-app-layout>
