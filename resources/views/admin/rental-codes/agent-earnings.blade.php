@extends('layouts.admin')

@section('page-title', 'Agent Earnings Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Enhanced Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            @if($isPayrollView)
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    Agent Commission File - {{ $agentSearch }}
                                @else
                                    My Commission File
                                @endif
                            @else
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    Agent Earnings Analytics
                                @else
                                    My Earnings
                                @endif
                            @endif
                        </h1>
                        <p class="text-gray-600 mt-1">
                            @if($isPayrollView)
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    Approved rentals up to 10th of each month - Commission view
                                @else
                                    Your approved rentals up to 10th of each month
                                @endif
                            @else
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    Comprehensive earnings analysis and performance insights
                                @else
                                    Your earnings overview and performance insights
                                @endif
                            @endif
                        </p>
                        <!-- Date Range Indicator -->
                        @if($startDate && $endDate)
                            <div class="mt-2 inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                                <span class="ml-2 text-blue-600">({{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days)</span>
                            </div>
                        @endif
                    </div>
    </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                    @if($isPayrollView)
                        <a href="{{ route('rental-codes.agent-earnings') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Back to All Agents
                        </a>
                    @else
        <a href="{{ route('rental-codes.index') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Back to Rental Codes
        </a>
                    @endif
                    @if(count($agentEarnings) > 0)
                    <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </button>
                    @endif
                </div>
            </div>
    </div>
</div>

    <!-- Advanced Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>
                        @if($isPayrollView)
                            Payroll Filters
                        @else
                        Advanced Filters
                        @endif
                    </h3>
                    <button onclick="toggleFilters()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-chevron-down" id="filterToggleIcon"></i>
                    </button>
                </div>
    </div>
            <div class="p-6" id="filtersContent" style="display: none;">
                @if($isPayrollView)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900">Commission File - Cycle View</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    This view shows only <strong>approved rentals from the selected commission cycle</strong> ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}) that this agent participated in as rental agent or marketing agent.
                                </p>
                                <p class="text-sm text-blue-700 mt-2">
                                    <strong>Note:</strong> Only transactions within the selected date range are shown. Use the quick cycle selector above to view different periods.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                <form method="GET" action="{{ route('rental-codes.agent-earnings') }}" class="space-y-6" id="filterForm">
                    <!-- Quick Commission Cycle Selector -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar-check mr-1"></i>Quick Select Commission Cycle (11th to 10th)
                            </label>
                            <span class="text-xs text-gray-500">Click to apply</span>
                        </div>
                        
                        <!-- Commission Cycle Buttons -->
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mb-3">
                            @php
                                $currentDate = now();
                                $cycles = [];
                                
                                // Generate last 8 commission cycles
                                for ($i = 0; $i < 8; $i++) {
                                    $cycleDate = $currentDate->copy()->subMonths($i);
                                    
                                    if ($cycleDate->day <= 10) {
                                        $cycleStart = $cycleDate->copy()->subMonthNoOverflow()->day(11);
                                        $cycleEnd = $cycleDate->copy()->day(10);
                                    } else {
                                        $cycleStart = $cycleDate->copy()->day(11);
                                        $cycleEnd = $cycleDate->copy()->addMonthNoOverflow()->day(10);
                                    }
                                    
                                    // Check if this cycle matches current filter
                                    $isSelected = ($startDate === $cycleStart->toDateString() && 
                                                  $endDate === min($currentDate->toDateString(), $cycleEnd->toDateString()));
                                    
                                    $cycles[] = [
                                        'start' => $cycleStart->toDateString(),
                                        'end' => min($currentDate->toDateString(), $cycleEnd->toDateString()),
                                        'label' => $cycleStart->format('d-M') . ' to ' . $cycleEnd->format('d-M Y'),
                                        'shortLabel' => $cycleStart->format('M Y'),
                                        'isCurrent' => $i === 0,
                                        'isSelected' => $isSelected
                                    ];
                                }
                            @endphp
                            
                            @foreach($cycles as $cycle)
                                <button type="button" 
                                        onclick="selectCycle('{{ $cycle['start'] }}', '{{ $cycle['end'] }}')"
                                        class="px-3 py-2 text-sm font-medium rounded-lg border transition-all duration-200 relative
                                               {{ $cycle['isSelected'] ? 'bg-green-600 text-white border-green-700 ring-2 ring-green-300' : 
                                                  ($cycle['isCurrent'] ? 'bg-blue-600 text-white border-blue-700 hover:bg-blue-700' : 
                                                   'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-blue-400') }}">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="flex items-center">
                                            @if($cycle['isSelected'])
                                                <i class="fas fa-check-circle mr-1 text-xs"></i>
                                            @elseif($cycle['isCurrent'])
                                                <i class="fas fa-star mr-1 text-xs"></i>
                                            @endif
                                            <span class="font-semibold">{{ $cycle['shortLabel'] }}</span>
                                        </div>
                                        <span class="text-xs mt-1 opacity-90">{{ $cycle['label'] }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                        
                        <!-- Quick Preset Buttons -->
                        <div class="border-t border-blue-200 pt-3 mt-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-gray-600">Quick Presets:</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @php
                                    // Last 3 months preset
                                    $last3MonthsStart = $currentDate->copy()->subMonths(3);
                                    $last3MonthsEnd = $currentDate->toDateString();
                                    
                                    // Last 6 months preset
                                    $last6MonthsStart = $currentDate->copy()->subMonths(6);
                                    $last6MonthsEnd = $currentDate->toDateString();
                                    
                                    // This year preset
                                    $thisYearStart = $currentDate->copy()->startOfYear();
                                    $thisYearEnd = $currentDate->toDateString();
                                    
                                    // Last year preset
                                    $lastYearStart = $currentDate->copy()->subYear()->startOfYear();
                                    $lastYearEnd = $currentDate->copy()->subYear()->endOfYear();
                                @endphp
                                
                                <button type="button" 
                                        onclick="selectCycle('{{ $last3MonthsStart->toDateString() }}', '{{ $last3MonthsEnd }}')"
                                        class="px-3 py-2 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all duration-200">
                                    <i class="fas fa-calendar-week mr-1"></i>Last 3 Months
                                </button>
                                
                                <button type="button" 
                                        onclick="selectCycle('{{ $last6MonthsStart->toDateString() }}', '{{ $last6MonthsEnd }}')"
                                        class="px-3 py-2 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all duration-200">
                                    <i class="fas fa-calendar mr-1"></i>Last 6 Months
                                </button>
                                
                                <button type="button" 
                                        onclick="selectCycle('{{ $thisYearStart->toDateString() }}', '{{ $thisYearEnd }}')"
                                        class="px-3 py-2 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all duration-200">
                                    <i class="fas fa-calendar-alt mr-1"></i>This Year
                                </button>
                                
                                <button type="button" 
                                        onclick="selectCycle('{{ $lastYearStart->toDateString() }}', '{{ $lastYearEnd->toDateString() }}')"
                                        class="px-3 py-2 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all duration-200">
                                    <i class="fas fa-history mr-1"></i>Last Year
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i>Start Date
                            </label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>
            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i>End Date
                            </label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i>Agent Filter
                            </label>
                            @if($isPayrollView)
                                <div class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 p-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-check text-green-600 mr-2"></i>
                                        <span class="font-medium text-gray-900">{{ $agentSearch }}</span>
                                        <span class="ml-2 text-sm text-gray-500">(Payroll View)</span>
                                    </div>
                                </div>
                                <input type="hidden" name="agent_search" value="{{ $agentSearch }}">
                            @elseif(auth()->check() && auth()->user()->role === 'admin')
                            <select name="agent_search" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">All Agents</option>
                                @foreach($agentEarnings as $agent)
                                    <option value="{{ $agent['name'] }}" {{ $agentSearch == $agent['name'] ? 'selected' : '' }}>
                                        {{ $agent['name'] }} ({{ $agent['transaction_count'] }} transactions)
                                    </option>
                                @endforeach
                            </select>
                            @else
                                <div class="w-full border-gray-300 rounded-lg shadow-sm bg-blue-50 p-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                                        <span class="font-medium text-gray-900">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</span>
                                        <span class="ml-2 text-sm text-gray-500">(Your Payroll Only)</span>
                                    </div>
                                </div>
                                @auth
                                <input type="hidden" name="agent_search" value="{{ auth()->user()->name }}">
                                @endauth
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-bullhorn mr-1"></i>Marketing Agent Filter
                            </label>
                            <select name="marketing_agent_filter" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">All Agents</option>
                                <option value="marketing_only" {{ $marketingAgentFilter == 'marketing_only' ? 'selected' : '' }}>Marketing Agents Only</option>
                                <option value="rent_only" {{ $marketingAgentFilter == 'rent_only' ? 'selected' : '' }}>Rent Agents Only</option>
                                <option value="both" {{ $marketingAgentFilter == 'both' ? 'selected' : '' }}>Both Rent & Marketing</option>
                            </select>
            </div>
            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-check-circle mr-1"></i>Status
                            </label>
                            <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">All Statuses</option>
                    <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status==='cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-credit-card mr-1"></i>Payment Method
                            </label>
                            <select name="payment_method" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">All Methods</option>
                    <option value="Cash" {{ $paymentMethod==='Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Transfer" {{ $paymentMethod==='Transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>
                        <div class="flex items-end space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>Apply Filters
                </button>
                            <a href="{{ route('rental-codes.agent-earnings') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium text-center transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
                        </div>
            </div>
        </form>
    </div>
</div>

        <!-- Summary Dashboard -->
        @if(!$agentSearch)
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Agents</p>
                        <p class="text-xl font-bold">{{ $summary['total_agents'] }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 p-2 rounded">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Total Earnings</p>
                        <p class="text-xl font-bold">£{{ number_format($summary['total_earnings'], 2) }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 p-2 rounded">
                        <i class="fas fa-pound-sign text-lg"></i>
            </div>
        </div>
    </div>
    
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Total Transactions</p>
                        <p class="text-xl font-bold">{{ $summary['total_transactions'] }}</p>
                </div>
                    <div class="bg-purple-400 bg-opacity-30 p-2 rounded">
                        <i class="fas fa-file-invoice text-lg"></i>
            </div>
        </div>
    </div>
    
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium">Avg per Agent</p>
                        <p class="text-xl font-bold">£{{ number_format($summary['avg_earnings_per_agent'], 2) }}</p>
                </div>
                    <div class="bg-orange-400 bg-opacity-30 p-2 rounded">
                        <i class="fas fa-chart-bar text-lg"></i>
            </div>
        </div>
    </div>
</div>

        <!-- Charts Section -->
        @if(count($agentEarnings) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Commission Cycle Earnings Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Commission Cycle Earnings Trend
                    </h3>
                    <p class="text-xs text-gray-500 mt-1"></p>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="monthlyChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Top Agents Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                    Top 10 Agents by Earnings
                </h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="agentsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        @endif
        @endif

<!-- Agent Earnings Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                @if($agentSearch)
                                    {{ $agentSearch }} - Commission File
                                @elseif($isPayrollView)
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                        Commission File - {{ $agentSearch }}
                                    @else
                                        My Commission File
                                    @endif
                                @else
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                        Agent Earnings Breakdown
                                    @else
                                        Agent Leaderboard
                                    @endif
                                @endif
                            </h3>
                            @if($isPayrollView)
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Cycle View: Rentals Only
                                </span>
                            @endif
                        </div>
        <p class="text-sm text-gray-500 mt-1">
                            @if($agentSearch)
                                Complete payroll breakdown for {{ $agentSearch }}
                                @if($startDate || $endDate)
                                    • Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }}
                                    — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                @endif
                            @elseif($isPayrollView)
                                Approved rentals for this agent only
                                @if($startDate || $endDate)
                                    • Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }}
                                    — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                @endif
                            @else
                            @if($startDate || $endDate)
                                Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }}
                                — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            @else
                                All Time Data
                            @endif
            @if($status) • Status: {{ ucfirst($status) }} @endif
            @if($paymentMethod) • Payment: {{ $paymentMethod }} @endif
                            @endif
        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">
                            @if($isPayrollView)
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    {{ count($agentEarnings) }} commission record{{ count($agentEarnings) !== 1 ? 's' : '' }}
                                @else
                                    My commission record{{ count($agentEarnings) !== 1 ? 's' : '' }}
                                @endif
                            @else
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    {{ count($agentEarnings) }} agents
                                @else
                                    Leaderboard
                                @endif
                            @endif
                        </span>
                        <div class="flex items-center space-x-1">
                            <button onclick="toggleView('table')" id="tableViewBtn" class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <i class="fas fa-table"></i>
                            </button>
                            <button onclick="toggleView('cards')" id="cardsViewBtn" class="p-2 bg-gray-100 text-gray-600 rounded-lg">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>
    </div>
    
            <!-- Table View -->
            <div id="tableView" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Agent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Agent Earnings
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Agency Earnings
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Earnings
                    </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Activity
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($agentEarnings as $index => $agent)
                        @php
                            $isAdmin = auth()->check() && auth()->user()->role === 'admin';
                            $currentUser = auth()->check() ? auth()->user()->name : null;
                            $isCurrentUser = $agent['name'] === $currentUser;
                            $showUserRanking = !$isAdmin && $isCurrentUser && $loop->index > 2;
                            
                            // For normal users, show top 3 agents + their own ranking if not in top 3
                            if (!$isAdmin) {
                                // If user is in top 3, show only top 3
                                if ($isCurrentUser && $loop->index < 3) {
                                    // User is in top 3, show them
                                } elseif (!$isCurrentUser && $loop->index >= 3) {
                                    // Not the current user and beyond top 3, skip
                                    continue;
                                } elseif ($isCurrentUser && $loop->index >= 3) {
                                    // User is beyond top 3, show them as 4th
                                } elseif (!$isCurrentUser && $loop->index >= 3) {
                                    // Not the current user and beyond top 3, skip
                                    continue;
                                }
                            }
                            
                            $rankingClass = '';
                            $rankBadgeClass = '';
                            $rankingEmoji = '';
                            $rankingText = '';
                            
                            if ($loop->index === 0) {
                                $rankingClass = 'ranking-row-gold';
                                $rankBadgeClass = 'rank-gold';
                                $rankingEmoji = '👑';
                                $rankingText = 'Gold - #1';
                            } elseif ($loop->index === 1) {
                                $rankingClass = 'ranking-row-silver';
                                $rankBadgeClass = 'rank-silver';
                                $rankingEmoji = '🥈';
                                $rankingText = 'Silver - #2';
                            } elseif ($loop->index === 2) {
                                $rankingClass = 'ranking-row-bronze';
                                $rankBadgeClass = 'rank-bronze';
                                $rankingEmoji = '🥉';
                                $rankingText = 'Bronze - #3';
                            } elseif ($showUserRanking) {
                                $rankingClass = 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400';
                                $rankBadgeClass = 'rank-user';
                                $rankingEmoji = '👤';
                                $rankingText = 'Your Ranking - #' . ($loop->index + 1);
                            }
                            
                            $isAfterTopThree = $loop->index >= 3;
                        @endphp
                        <tr class="transition-colors duration-200 {{ $rankingClass }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                                    @php
                                        $avatarClass = '';
                                        $showRankingIcon = false;
                                        
                                        if ($loop->index === 0) {
                                            $avatarClass = 'bg-gradient-to-r from-yellow-500 to-yellow-700 shadow-xl';
                                            $showRankingIcon = true;
                                        } elseif ($loop->index === 1) {
                                            $avatarClass = 'bg-gradient-to-r from-gray-400 to-gray-600 shadow-xl';
                                            $showRankingIcon = true;
                                        } elseif ($loop->index === 2) {
                                            $avatarClass = 'bg-gradient-to-r from-orange-500 to-orange-700 shadow-xl';
                                            $showRankingIcon = true;
                                        } elseif ($showUserRanking) {
                                            $avatarClass = 'bg-gradient-to-r from-blue-500 to-indigo-600';
                                            $showRankingIcon = true;
                                        } else {
                                            $avatarClass = 'bg-gradient-to-r from-blue-500 to-purple-600';
                                        }
                                    @endphp
                                    <div class="w-12 h-12 {{ $avatarClass }} rounded-full flex items-center justify-center mr-4 relative overflow-visible">
                                        @if($showRankingIcon)
                                            <div class="ranking-container {{ $rankBadgeClass }}" id="ranking-icon-{{ $index }}">
                                                <span class="ranking-emoji">{{ $rankingEmoji }}</span>
                                            </div>
                                        @endif
                                        <span class="text-white font-bold text-xl relative z-10 drop-shadow-lg">
                                            {{ strtoupper(substr($agent['name'], 0, 2)) }}
                                        </span>
                            </div>
                                    <div>
                                        <div class="text-sm font-semibold {{ $isAfterTopThree ? 'text-gray-700' : 'text-gray-900' }} flex items-center">
                                            {{ $agent['name'] }}
                                            @if($rankingText)
                                                <span class="ml-2 {{ $loop->index === 0 ? 'bg-yellow-100 text-yellow-800' : ($loop->index === 1 ? 'bg-gray-100 text-gray-800' : ($loop->index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }} text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                    <span class="mr-1">{{ $rankingEmoji }}</span>{{ $rankingText }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs {{ $isAfterTopThree ? 'text-gray-600' : 'text-gray-500' }}">{{ $agent['transaction_count'] }} total transactions</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $isAfterTopThree ? 'text-gray-700' : 'text-gray-900' }}">
                                    £{{ number_format($agent['agent_earnings'], 2) }}
                        </div>
                        <div class="text-xs {{ $isAfterTopThree ? 'text-gray-600' : 'text-gray-500' }}">
                                    55% of commission
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $isAfterTopThree ? 'text-gray-700' : 'text-gray-900' }}">
                                    £{{ number_format($agent['agency_earnings'], 2) }}
                        </div>
                        <div class="text-xs {{ $isAfterTopThree ? 'text-gray-600' : 'text-gray-500' }}">
                                    45% of commission
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $totalEarningsClass = '';
                            $totalEarningsEmoji = '';
                            
                            if ($loop->index === 0) {
                                $totalEarningsClass = 'text-yellow-600';
                                $totalEarningsEmoji = '👑';
                            } elseif ($loop->index === 1) {
                                $totalEarningsClass = 'text-gray-600';
                                $totalEarningsEmoji = '🥈';
                            } elseif ($loop->index === 2) {
                                $totalEarningsClass = 'text-orange-600';
                                $totalEarningsEmoji = '🥉';
                            } elseif ($showUserRanking) {
                                $totalEarningsClass = 'text-blue-600';
                                $totalEarningsEmoji = '👤';
                            } else {
                                $totalEarningsClass = 'text-gray-900';
                            }
                        @endphp
                        <div class="text-lg font-bold {{ $isAfterTopThree ? 'text-gray-700' : $totalEarningsClass }} flex items-center">
                            @if($totalEarningsEmoji)
                                <span class="mr-2">{{ $totalEarningsEmoji }}</span>
                            @endif
                            £{{ number_format($agent['total_earnings'], 2) }}
                        </div>
                        <div class="text-xs {{ $isAfterTopThree ? 'text-gray-600' : $totalEarningsClass }}">
                                    {{ $agent['transaction_count'] }} total
                        </div>
                    </td>
                                        
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                                    {{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ isset($agent['id']) ? route('rental-codes.agent-payroll', ['agentId' => $agent['id']]) : route('rental-codes.agent-payroll-by-name', ['agentName' => $agent['name']]) }}" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i>View Commission File
                                    </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No earnings data found</h3>
                                    <p class="text-gray-500">No rental codes found for the selected criteria</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
            </div>

            <!-- Cards View -->
            <div id="cardsView" class="hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($agentEarnings as $index => $agent)
                    @php
                        $isAdmin = auth()->check() && auth()->user()->role === 'admin';
                        $currentUser = auth()->check() ? auth()->user()->name : null;
                        $isCurrentUser = $agent['name'] === $currentUser;
                        $showUserRanking = !$isAdmin && $isCurrentUser && $loop->index > 2;
                        
                        // For normal users, show top 3 agents + their own ranking if not in top 3
                        if (!$isAdmin) {
                            // If user is in top 3, show only top 3
                            if ($isCurrentUser && $loop->index < 3) {
                                // User is in top 3, show them
                            } elseif (!$isCurrentUser && $loop->index >= 3) {
                                // Not the current user and beyond top 3, skip
                                continue;
                            } elseif ($isCurrentUser && $loop->index >= 3) {
                                // User is beyond top 3, show them as 4th
                            } elseif (!$isCurrentUser && $loop->index >= 3) {
                                // Not the current user and beyond top 3, skip
                                continue;
                            }
                        }
                        
                        $cardClass = '';
                        $cardRing = '';
                        $isAfterTopThree = $loop->index >= 3;
                        
                        if ($loop->index === 0) {
                            $cardClass = 'ranking-row-gold shadow-xl';
                            $cardRing = 'ring-4 ring-yellow-500';
                        } elseif ($loop->index === 1) {
                            $cardClass = 'ranking-row-silver shadow-xl';
                            $cardRing = 'ring-4 ring-gray-500';
                        } elseif ($loop->index === 2) {
                            $cardClass = 'ranking-row-bronze shadow-xl';
                            $cardRing = 'ring-4 ring-orange-600';
                        } elseif ($showUserRanking) {
                            $cardClass = 'bg-gradient-to-br from-blue-50 to-indigo-50';
                            $cardRing = 'ring-2 ring-blue-400';
                        } else {
                            $cardClass = 'bg-gradient-to-br from-white to-gray-50';
                        }
                    @endphp
                    <div class="{{ $cardClass }} rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200 {{ $cardRing }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @php
                                    $cardAvatarClass = '';
                                    $showCardRankingIcon = false;
                                    $cardRankingEmoji = '';
                                    $cardRankBadgeClass = '';
                                    $cardRankingText = '';
                                    
                                    if ($loop->index === 0) {
                                        $cardAvatarClass = 'bg-gradient-to-r from-yellow-400 to-yellow-600 shadow-lg';
                                        $showCardRankingIcon = true;
                                        $cardRankingEmoji = '👑';
                                        $cardRankBadgeClass = 'rank-gold';
                                        $cardRankingText = 'Gold - #1';
                                    } elseif ($loop->index === 1) {
                                        $cardAvatarClass = 'bg-gradient-to-r from-gray-300 to-gray-500 shadow-lg';
                                        $showCardRankingIcon = true;
                                        $cardRankingEmoji = '🥈';
                                        $cardRankBadgeClass = 'rank-silver';
                                        $cardRankingText = 'Silver - #2';
                                    } elseif ($loop->index === 2) {
                                        $cardAvatarClass = 'bg-gradient-to-r from-orange-400 to-orange-600 shadow-lg';
                                        $showCardRankingIcon = true;
                                        $cardRankingEmoji = '🥉';
                                        $cardRankBadgeClass = 'rank-bronze';
                                        $cardRankingText = 'Bronze - #3';
                                    } elseif ($showUserRanking) {
                                        $cardAvatarClass = 'bg-gradient-to-r from-blue-500 to-indigo-600';
                                        $showCardRankingIcon = true;
                                        $cardRankingEmoji = '👤';
                                        $cardRankBadgeClass = 'rank-user';
                                        $cardRankingText = 'Your Ranking - #' . ($loop->index + 1);
                                    } else {
                                        $cardAvatarClass = 'bg-gradient-to-r from-blue-500 to-purple-600';
                                    }
                                @endphp
                                <div class="w-12 h-12 {{ $cardAvatarClass }} rounded-full flex items-center justify-center mr-3 relative overflow-visible">
                                    @if($showCardRankingIcon)
                                        <div class="ranking-container {{ $cardRankBadgeClass }}" id="ranking-icon-card-{{ $index }}">
                                            <span class="ranking-emoji">{{ $cardRankingEmoji }}</span>
                                        </div>
                                    @endif
                                    <span class="text-white font-bold text-xl relative z-10 drop-shadow-lg">
                                        {{ strtoupper(substr($agent['name'], 0, 2)) }}
                                    </span>
                                </div>
                                    <div>
                                        <h4 class="font-semibold {{ $isAfterTopThree ? 'text-gray-700' : 'text-gray-900' }} flex items-center">
                                            {{ $agent['name'] }}
                                            @if($cardRankingText)
                                                <span class="ml-2 {{ $loop->index === 0 ? 'bg-yellow-100 text-yellow-800' : ($loop->index === 1 ? 'bg-gray-100 text-gray-800' : ($loop->index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }} text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                    <span class="mr-1">{{ $cardRankingEmoji }}</span>{{ $cardRankingText }}
                                                </span>
                                            @endif
                                        </h4>
                                        <p class="text-xs {{ $isAfterTopThree ? 'text-gray-600' : 'text-gray-500' }}">{{ $agent['transaction_count'] }} transactions</p>
    </div>
