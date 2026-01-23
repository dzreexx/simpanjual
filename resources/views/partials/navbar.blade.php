
        <!-- Header -->
        <header class="navbar bg-white shadow-sm z-10">
            <div class="flex-1">
                <h1 class="text-xl font-bold text-blue-500 px-4">Inbound</h1>
            </div>
            <div class="flex-none gap-4 px-4">
                <button class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <span class="badge badge-xs badge-primary indicator-item"></span>
                    </div>
                </button>
                <div class="flex items-center gap-2">
                    <div class="text-right hidden sm:block">
                        @if (Auth::check())   
                        <div class="text-sm font-bold">{{ Auth::user()->name}}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email}}</div>
                        @endif
                    </div>
                    <div class="avatar">
                        <div class="w-10 rounded-full">
                            <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                        </div>
                    </div>
                </div>
            </div>
        </header>