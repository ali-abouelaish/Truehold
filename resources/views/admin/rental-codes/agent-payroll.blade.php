@extends('layouts.admin')

@section('title', $agentName . ' - Commission File')

@section('content')
<style>
/* Force Light Mode - Override All Dark Mode Styles */
html, body {
    background-color: #ffffff !important;
    color: #212529 !important;
}

.main-content {
    background-color: #ffffff !important;
}

main {
    background-color: #ffffff !important;
}

.container, .container-fluid, div, section, article, .card, .card-body {
    background-color: transparent !important;
}

/* Override any parent dark mode */
.payroll-container, .payroll-container * {
    color: #212529 !important;
}

.transaction-card {
    background-color: #ffffff !important;
}

.stats-section {
    background-color: #ffffff !important;
}

/* Override sidebar dark colors for this page */
#sidebar {
    background-color: #1f2937 !important;
}

/* TrueHold Premium Design - Light Mode */
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

body {
    background-color: #ffffff !important;
    color: #212529 !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Page Header */
.payroll-header {
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--navy-light) 100%);
    color: var(--white);
    padding: 32px 0;
    box-shadow: 0 4px 16px rgba(30, 58, 95, 0.15);
    margin: -24px -24px 24px -24px;
}

.payroll-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}

.payroll-title-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.payroll-icon {
    width: 64px;
    height: 64px;
    background: rgba(212, 175, 55, 0.15);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payroll-icon svg {
    width: 40px;
    height: 40px;
    fill: var(--gold);
}

.payroll-title {
    font-size: 36px;
    font-weight: 700;
    margin: 0 0 4px 0;
}

.payroll-subtitle {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
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
    width: 18px;
    height: 18px;
    stroke: var(--gold);
    fill: none;
}

.date-badge {
    padding: 4px 12px;
    background: var(--gold);
    color: var(--primary-navy);
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
}

.btn-secondary {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--white);
    color: var(--primary-navy);
    border: 2px solid var(--light-gray);
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
}

.btn-secondary:hover {
    border-color: var(--gold);
    color: var(--gold);
}

/* Summary Cards */
.summary-section {
    padding: 32px 0;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.summary-card {
    padding: 24px;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    border: 2px solid transparent;
}

.summary-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.summary-card-blue {
    background: linear-gradient(135deg, #5b7fda, #4a6bb8);
    color: var(--white);
    border-color: #4a6bb8;
}

.summary-card-green {
    background: linear-gradient(135deg, #10b981, #059669);
    color: var(--white);
    border-color: #059669;
}

.summary-card-pink {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: var(--white);
    border-color: #db2777;
}

.summary-card-orange {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: var(--white);
    border-color: #d97706;
}

.summary-card-yellow {
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border-color: var(--gold-dark);
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.summary-label {
    font-size: 14px;
    font-weight: 600;
    opacity: 0.9;
}

.summary-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
}

.summary-value {
    font-size: 36px;
    font-weight: 700;
}

/* Filters Section */
.filters-section {
    background: var(--white);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    padding: 24px;
    margin-bottom: 32px;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--light-gray);
}

.filters-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-navy);
}

.filters-title svg {
    color: var(--gold);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
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
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 8px;
    border: 2px solid var(--light-gray);
    font-size: 14px;
    transition: var(--transition);
    background: var(--white);
    color: var(--text-dark);
}

.form-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
}

.cycle-section {
    background: rgba(212, 175, 55, 0.1);
    border: 2px solid rgba(212, 175, 55, 0.2);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
}

.cycle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
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

.btn-primary {
    padding: 12px 32px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
}

/* Rental Codes Section */
.rental-codes-section {
    background: var(--white);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 32px;
}

.section-header {
    padding: 24px;
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    color: var(--white);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.section-title svg {
    color: var(--gold);
    width: 24px;
    height: 24px;
}

.btn-success {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.section-content {
    padding: 24px;
}

.totals-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding: 20px;
    background: var(--off-white);
    border-radius: 12px;
    margin-bottom: 24px;
}

.total-item {
    text-align: center;
}

.total-label {
    font-size: 13px;
    color: var(--gray);
    font-weight: 600;
    margin-bottom: 8px;
}

.total-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-navy);
}

