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
                        <h1 class="text-3xl font-bold text-gray-900">Agent Earnings Analytics</h1>
                        <p class="text-gray-600 mt-1">Comprehensive earnings analysis and performance insights</p>
                    </div>
    </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
        <a href="{{ route('rental-codes.index') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Back to Rental Codes
        </a>
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
                        Advanced Filters
                    </h3>
                    <button onclick="toggleFilters()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-chevron-down" id="filterToggleIcon"></i>
                    </button>
                </div>
    </div>
            <div class="p-6" id="filtersContent">
                <form method="GET" action="{{ route('rental-codes.agent-earnings') }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i>Start Date
                            </label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>
            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i>End Date
                            </label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i>Agent Filter
                            </label>
                            <select name="agent_search" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">All Agents</option>
                                @foreach($agentEarnings as $agent)
                                    <option value="{{ $agent['name'] }}" {{ $agentSearch == $agent['name'] ? 'selected' : '' }}>
                                        {{ $agent['name'] }} ({{ $agent['transaction_count'] }} transactions)
                                    </option>
                                @endforeach
                            </select>
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
            <!-- Monthly Earnings Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Monthly Earnings Trend
                </h3>
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

<!-- Agent Earnings Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Agent Earnings Breakdown</h3>
        <p class="text-sm text-gray-500 mt-1">
                            @if($startDate || $endDate)
                                Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }}
                                — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            @else
                                All Time Data
                            @endif
            @if($status) • Status: {{ ucfirst($status) }} @endif
            @if($paymentMethod) • Payment: {{ $paymentMethod }} @endif
        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">{{ count($agentEarnings) }} agents</span>
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
                                Transactions
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marketing Deductions
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marketing Earnings
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paid Amount
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Outstanding
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
                        @forelse($agentEarnings as $agent)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-white font-bold text-lg">
                                            {{ strtoupper(substr($agent['name'], 0, 2)) }}
                                        </span>
                            </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $agent['name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $agent['transaction_count'] }} total transactions</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    £{{ number_format($agent['agent_earnings'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                                    55% of commission
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    £{{ number_format($agent['agency_earnings'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                                    45% of commission
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-lg font-bold text-gray-900">
                            £{{ number_format($agent['total_earnings'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">
                                    {{ $agent['transaction_count'] }} total
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $agent['transaction_count'] }} transactions
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    £{{ number_format($agent['marketing_deductions'], 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    £30 per different marketing agent
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    £{{ number_format($agent['marketing_agent_earnings'], 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Marketing agent earnings
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    £{{ number_format($agent['paid_amount'], 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Already paid
                                </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $agent['outstanding_amount'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    £{{ number_format($agent['outstanding_amount'], 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $agent['outstanding_amount'] > 0 ? 'Outstanding' : 'Up to date' }}
                                </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                                    {{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('rental-codes.agent-details', $agent['name']) }}" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-user-tie mr-1"></i>Agent Details
                                    </a>
                                    <button onclick="showAgentDetails('{{ $agent['name'] }}')" 
                                            class="text-green-600 hover:text-green-800 font-medium">
                                        <i class="fas fa-eye mr-1"></i>Quick View
                                    </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
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
                    @foreach($agentEarnings as $agent)
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">
                                        {{ strtoupper(substr($agent['name'], 0, 2)) }}
                                    </span>
                                </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $agent['name'] }}</h4>
                                        <p class="text-xs text-gray-500">{{ $agent['transaction_count'] }} transactions</p>
    </div>
</div>
                            <div class="flex space-x-2">
                                <a href="{{ route('rental-codes.agent-details', $agent['name']) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-user-tie"></i>
                                </a>
                                <button onclick="showAgentDetails('{{ $agent['name'] }}')" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-v"></i>
        </button>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Earnings</span>
                                <span class="text-lg font-bold text-gray-900">£{{ number_format($agent['total_earnings'], 2) }}</span>
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
                            
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Avg: £{{ number_format($agent['avg_transaction_value'], 2) }}</span>
                                <span>{{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M') : 'N/A' }}</span>
                            </div>
                            
                            @if($agent['marketing_deductions'] > 0 || $agent['marketing_agent_earnings'] > 0)
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-orange-600">Marketing Deductions: £{{ number_format($agent['marketing_deductions'], 2) }}</span>
                                    <span class="text-green-600">Marketing Earnings: £{{ number_format($agent['marketing_agent_earnings'], 2) }}</span>
    </div>
</div>
@endif

                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-blue-600">Paid: £{{ number_format($agent['paid_amount'], 2) }}</span>
                                    <span class="{{ $agent['outstanding_amount'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Outstanding: £{{ number_format($agent['outstanding_amount'], 2) }}
                                    </span>
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
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Agent Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modalContent" class="max-h-96 overflow-y-auto">
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

// Monthly earnings chart
@if(count($agentEarnings) > 0)
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($chartData['monthly_totals'])) !!},
        datasets: [{
            label: 'Monthly Earnings',
            data: {!! json_encode(array_values($chartData['monthly_totals'])) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
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

// Agent details modal
function showAgentDetails(agentName) {
    const agentData = {!! json_encode($agentEarnings) !!}[agentName];
    if (!agentData) return;
    
    document.getElementById('modalTitle').textContent = agentName + ' - Detailed Analysis';
    
     const transactions = agentData.transactions.slice(0, 10); // Show last 10 transactions
     let transactionsHtml = '';
     
     transactions.forEach(transaction => {
         transactionsHtml += `
             <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2">
                 <div class="flex items-center">
                     <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${transaction.payment_method === 'Transfer' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'}">
                         ${transaction.payment_method}
                     </span>
                     <span class="ml-3 text-sm font-medium text-gray-900">${transaction.code}</span>
                 </div>
                 <div class="text-right">
                     <div class="text-sm font-semibold text-gray-900">£${parseFloat(transaction.total_fee).toFixed(2)}</div>
                     <div class="text-xs text-gray-500">Agency: £${parseFloat(transaction.agency_cut).toFixed(2)} | Agent: £${parseFloat(transaction.agent_cut).toFixed(2)}</div>
                     ${transaction.vat_amount > 0 ? '<div class="text-xs text-orange-600">VAT: £' + parseFloat(transaction.vat_amount).toFixed(2) + '</div>' : ''}
                     ${transaction.marketing_deduction > 0 ? '<div class="text-xs text-red-600">Marketing Deduction: £' + parseFloat(transaction.marketing_deduction).toFixed(2) + '</div>' : ''}
                     ${transaction.is_marketing_earnings ? '<div class="text-xs text-green-600">Marketing Earnings</div>' : ''}
                     ${transaction.client_count > 1 ? '<div class="text-xs text-blue-600">Multiple Clients: ' + transaction.client_count + '</div>' : ''}
                     ${transaction.paid ? '<div class="text-xs text-green-600">✓ Paid</div>' : '<div class="text-xs text-orange-600">Pending Payment</div>'}
                 </div>
             </div>
         `;
     });
     
     document.getElementById('modalContent').innerHTML = `
         <div class="space-y-6">
             <div class="grid grid-cols-3 gap-4">
                 <div class="text-center p-4 bg-blue-50 rounded-lg">
                     <div class="text-2xl font-bold text-blue-900">£${parseFloat(agentData.total_earnings).toFixed(2)}</div>
                     <div class="text-sm text-blue-600">Total Commission</div>
                 </div>
                 <div class="text-center p-4 bg-green-50 rounded-lg">
                     <div class="text-2xl font-bold text-green-900">£${parseFloat(agentData.agent_earnings).toFixed(2)}</div>
                     <div class="text-sm text-green-600">Agent Earnings</div>
                 </div>
                 <div class="text-center p-4 bg-purple-50 rounded-lg">
                     <div class="text-2xl font-bold text-purple-900">${agentData.transaction_count}</div>
                     <div class="text-sm text-purple-600">Transactions</div>
                 </div>
             </div>
             
             <div>
                 <h4 class="font-semibold text-gray-900 mb-3">Recent Transactions</h4>
                 ${transactionsHtml}
             </div>
         </div>
     `;
    
    document.getElementById('agentModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('agentModal').classList.add('hidden');
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
    filtersContent.style.display = 'block';
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
</style>
@endsection
