@extends('layouts.admin')

@section('page-title', 'Agent Earnings Analytics')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Agent Earnings Analytics - TrueHold">
    <meta name="theme-color" content="#1e3a5f">
    <title>Agent Earnings - TrueHold</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
/* ==========================================
   TRUEHOLD - Premium Property Listings
   Color Palette: White, Navy Blue, Gold
   ========================================== */

/* CSS Variables */
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

/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-dark);
    background-color: var(--off-white);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

a {
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
    -webkit-tap-highlight-color: transparent;
}

button {
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: var(--transition);
    -webkit-tap-highlight-color: transparent;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ==========================================
   AGENT EARNINGS PAGE
   ========================================== */

.earnings-header {
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--navy-light) 100%);
    color: var(--white);
    padding: 32px 0;
    box-shadow: 0 4px 16px rgba(30, 58, 95, 0.15);
}

.earnings-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}

.earnings-title-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.earnings-icon {
    width: 64px;
    height: 64px;
    background: rgba(212, 175, 55, 0.15);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.earnings-icon svg {
    stroke: var(--gold);
}

.earnings-title {
    font-size: 36px;
    font-weight: 700;
    margin: 0 0 4px 0;
}

.earnings-subtitle {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}

.earnings-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.date-range-picker {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.date-range-picker svg {
    stroke: var(--gold);
}

.date-badge {
    padding: 4px 12px;
    background: var(--gold);
    color: var(--primary-navy);
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
}

.btn-export {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(212, 175, 55, 0.4);
}

/* Filters Section */
.filters-section {
    padding: 24px 0;
    background-color: var(--white);
    border-bottom: 1px solid var(--light-gray);
}

.filters-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background-color: var(--white);
    border: 2px solid var(--light-gray);
    border-radius: 10px;
    color: var(--primary-navy);
    font-weight: 600;
    font-size: 15px;
    transition: var(--transition);
    width: fit-content;
}

.filters-toggle:hover {
    border-color: var(--primary-navy);
    background-color: var(--off-white);
}

.filters-toggle svg:first-child {
    color: var(--gold);
}

.filters-toggle .chevron {
    margin-left: 8px;
    transition: transform 0.3s ease;
}

/* Stats Section */
.stats-section {
    padding: 32px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    padding: 24px;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.stat-label {
    font-size: 14px;
    font-weight: 600;
    opacity: 0.9;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
}

.stat-blue {
    background: linear-gradient(135deg, #5b7fda, #4a6bb8);
    color: var(--white);
}

.stat-green {
    background: linear-gradient(135deg, #4db8a8, #3c9688);
    color: var(--white);
}

.stat-purple {
    background: linear-gradient(135deg, #9b6ac8, #7f52a8);
    color: var(--white);
}

.stat-orange {
    background: linear-gradient(135deg, #e8a75f, #d18a42);
    color: var(--white);
}

/* Charts Section */
.charts-section {
    padding: 32px 0;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 24px;
}

.chart-card {
    background: var(--white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-md);
}

.chart-header {
    margin-bottom: 20px;
}

.chart-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-navy);
}

.chart-title svg {
    color: var(--gold);
}

.chart-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(30, 58, 95, 0.03), rgba(30, 58, 95, 0.01));
    border-radius: 12px;
    color: var(--gray);
    position: relative;
}

/* Leaderboard Section */
.leaderboard-section {
    padding: 32px 0 48px;
}

.leaderboard-card {
    background: var(--white);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.leaderboard-header {
    padding: 24px;
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    color: var(--white);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.leaderboard-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.leaderboard-title svg {
    color: var(--gold);
}

.period-badge {
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    font-size: 13px;
    backdrop-filter: blur(10px);
}

.table-responsive {
    overflow-x: auto;
}

.leaderboard-table {
    width: 100%;
    border-collapse: collapse;
}

.leaderboard-table thead {
    background-color: var(--off-white);
}

.leaderboard-table th {
    padding: 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 700;
    color: var(--primary-navy);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.leaderboard-table td {
    padding: 20px 16px;
    border-bottom: 1px solid var(--light-gray);
}

.leaderboard-table tbody tr {
    transition: var(--transition);
}

.leaderboard-table tbody tr:hover {
    background-color: var(--off-white);
}

.rank-gold {
    background: linear-gradient(to right, rgba(255, 215, 0, 0.1), transparent);
}

.rank-bronze {
    background: linear-gradient(to right, rgba(205, 127, 50, 0.1), transparent);
}

.agent-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.agent-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
}

.agent-name {
    font-weight: 600;
    font-size: 15px;
    color: var(--text-dark);
    margin-bottom: 4px;
}

.agent-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 4px;
}

.gold-badge {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #8b6914;
}

.silver-badge {
    background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
    color: #666;
}

.bronze-badge {
    background: linear-gradient(135deg, #cd7f32, #d99a5a);
    color: #5c3a1a;
}

.agent-transactions {
    font-size: 12px;
    color: var(--gray);
}

.earnings-cell {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.earnings-amount {
    font-weight: 700;
    font-size: 16px;
    color: var(--primary-navy);
}

.earnings-commission {
    font-size: 12px;
    color: var(--gray);
}

.total-earnings {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 18px;
    color: var(--primary-navy);
}

.total-earnings svg {
    color: var(--gold);
}

.total-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray);
}

.activity-date {
    font-size: 14px;
    color: var(--text-dark);
}

.btn-view-file {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: var(--transition);
    white-space: nowrap;
}

.btn-view-file:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .earnings-header-content,
    .earnings-actions {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    
    .earnings-title {
        font-size: 28px;
    }
    
    .date-range-picker,
    .btn-export {
        width: 100%;
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .leaderboard-table {
        min-width: 800px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .earnings-title-section {
        flex-direction: column;
        align-items: flex-start;
    }
}
    </style>
</head>
<body>
    <!-- Earnings Header -->
    <section class="earnings-header">
        <div class="container">
            <div class="earnings-header-content">
                <div class="earnings-title-section">
                    <div class="earnings-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke-width="2"/>
                            <circle cx="12" cy="7" r="4" stroke-width="2"/>
                            <path d="M16 11l5-3m0 0l-5-3m5 3H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="earnings-title">
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
                        <p class="earnings-subtitle">
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
                    </div>
                </div>
                <div class="earnings-actions">
                    @if($startDate && $endDate)
                        <div class="date-range-picker">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                            <span class="date-badge">{{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days</span>
                        </div>
                    @endif
                    @if(count($agentEarnings) > 0)
                        <button onclick="exportToExcel()" class="btn-export">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Export Excel
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Filters -->
    <section class="filters-section">
        <div class="container">
            <button class="filters-toggle" onclick="toggleFilters()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                </svg>
                @if($isPayrollView) Payroll Filters @else Advanced Filters @endif
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="chevron" id="filterToggleIcon">
                    <path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </section>

    <!-- Stats Cards -->
    @if(!$agentSearch)
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card stat-blue">
                        <div class="stat-header">
                            <div class="stat-label">Total Agents</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">{{ $summary['total_agents'] }}</div>
                    </div>

                    <div class="stat-card stat-green">
                        <div class="stat-header">
                            <div class="stat-label">Total Earnings</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">£{{ number_format($summary['total_earnings'], 2) }}</div>
                    </div>

                    <div class="stat-card stat-purple">
                        <div class="stat-header">
                            <div class="stat-label">Total Transactions</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">{{ $summary['total_transactions'] }}</div>
                    </div>

                    <div class="stat-card stat-orange">
                        <div class="stat-header">
                            <div class="stat-label">Avg per Agent</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">£{{ number_format($summary['avg_earnings_per_agent'], 2) }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        @if(count($agentEarnings) > 0)
            <section class="charts-section">
                <div class="container">
                    <div class="charts-grid">
                        <!-- Monthly Earnings Trend -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23 8c0 1.1-.9 2-2 2-.18 0-.35-.02-.51-.07l-3.56 3.55c.05.16.07.34.07.52 0 1.1-.9 2-2 2s-2-.9-2-2c0-.18.02-.36.07-.52l-2.55-2.55c-.16.05-.34.07-.52.07s-.36-.02-.52-.07l-4.55 4.56c.05.16.07.33.07.51 0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2c.18 0 .35.02.51.07l4.56-4.55C8.02 9.36 8 9.18 8 9c0-1.1.9-2 2-2s2 .9 2 2c0 .18-.02.36-.07.52l2.55 2.55c.16-.05.34-.07.52-.07s.36.02.52.07l3.55-3.56C19.02 8.35 19 8.18 19 8c0-1.1.9-2 2-2s2 .9 2 2z"/>
                                    </svg>
                                    Monthly Earnings Trend
                                </h3>
                            </div>
                            <div style="height: 300px;">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    <!-- Agent Leaderboard -->
    <section class="leaderboard-section">
        <div class="container">
            <div class="leaderboard-card">
                <div class="leaderboard-header">
                    <h3 class="leaderboard-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2z"/>
                        </svg>
                        @if($agentSearch)
                            {{ $agentSearch }} - Commission File
                        @elseif($isPayrollView)
                            Commission File
                        @else
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                Agent Earnings Breakdown
                            @else
                                Agent Leaderboard
                            @endif
                        @endif
                    </h3>
                    <div class="leaderboard-actions">
                        <span class="period-badge">
                            @if($startDate || $endDate)
                                Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }}
                                — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            @else
                                All Time Data
                            @endif
                            @if($status) • Status: {{ ucfirst($status) }} @endif
                            @if($paymentMethod) • Payment: {{ $paymentMethod }} @endif
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="leaderboard-table">
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
                                    
                                    $rankClass = '';
                                    $badgeClass = '';
                                    $badgeText = '';
                                    
                                    if ($loop->index === 0) {
                                        $rankClass = 'rank-gold';
                                        $badgeClass = 'gold-badge';
                                        $badgeText = 'Gold - #1';
                                    } elseif ($loop->index === 1) {
                                        $badgeClass = 'silver-badge';
                                        $badgeText = 'Silver - #2';
                                    } elseif ($loop->index === 2) {
                                        $rankClass = 'rank-bronze';
                                        $badgeClass = 'bronze-badge';
                                        $badgeText = 'Bronze - #3';
                                    }
                                @endphp
                                <tr class="{{ $rankClass }}">
                                    <td>
                                        <div class="agent-info">
                                            <div class="agent-avatar">{{ strtoupper(substr($agent['name'], 0, 2)) }}</div>
                                            <div>
                                                <div class="agent-name">{{ $agent['name'] }}</div>
                                                @if($badgeText)
                                                    <div class="agent-badge {{ $badgeClass }}">{{ $badgeText }}</div>
                                                @endif
                                                <div class="agent-transactions">{{ $agent['transaction_count'] }} total transactions</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="earnings-cell">
                                            <div class="earnings-amount">£{{ number_format($agent['agent_earnings'], 2) }}</div>
                                            <div class="earnings-commission">55% of commission</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="earnings-cell">
                                            <div class="earnings-amount">£{{ number_format($agent['agency_earnings'], 2) }}</div>
                                            <div class="earnings-commission">45% of commission</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="total-earnings">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2z"/>
                                            </svg>
                                            £{{ number_format($agent['total_earnings'], 2) }}
                                            <span class="total-label">{{ $agent['transaction_count'] }} total</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="activity-date">{{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ isset($agent['id']) ? route('rental-codes.agent-payroll', ['agentId' => $agent['id']]) : route('rental-codes.agent-payroll-by-name', ['agentName' => $agent['name']]) }}" 
                                           class="btn-view-file">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-width="2"/>
                                                <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                            </svg>
                                            View Commission File
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 48px;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="opacity: 0.3;">
                                                <path d="M23 8c0 1.1-.9 2-2 2-.18 0-.35-.02-.51-.07l-3.56 3.55c.05.16.07.34.07.52 0 1.1-.9 2-2 2s-2-.9-2-2c0-.18.02-.36.07-.52l-2.55-2.55c-.16.05-.34.07-.52.07s-.36-.02-.52-.07l-4.55 4.56c.05.16.07.33.07.51 0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2c.18 0 .35.02.51.07l4.56-4.55C8.02 9.36 8 9.18 8 9c0-1.1.9-2 2-2s2 .9 2 2c0 .18-.02.36-.07.52l2.55 2.55c.16-.05.34-.07.52-.07s.36.02.52.07l3.55-3.56C19.02 8.35 19 8.18 19 8c0-1.1.9-2 2-2s2 .9 2 2z"/>
                                            </svg>
                                            <h3 style="color: var(--primary-navy); font-size: 18px; font-weight: 600; margin: 0;">No earnings data found</h3>
                                            <p style="color: var(--gray); margin: 0;">No rental codes found for the selected criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleFilters() {
            const toggle = document.querySelector('.filters-toggle');
            const chevron = document.getElementById('filterToggleIcon');
            
            if (chevron.style.transform === 'rotate(180deg)') {
                chevron.style.transform = 'rotate(0deg)';
            } else {
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        // Export functionality
        function exportToExcel() {
            const table = document.querySelector('.leaderboard-table');
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
</body>
</html>
@endsection