.total-value.success {
    color: #10b981;
}

.total-value.warning {
    color: #f59e0b;
}

/* Rental Card */
.rental-card {
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 16px;
    border: 2px solid var(--light-gray);
    transition: var(--transition);
}

.rental-card:hover {
    border-color: var(--gold);
    box-shadow: var(--shadow-md);
}

.rental-card.paid {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.05));
    border-color: #10b981;
}

.rental-card.pending {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(217, 119, 6, 0.05));
    border-color: #f59e0b;
}

.rental-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.rental-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.rental-badge {
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}

.badge-transfer {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: var(--white);
}

.badge-cash {
    background: linear-gradient(135deg, #10b981, #059669);
    color: var(--white);
}

.badge-card {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: var(--white);
}

.badge-marketing {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: var(--white);
}

.badge-paid {
    background: linear-gradient(135deg, #10b981, #059669);
    color: var(--white);
}

.badge-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: var(--white);
}

.rental-code {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-navy);
}

.rental-date {
    font-size: 13px;
    color: var(--gray);
    margin-bottom: 16px;
}

.rental-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-label {
    font-size: 12px;
    color: var(--gray);
    font-weight: 600;
}

.detail-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-navy);
}

.detail-value.success {
    color: #10b981;
}

.deductions-section {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 2px solid var(--light-gray);
}

.deductions-title {
    font-size: 12px;
    color: var(--gray);
    font-weight: 600;
    margin-bottom: 8px;
}

.deduction-item {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
}

.deduction-vat {
    color: #f59e0b;
}

.deduction-marketing {
    color: #ef4444;
}

.deduction-agent {
    color: #3b82f6;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: var(--gray);
}

.empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
    opacity: 0.3;
}

