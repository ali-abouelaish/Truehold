<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo/Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('properties.index') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        🏠 LET CONNECT
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('properties.index') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('properties.index') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Properties
                    </a>
                    <a href="{{ route('properties.map') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('properties.map') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Map View
                    </a>
                </div>
            </div>

            <!-- Right side - Authentication -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <!-- Authenticated User -->
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">
                            Welcome, {{ Auth::user()->name }}!
                        </span>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Admin Panel
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest User -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Agent Login
                        </a>
                        
                    </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('properties.index') }}" 
               class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('properties.index') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">
                Properties
            </a>
            <a href="{{ route('properties.map') }}" 
               class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('properties.map') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">
                Map View
            </a>
        </div>

        <!-- Mobile authentication options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4 py-2">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                        Admin Panel
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login') }}" 
                       class="block w-full text-center px-4 py-2 text-base font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-md">
                        Agent Login
                    </a>

                </div>
            @endauth
        </div>
    </div>
</nav>
