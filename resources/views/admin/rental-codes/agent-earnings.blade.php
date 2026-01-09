@extends('layouts.admin')

@section('page-title', 'Agent Earnings Analytics')

@section('content')
<style>
/* TrueHold Premium Design Variables */
:root {
    --primary-navy: #1e3a5f;
    --navy-dark: #152a45;
    --navy-light: #2d5280;
    --gold: #d4af37;
    --gold-light: #e8c55c;
    --gold-dark: #b8941f;
    --white: #ffffff;
    --off-white: #f8f9fa;
    --light-gray: #e9ecef;
    --gray: #6c757d;
    --text-dark: #212529;
    --shadow-sm: 0 2px 4px rgba(30, 58, 95, 0.08);
    --shadow-md: 0 4px 12px rgba(30, 58, 95, 0.12);
    --shadow-lg: 0 8px 24px rgba(30, 58, 95, 0.15);
    --transition: all 0.3s ease;
}

.truehold-page {
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--navy-dark) 100%);
    min-height: 100vh;
    padding: 24px;
}

.truehold-header {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.98));
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(212, 175, 55, 0.1);
}

.header-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
}

.header-icon i {
    color: var(--white);
    font-size: 24px;
}

.header-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary-navy);
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.header-subtitle {
    color: var(--gray);
    font-size: 15px;
}

.date-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
}

.btn-truehold {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    color: var(--white);
    box-shadow: 0 4px 12px rgba(30, 58, 95, 0.25);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 58, 95, 0.35);
}

.btn-secondary {
    background: var(--white);
    color: var(--primary-navy);
    border: 2px solid var(--light-gray);
}

.btn-secondary:hover {
    border-color: var(--gold);
    color: var(--gold);
}

.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: var(--white);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
}

.truehold-card {
    background: var(--white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--light-gray);
    margin-bottom: 24px;
}

.card-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--light-gray);
    margin-bottom: 24px;
}

.card-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i {
    color: var(--gold);
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.summary-card {
    background: linear-gradient(135deg, var(--white), var(--off-white));
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-md);
    border: 2px solid transparent;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--gold), var(--gold-light));
}

.summary-card:hover {
    border-color: var(--gold);
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.summary-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gray);
    margin-bottom: 8px;
}

.summary-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--primary-navy);
    line-height: 1;
}

.summary-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
}

.summary-icon i {
    color: var(--white);
    font-size: 20px;
}

.filter-section {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--light-gray);
    margin-bottom: 24px;
}

.filter-header {
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.filter-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--white);
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-toggle {
    color: var(--white);
    font-size: 18px;
    transition: var(--transition);
}

.filter-content {
    padding: 24px;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-input,
.form-select {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 2px solid var(--light-gray);
    font-size: 14px;
    transition: var(--transition);
    background: var(--white);
    color: var(--text-dark);
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
}

.cycle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.cycle-btn {
    padding: 16px;
    border-radius: 12px;
    border: 2px solid var(--light-gray);
    background: var(--white);
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.cycle-btn:hover {
    border-color: var(--gold);
    transform: translateY(-2px);
}

.cycle-btn.active {
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    border-color: var(--gold-dark);
    color: var(--white);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
}

.cycle-btn.current {
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    border-color: var(--navy-dark);
    color: var(--white);
}

.agent-table {
    width: 100%;
    border-collapse: collapse;
}

.agent-table thead {
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
}

.agent-table th {
    padding: 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--white);
}

.agent-table tbody tr {
    border-bottom: 1px solid var(--light-gray);
    transition: var(--transition);
}

.agent-table tbody tr:hover {
    background: rgba(212, 175, 55, 0.05);
}

.agent-table td {
    padding: 16px;
}

.agent-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: var(--white);
    box-shadow: var(--shadow-md);
}

.rank-gold {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4) !important;
}

