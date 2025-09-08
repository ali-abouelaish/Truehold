<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LET CONNECT') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .sidebar {
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100vh; /* ensure full viewport height so inner nav can scroll */
            overflow: hidden; /* prevent the whole sidebar from scrolling */
        }
        
        .sidebar.collapsed {
            width: 4rem;
        }
        
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-icon {
            margin-right: 0;
        }
        
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 1rem;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        .main-content {
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed + .main-content {
            margin-left: 4rem;
        }
        
        .sidebar-item {
            transition: all 0.2s ease;
        }
        
        .sidebar-item:hover {
            background-color: rgba(59, 130, 246, 0.1);
            border-right: 3px solid #3b82f6;
        }
        
        .sidebar-item.active {
            background-color: rgba(59, 130, 246, 0.15);
            border-right: 3px solid #3b82f6;
            color: #3b82f6;
        }
        
        .stats-card {
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Mobile responsiveness */
        @media (max-width: 767.98px) {
            .sidebar {
                width: 16rem; /* keep full width on mobile when opened */
                transform: translateX(-100%);
                will-change: transform;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-white shadow-lg w-64 min-h-screen fixed left-0 top-0 z-40">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-2xl text-blue-600 sidebar-icon mr-3"></i>
                        <span class="sidebar-text text-xl font-bold text-gray-800">LET CONNECT</span>
                    </div>
                    <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav mt-4">
                <div class="px-4 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Main</span>
                </div>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Properties</span>
                </div>
                
                <a href="{{ route('admin.properties') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.properties*') ? 'active' : '' }}">
                    <i class="fas fa-building sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">All Properties</span>
                </a>
                
                <a href="{{ route('admin.properties.create') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                    <i class="fas fa-plus sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Add Property</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Clients</span>
                </div>
                
                <a href="{{ route('admin.clients') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Manage Clients</span>
                </a>
                
                <a href="{{ route('admin.clients.create') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.clients.create') ? 'active' : '' }}">
                    <i class="fas fa-user-plus sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Add Client</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Rental Codes</span>
                </div>
                
                <a href="{{ route('rental-codes.index') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('rental-codes*') ? 'active' : '' }}">
                    <i class="fas fa-key sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Manage Rental Codes</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoicing</span>
                </div>
                
                <a href="{{ route('admin.invoices.index') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.invoices*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Manage Invoices</span>
                </a>
                
                <a href="{{ route('admin.invoices.create') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.invoices.create') ? 'active' : '' }}">
                    <i class="fas fa-plus sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Create Invoice</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Viewings</span>
                </div>
                
                <a href="{{ route('admin.group-viewings.index') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.group-viewings*') ? 'active' : '' }}">
                    <i class="fas fa-users sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Group Viewings</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</span>
                </div>
                
                <a href="{{ route('admin.users') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600 {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Manage Agents</span>
                </a>

                <div class="px-4 mt-6 mb-4">
                    <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase tracking-wider">Site</span>
                </div>
                
                <a href="{{ route('properties.index') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">View Site</span>
                </a>
                
                <a href="{{ route('properties.map') }}" 
                   class="sidebar-item flex items-center px-4 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-map sidebar-icon mr-3 text-lg"></i>
                    <span class="sidebar-text">Map View</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="w-full p-4 border-t border-gray-200 mt-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 sidebar-text">
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden"></div>

        <!-- Main Content -->
        <div class="main-content flex-1 ml-64">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="mr-3 text-gray-600 md:hidden">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="text-gray-500 hover:text-gray-700 relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>
                        
                        <!-- Quick Actions -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.properties.create') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-plus mr-2"></i>Quick Add
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        (function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function isMobile() {
                return window.innerWidth < 768;
            }

            function openMobileSidebar() {
                sidebar.classList.add('open');
                if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('open');
                if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // Desktop collapse toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (isMobile()) {
                        // On mobile, use slide-in/out
                        if (sidebar.classList.contains('open')) {
                            closeMobileSidebar();
                        } else {
                            openMobileSidebar();
                        }
                        return;
                    }
                    sidebar.classList.toggle('collapsed');
                    if (sidebar.classList.contains('collapsed')) {
                        mainContent.style.marginLeft = '4rem';
                    } else {
                        mainContent.style.marginLeft = '16rem';
                    }
                });
            }

            // Mobile hamburger in top bar
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('open')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                });
            }

            // Close when clicking overlay (mobile)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeMobileSidebar);
            }

            // Handle responsive state
            function applyResponsiveLayout() {
                if (isMobile()) {
                    // Reset desktop-specific states
                    sidebar.classList.remove('collapsed');
                    mainContent.style.marginLeft = '0';
                    closeMobileSidebar();
                } else {
                    // Ensure overlay is hidden on desktop
                    closeMobileSidebar();
                    mainContent.style.marginLeft = sidebar.classList.contains('collapsed') ? '4rem' : '16rem';
                }
            }

            window.addEventListener('resize', applyResponsiveLayout);
            applyResponsiveLayout();
        })();
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