@media (max-width: 768px) {
    .payroll-header {
        padding: 24px 0;
    }
    
    .payroll-header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .payroll-title {
        font-size: 28px;
    }
    
    .payroll-icon {
        width: 56px;
        height: 56px;
    }
    
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .cycle-grid {
        grid-template-columns: 1fr;
    }
    
    .rental-details {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .totals-bar {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Payroll Header -->
<section class="payroll-header">
    <div class="container-fluid px-4">
        <div class="payroll-header-content">
            <div class="payroll-title-section">
                <div class="payroll-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="payroll-title">{{ $agentName }} - Commission File</h1>
                    <p class="payroll-subtitle">Complete commission breakdown for {{ $agentName }}</p>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                @if($startDate && $endDate)
                    <div class="date-range-picker">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                        <span class="date-badge">{{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days</span>
                    </div>
                @endif
                <a href="{{ route('rental-codes.agent-earnings') }}" class="btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Commission Report
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Summary Cards -->
<section class="summary-section">
    <div class="container-fluid px-4">
        <div class="summary-grid">
            <div class="summary-card summary-card-blue">
                <div class="summary-header">
                    <div class="summary-label">Total Earnings</div>
                    <div class="summary-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                            <path d="M12.5 7H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/>
                        </svg>
                    </div>
                </div>
                <div class="summary-value">£{{ number_format($agent['total_earnings'], 2) }}</div>
            </div>

            <div class="summary-card summary-card-green">
                <div class="summary-header">
                    <div class="summary-label">Agent Earnings</div>
                    <div class="summary-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                </div>
                @php
                    $totalAgentEarnings = 0;
                    foreach ($agent['transactions'] ?? [] as $transaction) {
                        $totalAgentEarnings += (float) ($transaction['agent_cut'] ?? 0);
                    }
                    foreach ($agent['landlord_bonuses'] ?? [] as $bonus) {
                        $totalAgentEarnings += (float) ($bonus['agent_commission'] ?? 0);
                    }
                @endphp
                <div class="summary-value">£{{ number_format($totalAgentEarnings, 2) }}</div>
                <small style="opacity: 0.8; font-size: 12px;">Rental + Marketing</small>
            </div>

            <div class="summary-card summary-card-pink">
                <div class="summary-header">
                    <div class="summary-label">Marketing Earnings</div>
                    <div class="summary-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                        </svg>
                    </div>
                </div>
                <div class="summary-value">£{{ number_format($agent['marketing_agent_earnings'] ?? 0, 2) }}</div>
            </div>

            <div class="summary-card summary-card-orange">
                <div class="summary-header">
                    <div class="summary-label">Total Transactions</div>
                    <div class="summary-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                </div>
                <div class="summary-value">{{ $agent['transaction_count'] }}</div>
            </div>

            <div class="summary-card summary-card-yellow">
                <div class="summary-header">
                    <div class="summary-label">Outstanding</div>
                    <div class="summary-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                </div>
                <div class="summary-value">£{{ number_format($agent['outstanding_amount'], 2) }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Filters Section -->
<section class="container-fluid px-4">
    <div class="filters-section">
        <div class="filters-header">
            <h3 class="filters-title">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                </svg>
                Filters & Commission Cycles
            </h3>
        </div>
        
        <form method="GET" action="{{ url()->current() }}" id="filterForm">
            <!-- Quick Commission Cycle Selector -->
            <div class="cycle-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <label style="font-weight: 600; color: var(--primary-navy); font-size: 15px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="color: var(--gold); vertical-align: middle; margin-right: 6px;">
                            <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                        </svg>
                        Quick Select Commission Cycle (11th to 10th)
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
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                @elseif($cycle['isCurrent'])
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
                                        <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2z"/>
                                    </svg>
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
                                class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                            Last 3 Months
                        </button>
                        
                        <button type="button" onclick="selectCycle('{{ $last6MonthsStart->toDateString() }}', '{{ $last6MonthsEnd }}')" 
                                class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                            Last 6 Months
                        </button>
                        
                        <button type="button" onclick="selectCycle('{{ $thisYearStart->toDateString() }}', '{{ $thisYearEnd }}')" 
                                class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                            This Year
                        </button>
                        
                        <button type="button" onclick="selectCycle('{{ $lastYearStart->toDateString() }}', '{{ $lastYearEnd->toDateString() }}')" 
                                class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                            Last Year
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input">
                </div>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ url()->current() }}" class="btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                    Clear Filters
                </a>
            </div>
        </form>
    </div>
</section>

<!-- Rental Codes Section -->
<section class="container-fluid px-4">
    <div class="rental-codes-section">
        <div class="section-header">
            <h3 class="section-title">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
                Rental Codes ({{ count($agent['transactions']) }})
            </h3>
            @auth
            @if(auth()->user()->role === 'admin' && count($agent['transactions']) > 0)
            <form id="bulkPaidForm" method="POST" action="{{ route('rental-codes.bulk-mark-paid') }}" class="d-inline">
                @csrf
                <input type="hidden" name="rental_code_ids[]" value="" id="dummyIdsPlaceholder" style="display:none;">
                <button type="submit" class="btn-success" onclick="return submitBulkPaid(event)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    Mark Selected Paid
                </button>
            </form>
            @endif
            @endauth
        </div>
        <div class="section-content">
            @if(count($agent['transactions']) > 0)
                @php
                    $rt = $agent['rental_totals'] ?? ['count'=>0,'agent_cut'=>0,'paid'=>0,'entitled'=>0,'outstanding'=>0];
                    $marketingEarnings = $agent['marketing_agent_earnings'] ?? 0;
                    $totalAgentEarningsFromRentals = $rt['agent_cut'] + $marketingEarnings;
                    
                    $marketingPaid = 0;
                    $marketingEntitled = 0;
                    foreach ($agent['transactions'] ?? [] as $transaction) {
                        if ($transaction['is_marketing_earnings'] ?? false) {
                            if ($transaction['paid'] ?? false) {
                                $marketingPaid += (float) ($transaction['agent_cut'] ?? 0);
                            } else {
                                $marketingEntitled += (float) ($transaction['agent_cut'] ?? 0);
                            }
                        }
                    }
                    $totalPaid = $rt['paid'] + $marketingPaid;
                    $totalEntitled = $rt['entitled'] + $marketingEntitled;
                @endphp
                <div class="totals-bar">
                    <div class="total-item">
                        <div class="total-label">Rental Count</div>
                        <div class="total-value">{{ $rt['count'] }}</div>
                    </div>
                    <div class="total-item">
                        <div class="total-label">Agent Earnings</div>
                        <div class="total-value success">£{{ number_format($totalAgentEarningsFromRentals, 2) }}</div>
                    </div>
                    <div class="total-item">
                        <div class="total-label">Paid</div>
                        <div class="total-value success">£{{ number_format($totalPaid, 2) }}</div>
                    </div>
                    <div class="total-item">
                        <div class="total-label">To Be Paid</div>
                        <div class="total-value warning">£{{ number_format($totalEntitled, 2) }}</div>
                    </div>
                </div>
                @auth
                @if(auth()->user()->role === 'admin')
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--primary-navy); cursor: pointer;">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" style="width: 18px; height: 18px; cursor: pointer;">
                        Select All
                    </label>
                </div>
                @endif
                @endauth
                @foreach($agent['transactions'] as $transaction)
                <div class="rental-card {{ $transaction['paid'] ? 'paid' : 'pending' }}">
                    <div class="rental-header">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @auth
                            @if(auth()->user()->role === 'admin' && !$transaction['paid'])
                            <input type="checkbox" class="bulk-checkbox" value="{{ $transaction['id'] }}" style="width: 18px; height: 18px; cursor: pointer;">
                            @endif
                            @endauth
                            <div>
                                <div class="rental-badges">
                                    <span class="rental-badge badge-{{ $transaction['payment_method'] === 'Transfer' || $transaction['payment_method'] === 'Card Machine' || $transaction['payment_method'] === 'Card machine' ? 'transfer' : ($transaction['payment_method'] === 'Cash' ? 'cash' : 'card') }}">
                                        @if($transaction['payment_method'] === 'Transfer')
                                            ⚡
                                        @elseif($transaction['payment_method'] === 'Card Machine' || $transaction['payment_method'] === 'Card machine')
                                            💳
                                        @elseif($transaction['payment_method'] === 'Cash')
                                            💰
                                        @endif
                                        {{ $transaction['payment_method'] }}
                                    </span>
                                    <span class="rental-code">{{ $transaction['code'] }}</span>
                                    @if($transaction['is_marketing_earnings'] ?? false)
                                        <span class="rental-badge badge-marketing">Marketing</span>
                                    @endif
                                    @if($transaction['paid'])
                                        <span class="rental-badge badge-paid">✓ Paid</span>
                                    @else
                                        <span class="rental-badge badge-pending">Pending</span>
                                    @endif
                                </div>
                                <div class="rental-date">
                                    Date: {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                                    @if(!empty($transaction['clients']))
                                        | Client: {{ $transaction['clients'] }}
                                    @elseif($transaction['client_count'] > 1)
                                        | Clients: {{ $transaction['client_count'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rental-details">
                        @if(!empty($transaction['clients']))
                        <div class="detail-item">
                            <div class="detail-label">Client</div>
                            <div class="detail-value" style="font-size: 14px;">{{ $transaction['clients'] }}</div>
                        </div>
                        @endif
                        <div class="detail-item">
                            <div class="detail-label">Total Fee</div>
                            <div class="detail-value">£{{ number_format($transaction['total_fee'], 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ ($transaction['is_marketing_earnings'] ?? false) ? 'Marketing Cut' : 'Agent Cut' }}</div>
                            <div class="detail-value success">£{{ number_format($transaction['agent_cut'], 2) }}</div>
                        </div>
                    </div>
                    @if($transaction['vat_amount'] > 0 || $transaction['marketing_deduction'] > 0)
                    <div class="deductions-section">
                        <div class="deductions-title">Deductions:</div>
                        @if($transaction['vat_amount'] > 0)
                            <div class="deduction-item deduction-vat">VAT: £{{ number_format($transaction['vat_amount'], 2) }}</div>
                        @endif
                        @if($transaction['marketing_deduction'] > 0)
                            <div class="deduction-item deduction-marketing">Marketing: £{{ number_format($transaction['marketing_deduction'], 2) }}</div>
                        @endif
                        @if($transaction['marketing_agent'] ?? false)
                            <div class="deduction-item deduction-agent">Marketing Agent: {{ $transaction['marketing_agent'] }}</div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <h3 style="color: var(--primary-navy); margin-bottom: 8px;">No rental codes found</h3>
                    <p style="color: var(--gray);">No rental codes found for this agent</p>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Landlord Bonuses Section -->
<section class="container-fluid px-4">
    <div class="rental-codes-section">
        <div class="section-header">
            <h3 class="section-title">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                Landlord Bonuses ({{ count($agent['landlord_bonuses'] ?? []) }})
            </h3>
        </div>
        <div class="section-content">
            @if(count($agent['landlord_bonuses'] ?? []) > 0)
                @php
                    $bt = $agent['bonus_totals'] ?? ['count'=>0,'agent_commission'=>0,'paid'=>0,'entitled'=>0,'outstanding'=>0];
                @endphp
                <div class="totals-bar">
                    <div class="total-item">
                        <div class="total-label">Bonuses</div>
                        <div class="total-value">{{ $bt['count'] }}</div>
                    </div>
                    <div class="total-item">
                        <div class="total-label">Agent Commission</div>
                        <div class="total-value success">£{{ number_format($bt['agent_commission'], 2) }}</div>
                    </div>
                    <div class="total-item">
                        <div class="total-label">Paid</div>
                        <div class="total-value success">£{{ number_format($bt['paid'], 2) }}</div>
                    </div>
                    <div class="total-item">
                        <div class="total-label">To Be Paid</div>
                        <div class="total-value warning">£{{ number_format($bt['entitled'], 2) }}</div>
                    </div>
                </div>
                @foreach($agent['landlord_bonuses'] as $bonus)
                <div class="rental-card {{ ($bonus['status'] ?? 'pending') === 'paid' ? 'paid' : 'pending' }}">
                    <div class="rental-header">
                        <div>
                            <div class="rental-badges">
                                <span class="rental-badge" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">
                                    🎁 {{ $bonus['bonus_code'] }}
                                </span>
                                <span class="rental-code">{{ $bonus['property'] }}</span>
                                @if($bonus['status'] === 'paid')
                                    <span class="rental-badge badge-paid">✓ Paid</span>
                                @else
                                    <span class="rental-badge badge-pending">Pending</span>
                                @endif
                            </div>
                            <div class="rental-date">
                                Landlord: {{ $bonus['landlord'] }} | Client: {{ $bonus['client'] }}
                            </div>
                        </div>
                    </div>
                    <div class="rental-details">
                        <div class="detail-item">
                            <div class="detail-label">Total Commission</div>
                            <div class="detail-value">£{{ number_format($bonus['commission'], 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Agent Commission</div>
                            <div class="detail-value success">£{{ number_format($bonus['agent_commission'], 2) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Split</div>
                            <div class="detail-value">{{ $bonus['bonus_split'] === '100_0' ? '100% Agent' : '55% Agent, 45% Agency' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($bonus['date'])->format('M d, Y') }}</div>
                        </div>
                    </div>
                    @if($bonus['notes'])
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 2px solid var(--light-gray);">
                        <div style="font-size: 12px; color: var(--gray); font-weight: 600;">Notes:</div>
                        <div style="font-size: 13px; color: var(--text-dark); margin-top: 4px;">{{ $bonus['notes'] }}</div>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <h3 style="color: var(--primary-navy); margin-bottom: 8px;">No landlord bonuses found</h3>
                    <p style="color: var(--gray);">No landlord bonuses found for this agent</p>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
function toggleSelectAll(source) {
    document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = source.checked);
}

function selectCycle(startDate, endDate) {
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('filterForm').submit();
}

function submitBulkPaid(e) {
    e.preventDefault();
    const ids = Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) {
        alert('Select at least one unpaid rental.');
        return false;
    }
    const form = document.getElementById('bulkPaidForm');
    Array.from(form.querySelectorAll('input[name="rental_code_ids[]"]')).forEach(el => el.remove());
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'rental_code_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    form.submit();
    return true;
}
</script>
@endsection