.rank-silver {
    background: linear-gradient(135deg, #d1d5db, #9ca3af);
    box-shadow: 0 4px 12px rgba(156, 163, 175, 0.4) !important;
}

.rank-bronze {
    background: linear-gradient(135deg, #cd7f32, #8b5a2b);
    box-shadow: 0 4px 12px rgba(205, 127, 50, 0.4) !important;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

.badge-gold {
    background: rgba(251, 191, 36, 0.15);
    color: #f59e0b;
}

.badge-silver {
    background: rgba(156, 163, 175, 0.15);
    color: #6b7280;
}

.badge-bronze {
    background: rgba(205, 127, 50, 0.15);
    color: #8b5a2b;
}

.view-toggle {
    display: flex;
    gap: 8px;
}

.toggle-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    border: 2px solid var(--light-gray);
    background: var(--white);
}

.toggle-btn.active {
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    border-color: var(--gold-dark);
    color: var(--white);
}

.agent-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.agent-card {
    background: var(--white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-md);
    border: 2px solid transparent;
    transition: var(--transition);
}

.agent-card:hover {
    border-color: var(--gold);
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.loading-screen {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    color: var(--gray);
}

.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--light-gray);
    border-top-color: var(--gold);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .truehold-header {
        padding: 20px;
    }
    
    .header-title {
        font-size: 22px;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .cycle-grid {
        grid-template-columns: 1fr;
    }
    
    .agent-table {
        font-size: 14px;
    }
    
    .agent-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="truehold-page">
    <!-- Header -->
    <div class="truehold-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h1 class="header-title">
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
                    <p class="header-subtitle">
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
                    @if($startDate && $endDate)
                        <div style="margin-top: 12px;">
                            <span class="date-badge">
                                <i class="fas fa-calendar-alt"></i>
                                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                <span style="opacity: 0.8;">({{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days)</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                @if($isPayrollView)
                    <a href="{{ route('rental-codes.agent-earnings') }}" class="btn-truehold btn-secondary">
                        <i class="fas fa-arrow-left"></i>Back to All Agents
                    </a>
                @else
                    <a href="{{ route('rental-codes.index') }}" class="btn-truehold btn-secondary">
                        <i class="fas fa-arrow-left"></i>Back to Rental Codes
                    </a>
                @endif
                @if(count($agentEarnings) > 0)
                    <button onclick="exportToExcel()" class="btn-truehold btn-success">
                        <i class="fas fa-file-excel"></i>Export Excel
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Dashboard -->
    @if(!$agentSearch)
        <div class="summary-grid">
            <div class="summary-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="summary-label">Total Agents</div>
                        <div class="summary-value">{{ $summary['total_agents'] }}</div>
                    </div>
                    <div class="summary-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="summary-label">Total Earnings</div>
                        <div class="summary-value" style="color: #10b981;">£{{ number_format($summary['total_earnings'], 2) }}</div>
                    </div>
                    <div class="summary-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-pound-sign"></i>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="summary-label">Total Transactions</div>
                        <div class="summary-value">{{ $summary['total_transactions'] }}</div>
                    </div>
                    <div class="summary-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="summary-label">Avg per Agent</div>
                        <div class="summary-value">£{{ number_format($summary['avg_earnings_per_agent'], 2) }}</div>
                    </div>
                    <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Advanced Filters -->
    <div class="filter-section">
        <div class="filter-header" onclick="toggleFilters()">
            <h3>
                <i class="fas fa-filter"></i>
                @if($isPayrollView) Payroll Filters @else Advanced Filters @endif
            </h3>
            <i class="fas fa-chevron-down filter-toggle" id="filterToggleIcon"></i>
        </div>
        <div class="filter-content" id="filtersContent" style="display: none;">
            @if($isPayrollView)
                <div style="background: rgba(59, 130, 246, 0.1); border: 2px solid rgba(59, 130, 246, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 18px; margin-top: 2px;"></i>
                        <div>
                            <h4 style="font-weight: 600; color: var(--primary-navy); margin-bottom: 8px;">Payroll View</h4>
                            <p style="color: var(--gray); font-size: 14px;">
                                This view shows only <strong>approved</strong> rentals that this agent participated in (as rental agent or marketing agent). Outstanding amounts show what the agent is owed from unpaid rentals.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <form method="GET" action="{{ route('rental-codes.agent-earnings') }}" id="filterForm">
                <!-- Quick Commission Cycle Selector -->
                <div style="background: rgba(212, 175, 55, 0.1); border: 2px solid rgba(212, 175, 55, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <label style="font-weight: 600; color: var(--primary-navy); font-size: 15px;">
                            <i class="fas fa-calendar-check" style="color: var(--gold);"></i> Quick Select Commission Cycle (11th to 10th)
                        </label>
                        <span style="font-size: 12px; color: var(--gray);">Click to apply</span>
                    </div>
                    
                    <div class="cycle-grid">
                        @php
                            $currentDate = now();
                            $cycles = [];
                            
                            for ($i = 0; $i < 8; $i++) {
                                $cycleDate = $currentDate->copy()->subMonths($i);
                                
                                if ($cycleDate->day <= 10) {
                                    $cycleStart = $cycleDate->copy()->subMonthNoOverflow()->day(11);
                                    $cycleEnd = $cycleDate->copy()->day(10);
                                } else {
                                    $cycleStart = $cycleDate->copy()->day(11);
                                    $cycleEnd = $cycleDate->copy()->addMonthNoOverflow()->day(10);
                                }
                                
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
                                    class="cycle-btn {{ $cycle['isSelected'] ? 'active' : ($cycle['isCurrent'] ? 'current' : '') }}">
                                <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">
                                    @if($cycle['isSelected'])
                                        <i class="fas fa-check-circle" style="margin-right: 6px;"></i>
                                    @elseif($cycle['isCurrent'])
                                        <i class="fas fa-star" style="margin-right: 6px;"></i>
                                    @endif
                                    {{ $cycle['shortLabel'] }}
                                </div>
                                <div style="font-size: 11px; opacity: 0.8;">{{ $cycle['label'] }}</div>
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Presets -->
                    <div style="border-top: 2px solid rgba(212, 175, 55, 0.2); padding-top: 16px; margin-top: 16px;">
                        <div style="font-size: 12px; font-weight: 600; color: var(--gray); margin-bottom: 12px;">Quick Presets:</div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                            @php
                                $last3MonthsStart = $currentDate->copy()->subMonths(3);
                                $last3MonthsEnd = $currentDate->toDateString();
                                $last6MonthsStart = $currentDate->copy()->subMonths(6);
                                $last6MonthsEnd = $currentDate->toDateString();
                                $thisYearStart = $currentDate->copy()->startOfYear();
                                $thisYearEnd = $currentDate->toDateString();
                                $lastYearStart = $currentDate->copy()->subYear()->startOfYear();
                                $lastYearEnd = $currentDate->copy()->subYear()->endOfYear();
                            @endphp
                            
                            <button type="button" onclick="selectCycle('{{ $last3MonthsStart->toDateString() }}', '{{ $last3MonthsEnd }}')" 
                                    class="btn-truehold btn-secondary" style="font-size: 12px; padding: 10px;">
                                <i class="fas fa-calendar-week"></i>Last 3 Months
                            </button>
                            
                            <button type="button" onclick="selectCycle('{{ $last6MonthsStart->toDateString() }}', '{{ $last6MonthsEnd }}')" 
                                    class="btn-truehold btn-secondary" style="font-size: 12px; padding: 10px;">
                                <i class="fas fa-calendar"></i>Last 6 Months
                            </button>
                            
                            <button type="button" onclick="selectCycle('{{ $thisYearStart->toDateString() }}', '{{ $thisYearEnd }}')" 
                                    class="btn-truehold btn-secondary" style="font-size: 12px; padding: 10px;">
                                <i class="fas fa-calendar-alt"></i>This Year
                            </button>
                            
                            <button type="button" onclick="selectCycle('{{ $lastYearStart->toDateString() }}', '{{ $lastYearEnd->toDateString() }}')" 
                                    class="btn-truehold btn-secondary" style="font-size: 12px; padding: 10px;">
                                <i class="fas fa-history"></i>Last Year
                            </button>
                        </div>
                    </div>
                </div>

                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>Start Date
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>End Date
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>Agent Filter
                        </label>
                        @if($isPayrollView)
                            <div class="form-input" style="background: var(--off-white); color: var(--primary-navy); font-weight: 600;">
                                <i class="fas fa-user-check" style="color: #10b981; margin-right: 8px;"></i>
                                {{ $agentSearch }} <span style="opacity: 0.7; font-weight: 400;">(Payroll View)</span>
                            </div>
                            <input type="hidden" name="agent_search" value="{{ $agentSearch }}">
                        @elseif(auth()->check() && auth()->user()->role === 'admin')
                            <select name="agent_search" class="form-select">
                                <option value="">All Agents</option>
                                @foreach($agentEarnings as $agent)
                                    <option value="{{ $agent['name'] }}" {{ $agentSearch == $agent['name'] ? 'selected' : '' }}>
                                        {{ $agent['name'] }} ({{ $agent['transaction_count'] }} transactions)
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="form-input" style="background: rgba(59, 130, 246, 0.1); color: var(--primary-navy); font-weight: 600;">
                                <i class="fas fa-user-shield" style="color: #3b82f6; margin-right: 8px;"></i>
                                {{ auth()->check() ? auth()->user()->name : 'Guest' }} <span style="opacity: 0.7; font-weight: 400;">(Your Payroll Only)</span>
                            </div>
                            @auth
                                <input type="hidden" name="agent_search" value="{{ auth()->user()->name }}">
                            @endauth
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-bullhorn"></i>Marketing Agent Filter
                        </label>
                        <select name="marketing_agent_filter" class="form-select">
                            <option value="">All Agents</option>
                            <option value="marketing_only" {{ $marketingAgentFilter == 'marketing_only' ? 'selected' : '' }}>Marketing Agents Only</option>
                            <option value="rent_only" {{ $marketingAgentFilter == 'rent_only' ? 'selected' : '' }}>Rent Agents Only</option>
                            <option value="both" {{ $marketingAgentFilter == 'both' ? 'selected' : '' }}>Both Rent & Marketing</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-check-circle"></i>Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>Approved</option>
                            <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $status==='cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-credit-card"></i>Payment Method
                        </label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="Cash" {{ $paymentMethod==='Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Transfer" {{ $paymentMethod==='Transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn-truehold btn-primary">
                        <i class="fas fa-search"></i>Apply Filters
                    </button>
                    <a href="{{ route('rental-codes.agent-earnings') }}" class="btn-truehold btn-secondary">
                        <i class="fas fa-times"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Earnings Chart -->
    @if(!$agentSearch && count($agentEarnings) > 0)
        <div class="truehold-card">
            <div class="card-title">
                <i class="fas fa-chart-line"></i>
                Total Earnings Over Time (Monthly)
            </div>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    @endif

    <!-- Agent Earnings Table/Cards -->
    <div class="truehold-card">
        <div class="card-header">
            <div>
                <div class="card-title">
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
                </div>
                <p style="color: var(--gray); font-size: 14px; margin-top: 8px;">
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
            <div style="display: flex; align-items: center; gap: 16px;">
                <span style="font-size: 14px; color: var(--gray);">
                    @if($isPayrollView)
                        {{ count($agentEarnings) }} commission record{{ count($agentEarnings) !== 1 ? 's' : '' }}
                    @else
                        {{ count($agentEarnings) }} agent{{ count($agentEarnings) !== 1 ? 's' : '' }}
                    @endif
                </span>
                <div class="view-toggle">
                    <button onclick="toggleView('table')" id="tableViewBtn" class="toggle-btn active">
                        <i class="fas fa-table"></i>
                    </button>
                    <button onclick="toggleView('cards')" id="cardsViewBtn" class="toggle-btn">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Table View -->
        <div id="tableView">
            @if(count($agentEarnings) > 0)
                <div style="overflow-x: auto;">
                    <table class="agent-table">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Agent Earnings</th>
                                <th>Agency Earnings</th>
                                <th>Total Earnings</th>
                                <th>Last Activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agentEarnings as $index => $agent)
                                @php
                                    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
                                    $currentUser = auth()->check() ? auth()->user()->name : null;
                                    $isCurrentUser = $agent['name'] === $currentUser;
                                    
                                    if (!$isAdmin && !$isCurrentUser && $loop->index >= 3) {
                                        continue;
                                    }
                                    
                                    $avatarClass = '';
                                    $rankBadge = '';
                                    
                                    if ($loop->index === 0) {
                                        $avatarClass = 'rank-gold';
                                        $rankBadge = '<span class="rank-badge badge-gold"><span>👑</span> Gold - #1</span>';
                                    } elseif ($loop->index === 1) {
                                        $avatarClass = 'rank-silver';
                                        $rankBadge = '<span class="rank-badge badge-silver"><span>🥈</span> Silver - #2</span>';
                                    } elseif ($loop->index === 2) {
                                        $avatarClass = 'rank-bronze';
                                        $rankBadge = '<span class="rank-badge badge-bronze"><span>🥉</span> Bronze - #3</span>';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div class="agent-avatar {{$avatarClass}}">
                                                {{ strtoupper(substr($agent['name'], 0, 2)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: var(--primary-navy); display: flex; align-items: center; gap: 8px;">
                                                    {{ $agent['name'] }}
                                                    {!! $rankBadge !!}
                                                </div>
                                                <div style="font-size: 12px; color: var(--gray);">{{ $agent['transaction_count'] }} total transactions</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--primary-navy); font-size: 15px;">
                                            £{{ number_format($agent['agent_earnings'], 2) }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--gray);">55% of commission</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--primary-navy); font-size: 15px;">
                                            £{{ number_format($agent['agency_earnings'], 2) }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--gray);">45% of commission</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: {{ $loop->index === 0 ? '#f59e0b' : ($loop->index === 1 ? '#6b7280' : ($loop->index === 2 ? '#8b5a2b' : 'var(--primary-navy)')) }}; font-size: 18px;">
                                            £{{ number_format($agent['total_earnings'], 2) }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--gray);">{{ $agent['transaction_count'] }} total</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 14px; color: var(--primary-navy);">
                                            {{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ isset($agent['id']) ? route('rental-codes.agent-payroll', ['agentId' => $agent['id']]) : route('rental-codes.agent-payroll-by-name', ['agentName' => $agent['name']]) }}" 
                                           class="btn-truehold btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                            <i class="fas fa-eye"></i>View Commission File
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="loading-screen">
                                            <i class="fas fa-chart-line" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
                                            <h3 style="color: var(--primary-navy); margin-bottom: 8px;">No earnings data found</h3>
                                            <p style="color: var(--gray);">No rental codes found for the selected criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="loading-screen">
                    <i class="fas fa-chart-line" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <h3 style="color: var(--primary-navy); margin-bottom: 8px;">No earnings data found</h3>
                    <p style="color: var(--gray);">No rental codes found for the selected criteria</p>
                </div>
            @endif
        </div>

        <!-- Cards View -->
        <div id="cardsView" class="agent-cards" style="display: none;">
            @foreach($agentEarnings as $index => $agent)
                @php
                    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
                    $currentUser = auth()->check() ? auth()->user()->name : null;
                    $isCurrentUser = $agent['name'] === $currentUser;
                    
                    if (!$isAdmin && !$isCurrentUser && $loop->index >= 3) {
                        continue;
                    }
                    
                    $avatarClass = '';
                    $cardBorder = '';
                    
                    if ($loop->index === 0) {
                        $avatarClass = 'rank-gold';
                        $cardBorder = 'border-color: #f59e0b;';
                    } elseif ($loop->index === 1) {
                        $avatarClass = 'rank-silver';
                        $cardBorder = 'border-color: #9ca3af;';
                    } elseif ($loop->index === 2) {
                        $avatarClass = 'rank-bronze';
                        $cardBorder = 'border-color: #8b5a2b;';
                    }
                @endphp
                <div class="agent-card" style="{{ $cardBorder }}">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="agent-avatar {{ $avatarClass }}">
                                {{ strtoupper(substr($agent['name'], 0, 2)) }}
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: var(--primary-navy);">{{ $agent['name'] }}</h4>
                                <p style="font-size: 12px; color: var(--gray);">{{ $agent['transaction_count'] }} transactions</p>
                            </div>
                        </div>
                        <a href="{{ isset($agent['id']) ? route('rental-codes.agent-payroll', ['agentId' => $agent['id']]) : route('rental-codes.agent-payroll-by-name', ['agentName' => $agent['name']]) }}">
                            <i class="fas fa-money-bill-wave" style="color: var(--gold); font-size: 20px;"></i>
                        </a>
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 14px; color: var(--gray);">Total Earnings</span>
                            <span style="font-size: 22px; font-weight: 700; color: {{ $loop->index === 0 ? '#f59e0b' : ($loop->index === 1 ? '#6b7280' : ($loop->index === 2 ? '#8b5a2b' : 'var(--primary-navy)')) }};">
                                £{{ number_format($agent['total_earnings'], 2) }}
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div style="background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 12px; text-align: center;">
                            <div style="font-weight: 600; color: #10b981; font-size: 15px;">£{{ number_format($agent['agent_earnings'], 2) }}</div>
                            <div style="font-size: 11px; color: #059669;">Agent (55%)</div>
                        </div>
                        <div style="background: rgba(59, 130, 246, 0.1); padding: 12px; border-radius: 12px; text-align: center;">
                            <div style="font-weight: 600; color: #3b82f6; font-size: 15px;">£{{ number_format($agent['agency_earnings'], 2) }}</div>
                            <div style="font-size: 11px; color: #2563eb;">Agency (45%)</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 2px solid var(--light-gray);">
                        <span style="font-size: 12px; color: var(--gray);">Avg: £{{ number_format($agent['avg_transaction_value'], 2) }}</span>
                        <span style="font-size: 12px; color: var(--gray);">{{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Filter toggle
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

// Commission cycle selection
function selectCycle(startDate, endDate) {
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('filterForm').submit();
}

// View toggle
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardsBtn = document.getElementById('cardsViewBtn');
    
    if (view === 'table') {
        tableView.style.display = 'block';
        cardsView.style.display = 'none';
        tableBtn.classList.add('active');
        cardsBtn.classList.remove('active');
    } else {
        tableView.style.display = 'none';
        cardsView.style.display = 'grid';
        tableBtn.classList.remove('active');
        cardsBtn.classList.add('active');
    }
}

// Export to Excel
function exportToExcel() {
    const table = document.querySelector('.agent-table');
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

// Chart.js configuration
@if(!$agentSearch && count($agentEarnings) > 0)
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = '#6c757d';

const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($chartData['monthly_totals'])) !!},
        datasets: [{
            label: 'Total Earnings',
            data: {!! json_encode(array_values($chartData['monthly_totals'])) !!},
            borderColor: '#d4af37',
            backgroundColor: 'rgba(212, 175, 55, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#d4af37',
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
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    callback: function(value) {
                        return '£' + value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
@endif
</script>
@endsection
