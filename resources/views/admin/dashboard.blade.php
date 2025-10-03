@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Quick Access Section -->
<div class="mb-8">
    <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-600 p-6">
        <h2 class="text-xl font-bold mb-4" style="color: #d1d5db;">Quick Access</h2>
        
        <!-- Site Access First -->
        <div class="mb-8">
            <h3 class="text-xl font-bold mb-4" style="color: #d1d5db;">Public Site Access</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('properties.index') }}" 
                   class="flex items-center justify-center p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                   style="color: #d1d5db; background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #4b5563, #6b7280)'; this.style.color='#f9fafb'; this.style.borderColor='#9ca3af'; this.style.boxShadow='0 6px 16px rgba(156, 163, 175, 0.3)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#d1d5db'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <div class="text-center">
                        <i class="fas fa-globe text-4xl mb-3" style="color: #fbbf24;"></i>
                        <h4 class="text-xl font-bold mb-2" style="color: #d1d5db;">View Public Site</h4>
                        <p class="text-sm" style="color: #9ca3af;">See how properties appear to visitors</p>
                    </div>
                </a>
                
                <a href="{{ route('properties.map') }}" 
                   class="flex items-center justify-center p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                   style="color: #d1d5db; background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #4b5563, #6b7280)'; this.style.color='#f9fafb'; this.style.borderColor='#9ca3af'; this.style.boxShadow='0 6px 16px rgba(156, 163, 175, 0.3)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#d1d5db'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl mb-3" style="color: #fbbf24;"></i>
                        <h4 class="text-xl font-bold mb-2" style="color: #d1d5db;">Interactive Map</h4>
                        <p class="text-sm" style="color: #9ca3af;">Explore properties on map</p>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Available Admin Sections -->
        <div>
            <h3 class="text-lg font-semibold mb-3" style="color: #d1d5db;">Admin Sections</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if(auth()->user()->hasAdminPermission('dashboard', 'view'))
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-tachometer-alt text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium" style="color: #d1d5db;">Dashboard</div>
                        <div class="text-sm" style="color: #9ca3af;">Overview & Stats</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('properties', 'view'))
                <a href="{{ route('admin.properties') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-building text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium" style="color: #d1d5db;">Properties</div>
                        <div class="text-sm" style="color: #9ca3af;">Manage Properties</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('clients', 'view'))
                <a href="{{ route('admin.clients') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-user-friends text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Clients</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Clients</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('rental_codes', 'view'))
                <a href="{{ route('rental-codes.index') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-key text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Rental Codes</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Codes</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('invoices', 'view'))
                <a href="{{ route('admin.invoices.index') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-file-invoice text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Invoices</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Invoices</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('group_viewings', 'view'))
                <a href="{{ route('admin.group-viewings.index') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-users text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Group Viewings</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Viewings</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('call_logs', 'view'))
                <a href="{{ route('admin.call-logs.index') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-phone text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Call Logs</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Calls</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('users', 'view'))
                <a href="{{ route('admin.users') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-users text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Users</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Agents</div>
                    </div>
                </a>
                @endif
                
                @if(auth()->user()->hasAdminPermission('admin_permissions', 'view'))
                <a href="{{ route('admin.user-permissions.index') }}" 
                   class="flex items-center p-4 rounded-lg transition-all duration-300 transform hover:scale-105"
                   style="background: linear-gradient(135deg, #1f2937, #374151); border: 2px solid #fbbf24; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);"
                   onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.borderColor='#f59e0b'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                   onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.borderColor='#fbbf24'; this.style.boxShadow='0 4px 12px rgba(251, 191, 36, 0.2)';">
                    <i class="fas fa-shield-alt text-xl mr-3" style="color: #fbbf24;"></i>
                    <div>
                        <div class="font-medium style="color: #d1d5db;"">Permissions</div>
                        <div class="text-sm style="color: #9ca3af;"">Manage Access</div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>




<!-- Floating Action Buttons -->
<div class="fixed bottom-6 right-6 z-50">
    <div class="flex flex-col space-y-3">
        <!-- Map Button -->
        <a href="{{ route('properties.map') }}" 
           class="group relative style="color: #d1d5db;" p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
           style="background: linear-gradient(135deg, #1f2937, #374151); border: 1px solid #4b5563;">
            <i class="fas fa-map-marked-alt text-xl" style="color: #fbbf24;"></i>
        </a>
        
        <!-- Site Button -->
        <a href="{{ route('properties.index') }}" 
           class="group relative style="color: #d1d5db;" p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
           style="background: linear-gradient(135deg, #1f2937, #374151); border: 1px solid #4b5563;">
            <i class="fas fa-globe text-xl" style="color: #fbbf24;"></i>
        </a>
    </div>
</div>
@endsection
