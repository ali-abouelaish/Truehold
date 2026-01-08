<nav class="shadow-lg border-b" style="background-color: #1f2937; border-bottom-color: #374151;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo/Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('properties.index') }}" class="flex items-center space-x-3 transition-colors">
                        <img src="{{ asset('images/truehold-logo.jpg') }}" alt="TRUEHOLD GROUP LTD" class="h-10 w-auto">
                        <span class="text-2xl font-bold" style="color: #fbbf24;">TRUEHOLD</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('properties.index') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('properties.index') ? 'border-fbbf24 text-d1d5db' : 'border-transparent text-9ca3af hover:text-d1d5db hover:border-fbbf24' }}"
                       style="{{ request()->routeIs('properties.index') ? 'border-bottom-color: #fbbf24; color: #d1d5db;' : 'border-bottom-color: transparent; color: #9ca3af;' }}">
                        Properties
                    </a>
                    <a href="{{ route('properties.map') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('properties.map') ? 'border-fbbf24 text-d1d5db' : 'border-transparent text-9ca3af hover:text-d1d5db hover:border-fbbf24' }}"
                       style="{{ request()->routeIs('properties.map') ? 'border-bottom-color: #fbbf24; color: #d1d5db;' : 'border-bottom-color: transparent; color: #9ca3af;' }}">
                        Map View
                    </a>
                </div>
            </div>

            <!-- Right side - Authentication -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <!-- Authenticated User -->
                    <div class="flex items-center space-x-4">
                        <span class="text-sm" style="color: #d1d5db;">
                            Welcome, {{ Auth::user()->name }}!
                        </span>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center px-3 py-2 border text-sm leading-4 font-medium rounded-md transition-colors"
                           style="background: linear-gradient(135deg, #1f2937, #374151); border-color: #fbbf24; color: #fbbf24;"
                           onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.color='#1f2937';"
                           onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#fbbf24';">
                            Admin Panel
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border text-sm leading-4 font-medium rounded-md transition-colors"
                                    style="background: linear-gradient(135deg, #374151, #4b5563); border-color: #6b7280; color: #d1d5db;"
                                    onmouseover="this.style.background='linear-gradient(135deg, #4b5563, #6b7280)'; this.style.borderColor='#fbbf24'; this.style.color='#f9fafb';"
                                    onmouseout="this.style.background='linear-gradient(135deg, #374151, #4b5563)'; this.style.borderColor='#6b7280'; this.style.color='#d1d5db';">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest User -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}" 
                           class="inline-flex items-center px-3 py-2 border text-sm leading-4 font-medium rounded-md transition-colors"
                           style="background: linear-gradient(135deg, #1f2937, #374151); border-color: #fbbf24; color: #fbbf24;"
                           onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.color='#1f2937';"
                           onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#fbbf24';">
                            Agent Login
                        </a>
                        
                    </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md transition duration-150 ease-in-out"
                        style="color: #fbbf24;"
                        onmouseover="this.style.backgroundColor='#374151'; this.style.color='#f59e0b';"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#fbbf24';">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" class="sm:hidden" style="background-color: #374151;">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('properties.index') }}" 
               class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors"
               style="{{ request()->routeIs('properties.index') ? 'background-color: #4b5563; border-left-color: #fbbf24; color: #d1d5db;' : 'border-left-color: transparent; color: #9ca3af;' }}"
               onmouseover="this.style.backgroundColor='#4b5563'; this.style.borderLeftColor='#fbbf24'; this.style.color='#d1d5db';"
               onmouseout="this.style.backgroundColor='{{ request()->routeIs('properties.index') ? '#4b5563' : 'transparent' }}'; this.style.borderLeftColor='{{ request()->routeIs('properties.index') ? '#fbbf24' : 'transparent' }}'; this.style.color='{{ request()->routeIs('properties.index') ? '#d1d5db' : '#9ca3af' }}';">
                Properties
            </a>
            <a href="{{ route('properties.map') }}" 
               class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors"
               style="{{ request()->routeIs('properties.map') ? 'background-color: #4b5563; border-left-color: #fbbf24; color: #d1d5db;' : 'border-left-color: transparent; color: #9ca3af;' }}"
               onmouseover="this.style.backgroundColor='#4b5563'; this.style.borderLeftColor='#fbbf24'; this.style.color='#d1d5db';"
               onmouseout="this.style.backgroundColor='{{ request()->routeIs('properties.map') ? '#4b5563' : 'transparent' }}'; this.style.borderLeftColor='{{ request()->routeIs('properties.map') ? '#fbbf24' : 'transparent' }}'; this.style.color='{{ request()->routeIs('properties.map') ? '#d1d5db' : '#9ca3af' }}';">
                Map View
            </a>
        </div>

        <!-- Mobile authentication options -->
        <div class="pt-4 pb-1" style="border-top-color: #4b5563;">
            @auth
                <div class="px-4 py-2">
                    <div class="text-base font-medium" style="color: #d1d5db;">{{ Auth::user()->name }}</div>
                    <div class="text-sm" style="color: #9ca3af;">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="block px-4 py-2 text-base font-medium transition-colors"
                       style="color: #9ca3af;"
                       onmouseover="this.style.color='#d1d5db'; this.style.backgroundColor='#4b5563';"
                       onmouseout="this.style.color='#9ca3af'; this.style.backgroundColor='transparent';">
                        Admin Panel
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-base font-medium transition-colors"
                                style="color: #9ca3af;"
                                onmouseover="this.style.color='#d1d5db'; this.style.backgroundColor='#4b5563';"
                                onmouseout="this.style.color='#9ca3af'; this.style.backgroundColor='transparent';">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" 
                       class="block w-full text-center px-4 py-2 text-base font-medium rounded-md transition-colors"
                       style="background: linear-gradient(135deg, #1f2937, #374151); border: 1px solid #fbbf24; color: #fbbf24;"
                       onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.color='#1f2937';"
                       onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#fbbf24';">
                        Agent Login
                    </a>

                </div>
            @endauth
        </div>
    </div>
</nav>