</div>
                            <div class="flex space-x-2">
                                <a href="{{ isset($agent['id']) ? route('rental-codes.agent-payroll', ['agentId' => $agent['id']]) : route('rental-codes.agent-payroll-by-name', ['agentName' => $agent['name']]) }}" 
                                   class="text-purple-600 hover:text-purple-800">
                                    <i class="fas fa-money-bill-wave"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                @php
                                    $cardTotalClass = '';
                                    $cardTotalEmoji = '';
                                    
                                    if ($loop->index === 0) {
                                        $cardTotalClass = 'text-yellow-600';
                                        $cardTotalEmoji = '👑';
                                    } elseif ($loop->index === 1) {
                                        $cardTotalClass = 'text-gray-600';
                                        $cardTotalEmoji = '🥈';
                                    } elseif ($loop->index === 2) {
                                        $cardTotalClass = 'text-orange-600';
                                        $cardTotalEmoji = '🥉';
                                    } elseif ($showUserRanking) {
                                        $cardTotalClass = 'text-blue-600';
                                        $cardTotalEmoji = '👤';
                                    } else {
                                        $cardTotalClass = 'text-gray-600';
                                    }
                                @endphp
                                <span class="text-sm {{ $isAfterTopThree ? 'text-gray-600' : $cardTotalClass }}">Total Earnings</span>
                                <span class="text-lg font-bold {{ $isAfterTopThree ? 'text-gray-700' : $cardTotalClass }} flex items-center">
                                    @if($cardTotalEmoji)
                                        <span class="mr-1">{{ $cardTotalEmoji }}</span>
                                    @endif
                                    £{{ number_format($agent['total_earnings'], 2) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-sm font-semibold text-green-900">£{{ number_format($agent['agent_earnings'], 2) }}</div>
                                    <div class="text-xs text-green-600">Agent (55%)</div>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-sm font-semibold text-blue-900">£{{ number_format($agent['agency_earnings'], 2) }}</div>
                                    <div class="text-xs text-blue-600">Agency (45%)</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center text-xs {{ $isAfterTopThree ? 'text-gray-600' : 'text-gray-500' }}">
                                <span>Avg: £{{ number_format($agent['avg_transaction_value'], 2) }}</span>
                                <span>{{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M') : 'N/A' }}</span>
                            </div>
                            
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500">Last Activity: {{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}</span>
                                    <a href="{{ route('rental-codes.agent-details', $agent['name']) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Agent Details Modal -->
<div id="agentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Agent Payroll Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modalContent" class="max-h-screen overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js configuration
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6B7280';

// Commission Cycle earnings chart (11th to 10th) - Total Earnings
@if(count($agentEarnings) > 0)
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($chartData['monthly_totals'])) !!},
        datasets: [{
            label: 'Total Earnings per Cycle (Agency + Agent)',
            data: {!! json_encode(array_values($chartData['monthly_totals'])) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                labels: {
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                callbacks: {
                    title: function(context) {
                        return 'Commission Cycle: ' + context[0].label;
                    },
                    label: function(context) {
                        return 'Total Earnings (100%): £' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    font: {
                        size: 10
                    }
                },
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '£' + value.toLocaleString();
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        }
    }
});

// Top agents chart
const agentsCtx = document.getElementById('agentsChart').getContext('2d');
const agentsChart = new Chart(agentsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_slice(array_keys($chartData['agent_comparison']), 0, 5)) !!},
        datasets: [{
            label: 'Earnings',
            data: {!! json_encode(array_slice(array_map(fn($x) => $x['total_earnings'], $chartData['agent_comparison']), 0, 5)) !!},
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(139, 92, 246, 0.8)'
            ],
            borderColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(139, 92, 246)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '£' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
@endif

// View toggle functionality
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardsBtn = document.getElementById('cardsViewBtn');
    
    if (view === 'table') {
        tableView.classList.remove('hidden');
        cardsView.classList.add('hidden');
        tableBtn.classList.add('bg-blue-100', 'text-blue-600');
        tableBtn.classList.remove('bg-gray-100', 'text-gray-600');
        cardsBtn.classList.add('bg-gray-100', 'text-gray-600');
        cardsBtn.classList.remove('bg-blue-100', 'text-blue-600');
    } else {
        tableView.classList.add('hidden');
        cardsView.classList.remove('hidden');
        cardsBtn.classList.add('bg-blue-100', 'text-blue-600');
        cardsBtn.classList.remove('bg-gray-100', 'text-gray-600');
        tableBtn.classList.add('bg-gray-100', 'text-gray-600');
        tableBtn.classList.remove('bg-blue-100', 'text-blue-600');
    }
}

// Filter toggle functionality
function toggleFilters() {
    const content = document.getElementById('filtersContent');
    const icon = document.getElementById('filterToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

// Commission cycle selection function
function selectCycle(startDate, endDate) {
    // Update the date inputs
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    
    // Submit the form automatically
    document.getElementById('filterForm').submit();
}

// Agent payroll modal
function showAgentDetails(agentName) {
    const agentData = {!! json_encode($agentEarnings) !!}[agentName];
    if (!agentData) return;
    
    document.getElementById('modalTitle').textContent = agentName + ' - Payroll Details';
    
    const transactions = agentData.transactions || [];
    const landlordBonuses = agentData.landlord_bonuses || [];
    
    // Calculate totals
    const rentalCodeEarnings = transactions.reduce((sum, t) => sum + parseFloat(t.agent_cut || 0), 0);
    const landlordBonusEarnings = landlordBonuses.reduce((sum, b) => sum + parseFloat(b.agent_commission || 0), 0);
    const marketingEarnings = transactions.reduce((sum, t) => sum + parseFloat(t.is_marketing_earnings ? t.agent_cut : 0), 0);
    const marketingDeductions = transactions.reduce((sum, t) => sum + parseFloat(t.marketing_deduction || 0), 0);
    const vatDeductions = transactions.reduce((sum, t) => sum + parseFloat(t.vat_amount || 0), 0);
    const totalEarnings = rentalCodeEarnings + landlordBonusEarnings;
    
    // Build rental codes HTML
    let rentalCodesHtml = '';
    if (transactions.length > 0) {
        transactions.forEach(transaction => {
            const isMarketingEarnings = transaction.is_marketing_earnings;
            const bgColor = isMarketingEarnings ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-gray-200';
            
            rentalCodesHtml += `
                <div class="p-4 ${bgColor} rounded-lg border mb-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${transaction.payment_method === 'Transfer' || transaction.payment_method === 'Card Machine' || transaction.payment_method === 'Card machine' ? 'bg-purple-100 text-purple-800' : (transaction.payment_method === 'Cash' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')}">
                                    ${transaction.payment_method === 'Transfer' ? '⚡ ' : ((transaction.payment_method === 'Card Machine' || transaction.payment_method === 'Card machine') ? '💳 ' : (transaction.payment_method === 'Cash' ? '💰 ' : ''))}${transaction.payment_method}
                                </span>
                                <span class="font-medium text-gray-900">${transaction.code}</span>
                                ${isMarketingEarnings ? '<span class="text-xs text-orange-600 font-medium">Marketing</span>' : ''}
                                ${transaction.paid ? '<span class="text-xs text-green-600 font-medium">✓ Paid</span>' : '<span class="text-xs text-yellow-600 font-medium">Pending</span>'}
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                Date: ${new Date(transaction.date).toLocaleDateString()}
                                ${transaction.client_count > 1 ? ` | Clients: ${transaction.client_count}` : ''}
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Total Fee:</span>
                                    <span class="font-semibold text-gray-900">£${parseFloat(transaction.total_fee).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Agent Cut:</span>
                                    <span class="font-semibold text-green-600">£${parseFloat(transaction.agent_cut).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Agency Cut:</span>
                                    <span class="font-semibold text-blue-600">£${parseFloat(transaction.agency_cut).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Base Commission:</span>
                                    <span class="font-semibold text-gray-700">£${parseFloat(transaction.base_commission).toFixed(2)}</span>
                                </div>
                            </div>
                            ${transaction.vat_amount > 0 || transaction.marketing_deduction > 0 ? `
                                <div class="mt-2 pt-2 border-t border-gray-200">
                                    <div class="text-xs text-gray-500">Deductions:</div>
                                    ${transaction.vat_amount > 0 ? `<div class="text-xs text-orange-600">VAT: £${parseFloat(transaction.vat_amount).toFixed(2)}</div>` : ''}
                                    ${transaction.marketing_deduction > 0 ? `<div class="text-xs text-red-600">Marketing: £${parseFloat(transaction.marketing_deduction).toFixed(2)}</div>` : ''}
                                    ${transaction.marketing_agent ? `<div class="text-xs text-blue-600">Marketing Agent: ${transaction.marketing_agent}</div>` : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        rentalCodesHtml = '<div class="text-center py-8 text-gray-500">No rental codes found</div>';
    }
    
    // Build landlord bonuses HTML
    let landlordBonusesHtml = '';
    if (landlordBonuses.length > 0) {
        landlordBonuses.forEach(bonus => {
            landlordBonusesHtml += `
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-gift mr-1"></i>${bonus.bonus_code}
                                </span>
                                <span class="font-medium text-gray-900">${bonus.property}</span>
                                <span class="text-xs ${bonus.status === 'paid' ? 'text-green-600' : 'text-yellow-600'}">${bonus.status === 'paid' ? '✓ Paid' : 'Pending'}</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                Landlord: ${bonus.landlord} | Client: ${bonus.client}
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Total Commission:</span>
                                    <span class="font-semibold text-gray-900">£${parseFloat(bonus.commission).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Agent Commission:</span>
                                    <span class="font-semibold text-green-600">£${parseFloat(bonus.agent_commission).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Split:</span>
                                    <span class="font-semibold text-blue-600">${bonus.bonus_split === '100_0' ? '100% Agent' : '55% Agent, 45% Agency'}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="font-semibold text-gray-700">${new Date(bonus.date).toLocaleDateString()}</span>
                                </div>
                            </div>
                            ${bonus.notes ? `<div class="mt-2 text-xs text-gray-500">Notes: ${bonus.notes}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        landlordBonusesHtml = '<div class="text-center py-8 text-gray-500">No landlord bonuses found</div>';
    }
    
    document.getElementById('modalContent').innerHTML = `
        <div class="space-y-6">
            <!-- Header with agent name and totals -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">${agentName}</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-900">£${parseFloat(totalEarnings).toFixed(2)}</div>
                        <div class="text-sm text-blue-600">Total Earnings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-900">£${parseFloat(rentalCodeEarnings).toFixed(2)}</div>
                        <div class="text-sm text-green-600">Rental Codes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-900">£${parseFloat(landlordBonusEarnings).toFixed(2)}</div>
                        <div class="text-sm text-purple-600">Landlord Bonuses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-900">£${parseFloat(marketingEarnings).toFixed(2)}</div>
                        <div class="text-sm text-orange-600">Marketing Earnings</div>
                    </div>
                </div>
                ${marketingDeductions > 0 || vatDeductions > 0 ? `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600 mb-2">Deductions:</div>
                        <div class="flex space-x-4">
                            ${marketingDeductions > 0 ? `<span class="text-sm text-red-600">Marketing: £${parseFloat(marketingDeductions).toFixed(2)}</span>` : ''}
                            ${vatDeductions > 0 ? `<span class="text-sm text-yellow-600">VAT: £${parseFloat(vatDeductions).toFixed(2)}</span>` : ''}
                        </div>
                    </div>
                ` : ''}
            </div>
            
            <!-- Rental Codes Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-contract text-blue-600 mr-2"></i>
                    Rental Codes (${transactions.length})
                </h3>
                ${rentalCodesHtml}
            </div>
            
            <!-- Landlord Bonuses Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-gift text-green-600 mr-2"></i>
                    Landlord Bonuses (${landlordBonuses.length})
                </h3>
                ${landlordBonusesHtml}
            </div>
        </div>
    `;
    
    document.getElementById('agentModal').classList.remove('hidden');
}


function closeModal() {
    document.getElementById('agentModal').classList.add('hidden');
}

// Print payroll function
function printPayroll(agentName) {
    const agentData = {!! json_encode($agentEarnings) !!}[agentName];
    if (!agentData) return;
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payroll - ${agentName}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #333; margin-bottom: 10px; }
                .header p { color: #666; }
                .summary { display: flex; justify-content: space-around; margin-bottom: 30px; }
                .summary-item { text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
                .summary-item h3 { margin: 0; color: #333; }
                .summary-item p { margin: 5px 0 0 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; }
                .total-row { font-weight: bold; background-color: #f9f9f9; }
                .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Agent Payroll Report</h1>
                <p><strong>Agent:</strong> ${agentName}</p>
                <p><strong>Period:</strong> Approved rentals up to 10th of each month</p>
                <p><strong>Generated:</strong> ${new Date().toLocaleDateString()}</p>
            </div>
            
            <div class="summary">
                <div class="summary-item">
                    <h3>£${parseFloat(agentData.total_earnings).toFixed(2)}</h3>
                    <p>Total Commission</p>
                </div>
                <div class="summary-item">
                    <h3>£${parseFloat(agentData.agent_earnings).toFixed(2)}</h3>
                    <p>Agent Earnings (55%)</p>
                </div>
                <div class="summary-item">
                    <h3>£${parseFloat(agentData.agency_earnings).toFixed(2)}</h3>
                    <p>Agency Earnings (45%)</p>
                </div>
                <div class="summary-item">
                    <h3>${agentData.transaction_count}</h3>
                    <p>Transactions</p>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Rental Code</th>
                        <th>Date</th>
                        <th>Total Fee</th>
                        <th>Agent Cut</th>
                        <th>Agency Cut</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${agentData.transactions.map(transaction => `
                        <tr>
                            <td>${transaction.code}</td>
                            <td>${new Date(transaction.date).toLocaleDateString()}</td>
                            <td>${transaction.payment_method === 'Transfer' ? '⚡ ' : ((transaction.payment_method === 'Card Machine' || transaction.payment_method === 'Card machine') ? '💳 ' : (transaction.payment_method === 'Cash' ? '💰 ' : ''))}£${parseFloat(transaction.total_fee).toFixed(2)}</td>
                            <td>£${parseFloat(transaction.agent_cut).toFixed(2)}</td>
                            <td>£${parseFloat(transaction.agency_cut).toFixed(2)}</td>
                            <td>${transaction.status}</td>
                        </tr>
                    `).join('')}
                    <tr class="total-row">
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>£${parseFloat(agentData.agent_earnings).toFixed(2)}</strong></td>
                        <td><strong>£${parseFloat(agentData.agency_earnings).toFixed(2)}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="footer">
                <p>This report shows approved rentals up to the 10th of each month for payroll purposes.</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

// Export functionality
function exportToExcel() {
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = '';
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const rowData = cells.map(cell => {
            let text = cell.textContent.trim();
            text = text.replace(/\s+/g, ' ');
            if (text.includes(',') || text.includes('"')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            return text;
        });
        csv += rowData.join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'agent_earnings_{{ $endDate }}.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initialize filters state
document.addEventListener('DOMContentLoaded', function() {
    const filtersContent = document.getElementById('filtersContent');
    const filterToggleIcon = document.getElementById('filterToggleIcon');
    
    // Ensure filters start collapsed
    if (filtersContent) {
        filtersContent.style.display = 'none';
    }
    
    // Ensure icon starts as chevron-down
    if (filterToggleIcon) {
        filterToggleIcon.className = 'fas fa-chevron-down';
    }
    
    // Immediate crown fallback check
    function checkCrownFallback() {
        // Check if FontAwesome is loaded by testing a simple icon
        const testDiv = document.createElement('div');
        const testIcon = document.createElement('i');
        testIcon.className = 'fas fa-user';
        // keep off-screen to avoid layout shift
        testDiv.style.position = 'absolute';
        testDiv.style.left = '-9999px';
        testDiv.appendChild(testIcon);
        document.body.appendChild(testDiv);
        let isFontAwesomeLoaded = false;
        try {
            const fontFamily = window.getComputedStyle(testIcon).fontFamily || '';
            isFontAwesomeLoaded = fontFamily.includes('Font Awesome') || fontFamily.includes('FontAwesome');
        } catch (e) {
            isFontAwesomeLoaded = false;
        }
        document.body.removeChild(testDiv);
        
        console.log('FontAwesome loaded:', isFontAwesomeLoaded);
        
        if (!isFontAwesomeLoaded) {
            console.log('FontAwesome not detected, showing emoji fallbacks');
            // Emoji fallbacks are already shown by default CSS
        } else {
            console.log('FontAwesome detected, switching to FontAwesome icons');
            // Add class to enable FontAwesome icons
            document.body.classList.add('fontawesome-loaded');
        }
    }
    
    // Run immediately
    checkCrownFallback();
    
    // Debug: Check if ranking containers exist
    setTimeout(() => {
        console.log('Debug: Checking for ranking containers...');
        const allRankingContainers = document.querySelectorAll('.ranking-container');
        console.log('All ranking containers found:', allRankingContainers.length);
        
        // Also check for any elements with crown-related classes
        const crownElements = document.querySelectorAll('[class*="crown"]');
        console.log('All crown-related elements:', crownElements.length);
        
        // Check for agent rows and cards
        const agentRows = document.querySelectorAll('tbody tr');
        const agentCards = document.querySelectorAll('#cardsView .grid > div');
        console.log('Agent rows found:', agentRows.length);
        console.log('Agent cards found:', agentCards.length);
        
        // Check if there are any agents at all
        const agentNames = document.querySelectorAll('[class*="agent"], [class*="Agent"]');
        console.log('Elements with "agent" in class:', agentNames.length);
        
        allRankingContainers.forEach((container, index) => {
            console.log(`Container ${index}:`, {
                element: container,
                parent: container.parentElement,
                visible: container.offsetParent !== null,
                display: window.getComputedStyle(container).display,
                position: window.getComputedStyle(container).position
            });
        });
    }, 500);
    
    // Check ranking icon visibility after FontAwesome detection
    setTimeout(() => {
        console.log('Checking ranking icon visibility...');
        const rankingContainers = document.querySelectorAll('.ranking-container');
        console.log('Found ranking containers:', rankingContainers.length);
        
        rankingContainers.forEach((container, index) => {
            const icon = container.querySelector('i');
            const fallback = container.querySelector('.ranking-fallback');
            
            console.log(`Ranking container ${index}:`, {
                container: container,
                icon: icon,
                fallback: fallback,
                iconVisible: icon ? icon.offsetParent !== null : false,
                fallbackVisible: fallback ? fallback.offsetParent !== null : false
            });
            
            if (icon && fallback) {
                // Check if FontAwesome icon is working
                const iconStyle = window.getComputedStyle(icon);
                const isIconWorking = iconStyle.fontFamily.includes('Font Awesome') || 
                                    iconStyle.fontFamily.includes('FontAwesome');
                
                if (isIconWorking && document.body.classList.contains('fontawesome-loaded')) {
                    console.log('FontAwesome icon working, hiding fallback');
                    icon.style.display = 'block';
                    fallback.style.display = 'none';
                } else {
                    console.log('FontAwesome icon not working, showing fallback');
                    icon.style.display = 'none';
                    fallback.style.display = 'block';
                }
            }
        });
    }, 1000);
});
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .shadow-sm, .shadow-lg {
        box-shadow: none !important;
    }
    
    .border {
        border: 1px solid #000 !important;
    }
}

/* Ranking container styles */
.ranking-container {
    z-index: 5;
    position: absolute;
    top: -10px;
    right: -10px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Gold ranking (1st place) */
.ranking-container.rank-gold { background: #FCD34D; border: 2px solid #F59E0B; }
/* Silver ranking (2nd place) */
.ranking-container.rank-silver { background: #E5E7EB; border: 2px solid #9CA3AF; }
/* Bronze ranking (3rd place) */
.ranking-container.rank-bronze { background: #FED7AA; border: 2px solid #EA580C; }
/* User ranking (4th place for non-admin) */
.ranking-container.rank-user { background: #DBEAFE; border: 2px solid #3B82F6; }

/* Crown visibility fixes */
.crown-container {
    z-index: 5;
    position: absolute;
    top: -10px;
    right: -10px;
    width: 20px;
    height: 20px;
    background: #FCD34D;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    border: 2px solid #F59E0B;
}

.crown-container i {
    color: #92400E;
    font-size: 12px;
}

/* Ranking emoji styles */
.ranking-container .ranking-emoji { font-size: 12px; line-height: 1; }

/* Ensure crown is visible */
.relative {
    overflow: visible !important;
}

/* Remove FontAwesome fallback logic since emojis are primary */

/* === VISIBILITY FIXES === */
.crown-container,
.ranking-container {
    display: flex !important;
    visibility: visible !important;
}

/* === INITIALS VISIBILITY === */
.relative span {
    position: relative !important;
    z-index: 10 !important;
    font-weight: 900 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.45) !important;
}

/* === GOLD RANK === */
.ranking-row-gold {
    background: linear-gradient(135deg, #fbbf24, #f59e0b, #b45309) !important;
    border-left: 4px solid #92400e !important;
    box-shadow: 0 8px 20px rgba(217, 119, 6, 0.35) !important;
}

.ranking-row-gold td {
    background: transparent !important;
    border-color: rgba(146, 64, 14, 0.25) !important;
    color: #1e1200 !important; /* 30% darker than #2d1b00 */
}

/* === SILVER RANK === */
.ranking-row-silver {
    background: linear-gradient(135deg, #d1d5db, #9ca3af, #6b7280) !important;
    border-left: 4px solid #4b5563 !important;
    box-shadow: 0 8px 20px rgba(107, 114, 128, 0.35) !important;
}

.ranking-row-silver td {
    background: transparent !important;
    border-color: rgba(17, 112, 247, 0.2) !important;
    color:rgb(255, 134, 14) !important; /* 30% darker than #3b2d1d */
}

.ranking-row-silver .total-earnings,
.ranking-row-silver .ranking-container i,
.ranking-row-silver td .text-lg {
    color:rgb(245,170,21) !important;
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}
/* Darken the main name text while keeping the badge light */
.text-sm.font-semibold.text-gray-900 {
    color: #0a0500 !important; /* deeper, richer dark tone */
    font-weight: 900 !important;
}

/* Keep the badge visually lighter for contrast */
.text-sm.font-semibold.text-gray-900 span {
    color: #1f2937 !important; /* restore neutral gray for badge */
    font-weight: 600 !important;
}
    push
.ranking-row-silver td .font-bold {
    color: #1a1005 !important; /* slightly deeper tone */
    font-weight: 900 !important;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5) !important;
}

/* === BRONZE RANK === */
.ranking-row-bronze {
    background: linear-gradient(135deg, #cd7f32, #8b5a2b, #5a3a1b) !important;
    border-left: 4px solid #3a2414 !important;
    box-shadow: 0 8px 20px rgba(139, 69, 19, 0.45) !important;
}

.ranking-row-bronze td {
    background: transparent !important;
    border-color: rgba(90, 30, 15, 0.3) !important;
    color: #1e1200 !important; /* 30% darker than #2d1b00 */
}

/* === CARD VARIANTS === */
.ranking-row-gold.shadow-xl {
    background: linear-gradient(145deg, #fbbf24, #f59e0b, #b45309) !important;
    border: 2px solid #92400e !important;
    color: #1e1200 !important;
}

.ranking-row-silver.shadow-xl {
    background: linear-gradient(145deg, #d1d5db, #9ca3af, #6b7280) !important;
    border: 2px solid #4b5563 !important;
    color: #281c10 !important;
}

.ranking-row-bronze.shadow-xl {
    background: linear-gradient(145deg, #cd7f32, #8b5a2b, #5a3a1b) !important;
    border: 2px solid #3a2414 !important;
    color: #1e1200 !important;
}

/* === TOP EARNER GRADIENT === */
.from-yellow-400 {
    --tw-gradient-from: #facc15;
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(250, 204, 21, 0));
}

.to-yellow-600 {
    --tw-gradient-to: #ca8a04;
}

</style>
@endsection

