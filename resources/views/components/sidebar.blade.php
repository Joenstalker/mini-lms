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

<div class="flex flex-col h-full glass w-full overflow-hidden border-r border-white/10 text-white">
    <!-- Brand -->
    <div class="p-6">
        <a href="{{ route('dashboard') }}" class="flex items-center no-underline group justify-start overflow-hidden">
            <img src="{{ asset('images/lms-logo.png') }}" alt="Mini-LMS Logo" class="h-10 w-auto object-contain transition-all duration-300 group-hover:scale-105">
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow pb-4 overflow-x-hidden custom-scrollbar transition-all duration-300 overflow-y-auto px-4">
        <ul class="menu menu-md p-0 gap-1">
            <li class="menu-title px-4 py-3 opacity-50 uppercase text-[10px] tracking-widest font-bold text-left">
                <span>Main Menu</span>
            </li>
            @foreach($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['route'] . '*') || (isset($item['pattern']) && request()->is($item['pattern']));
                @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center py-3 px-4 justify-start gap-4 rounded-xl transition-all duration-300 group">
                        <div @class([
                            'transition-transform duration-300 group-hover:scale-110 flex-shrink-0 flex items-center justify-center',
                            'text-primary-content' => $isActive,
                                'text-primary' => !$isActive,
                                'text-primary group-hover:text-primary' => !$isActive
                            ])>
                                {!! $item['icon'] !!}
                            </div>
                            <div class="flex flex-col overflow-hidden transition-all duration-300">
                                <span class="font-bold truncate text-white">{{ $item['name'] }}</span>
                                @if(!$isActive)
                                    <span class="text-[10px] text-white/50 group-hover:text-white/70 transition-opacity truncate">{{ $item['purpose'] }}</span>
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

    </nav>

    <!-- User Section -->
    <div class="p-4 mt-auto transition-all duration-300 px-4">
        <div class="glass rounded-2xl border transition-all duration-300 overflow-hidden p-2 border-white/10">
            <button @click="showProfileModal = true" class="flex items-center gap-3 p-2 justify-start w-full hover:bg-white/10 rounded-xl transition-all duration-300 group cursor-pointer">
                <div class="avatar @if(!Auth::user()->profile_image) placeholder @endif flex-shrink-0">
                    <div class="bg-primary text-primary-content font-bold transition-all duration-300 rounded-full w-10 h-10 overflow-hidden">
                        @if(Auth::user()->profile_image)
                            <img src="{{ Auth::user()->profile_image }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 1) }}
                        @endif
                    </div>
                </div>
                <div class="flex flex-col min-w-0 transition-all duration-300 text-left flex-grow">
                    <span class="text-sm font-bold truncate text-white">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-white/50 truncate">{{ Auth::user()->email }}</span>
                </div>
                <svg class="w-4 h-4 text-white/30 group-hover:text-white/70 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>

            <!-- Visible Logout Button -->
            <div class="px-2 pb-2 mt-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-error btn-ghost w-full justify-start gap-2 rounded-xl normal-case font-medium hover:bg-error/20 text-white/70 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
