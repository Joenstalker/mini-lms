@php
    $navItems = [
        [
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
            'purpose' => 'Overview of the library'
        ],
        [
            'name' => 'Books',
            'route' => 'books.index',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path></svg>',
            'purpose' => 'Manage library inventory'
        ],
        [
            'name' => 'Authors',
            'route' => 'authors.index',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
            'purpose' => 'Manage book authors'
        ],
        [
            'name' => 'Students',
            'route' => 'students.index',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
            'purpose' => 'Manage borrower profiles'
        ],
        [
            'name' => 'Transactions',
            'route' => 'borrow-transactions.index',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>',
            'purpose' => 'Handle borrowing logic'
        ],
    ];
@endphp

<div class="flex flex-col h-full bg-base-200 w-80">
    <!-- Brand -->
    <div class="p-6">
        <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline group">
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-primary-content shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-bold tracking-tight gradient-text">Mini-LMS</span>
                <span class="text-[10px] uppercase tracking-widest opacity-50 font-bold">Administrator</span>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow px-4 pb-4 overflow-y-auto custom-scrollbar">
        <ul class="menu menu-md p-0 gap-1">
            <li class="menu-title px-4 py-3 opacity-50 uppercase text-[10px] tracking-widest font-bold">Main Menu</li>
            @foreach($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['route'] . '*') || (isset($item['pattern']) && request()->is($item['pattern']));
                @endphp
                <li>
                    <a href="{{ route($item['route']) }}" 
                       @class([
                           'flex items-center gap-4 py-3 rounded-xl transition-all duration-300 group',
                           'bg-primary text-primary-content shadow-lg shadow-primary/20' => $isActive,
                           'hover:bg-primary/10 hover:text-primary' => !$isActive
                       ])>
                        <div @class([
                            'transition-transform duration-300 group-hover:scale-110',
                            'text-primary-content' => $isActive,
                            'text-primary group-hover:text-primary' => !$isActive
                        ])>
                            {!! $item['icon'] !!}
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold">{{ $item['name'] }}</span>
                            @if(!$isActive)
                                <span class="text-[10px] opacity-50 group-hover:opacity-70 transition-opacity">{{ $item['purpose'] }}</span>
                            @endif
                        </div>
                        @if(isset($item['badge']) && $item['badge'] > 0)
                            <div class="ml-auto badge badge-warning badge-sm font-bold shadow-sm">{{ $item['badge'] }}</div>
                        @elseif($isActive)
                            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-primary-content animate-pulse"></div>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>

        <!-- Bottom section / help -->
        <div class="mt-8 px-2">
            <div class="bg-base-300/50 rounded-2xl p-4 border border-base-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-success/20 text-success flex items-center justify-center text-lg">💡</div>
                    <span class="text-xs font-bold uppercase tracking-wider">Quick Hint</span>
                </div>
                <p class="text-[11px] opacity-60 leading-relaxed mb-3">Check the transactions module for overdue books and fines.</p>
                <a href="{{ route('borrow-transactions.overdue') }}" class="btn btn-xs btn-primary btn-block rounded-lg">View Overdue</a>
            </div>
        </div>
    </nav>

    <!-- User Section -->
    <div class="p-4 mt-auto">
        <div class="bg-base-300/30 rounded-2xl p-2 border border-base-300/50">
            <div class="flex items-center gap-3 p-2">
                <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content rounded-xl w-10 h-10 font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold truncate">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] opacity-50 truncate">{{ Auth::user()->email }}</span>
                </div>
                <div class="dropdown dropdown-top dropdown-end ml-auto">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle opacity-50 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                    </div>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-2xl bg-base-100 rounded-2xl w-52 mb-2 border border-base-200">
                        <li><a href="{{ route('profile.edit') }}" class="py-2 rounded-xl group hover:bg-primary/10"><svg class="w-4 h-4 opacity-70 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> My Profile</a></li>
                        <div class="divider my-1"></div>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 rounded-xl text-error hover:bg-error/10 flex items-center gap-2 group">
                                    <svg class="w-4 h-4 opacity-70 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
