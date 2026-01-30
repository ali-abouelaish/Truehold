@extends('layouts.admin')

@section('page-title', 'Agent Earnings Analytics')

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
.earnings-container, .earnings-container * {
    color: #212529 !important;
}

.leaderboard-section {
    background-color: #ffffff !important;
}

.leaderboard-card {
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

/* Earnings Header */
.earnings-header {
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--navy-light) 100%);
    color: var(--white);
    padding: 32px 0;
    box-shadow: 0 4px 16px rgba(30, 58, 95, 0.15);
    margin: -24px -24px 24px -24px;
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
    width: 40px;
    height: 40px;
    fill: none;
    stroke: var(--gold);
    stroke-width: 2;
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

/* Filters Section */
.filters-section {
    padding: 24px 0;
    background-color: var(--white);
    border-bottom: 1px solid var(--light-gray);
    margin: -24px -24px 24px -24px;
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
    cursor: pointer;
}

.filters-toggle:hover {
    border-color: var(--primary-navy);
    background-color: var(--off-white);
}

.filters-toggle svg:first-child {
    color: var(--gold);
    width: 20px;
    height: 20px;
}

.filters-toggle .chevron {
    margin-left: 8px;
    transition: transform 0.3s ease;
    width: 16px;
    height: 16px;
}

.filters-content {
    display: none;
    margin-top: 20px;
    padding: 24px;
    background-color: var(--off-white);
    border-radius: 12px;
}

.filters-content.show {
    display: block;
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
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-input,
.form-select {
    width: 100%;
    padding: 12px 16px;
    border-radius: 8px;
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
    margin-bottom: 16px;
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

/* Stats Section */
.stats-section {
    padding: 32px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
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

.chart-card {
    background: var(--white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-md);
    margin-bottom: 32px;
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
    width: 20px;
    height: 20px;
}

/* Leaderboard Section */
.leaderboard-section {
    padding: 32px 0 48px;
    background-color: #ffffff;
}

.leaderboard-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--light-gray);
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
    width: 24px;
    height: 24px;
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
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid var(--light-gray);
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

/* Force all table cells to have proper light mode colors */
.leaderboard-table td,
.leaderboard-table th {
    background-color: transparent !important;
}

.leaderboard-table td {
    color: #212529 !important;
}

.leaderboard-table th {
    color: var(--primary-navy) !important;
}

.leaderboard-table tbody tr {
    transition: var(--transition);
    background-color: #ffffff !important;
}

.leaderboard-table tbody tr:hover {
    background-color: #f8f9fa !important;
}

/* Force override any parent dark mode styles on ALL table rows */
.leaderboard-table tbody tr:not(.rank-gold):not(.rank-silver):not(.rank-bronze) {
    background-color: #ffffff !important;
}

.leaderboard-table tbody tr:not(.rank-gold):not(.rank-silver):not(.rank-bronze):hover {
    background-color: #f8f9fa !important;
}

.rank-gold:hover,
.rank-silver:hover,
.rank-bronze:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
}

.rank-gold:hover {
    background: linear-gradient(135deg, #fffbf0 0%, #ffed9f 30%, #fffbf0 70%, #ffd93d 100%) !important;
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.35);
}

.rank-silver:hover {
    background: linear-gradient(135deg, #fafafa 0%, #e0e0e0 30%, #fafafa 70%, #d1d1d1 100%) !important;
    box-shadow: 0 6px 20px rgba(192, 192, 192, 0.35);
}

.rank-bronze:hover {
    background: linear-gradient(135deg, #fffaf5 0%, #ffd0a8 30%, #fffaf5 70%, #e8a87c 100%) !important;
    box-shadow: 0 6px 20px rgba(205, 127, 50, 0.35);
}

.rank-gold {
    background: linear-gradient(135deg, #fff9e6 0%, #ffeaa7 30%, #fff9e6 70%, #ffd93d 100%) !important;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.25);
    border-left: 5px solid #d4af37 !important;
    border-top: 1px solid #f1c232;
    border-bottom: 1px solid #f1c232;
}

.rank-gold td {
    color: #1a1a1a !important;
    font-weight: 600 !important;
}

.rank-gold .agent-name,
.rank-gold .rental-code,
.rank-gold .detail-value {
    color: #000000 !important;
    font-weight: 700 !important;
}

.rank-silver {
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 30%, #f5f5f5 70%, #d1d1d1 100%) !important;
    box-shadow: 0 4px 15px rgba(192, 192, 192, 0.25);
    border-left: 5px solid #9ca3af !important;
    border-top: 1px solid #d1d5db;
    border-bottom: 1px solid #d1d5db;
}

.rank-silver td {
    color: #1a1a1a !important;
    font-weight: 600 !important;
}

.rank-silver .agent-name,
.rank-silver .rental-code,
.rank-silver .detail-value {
    color: #000000 !important;
    font-weight: 700 !important;
}

.rank-bronze {
    background: linear-gradient(135deg, #fff5ed 0%, #ffd7b8 30%, #fff5ed 70%, #e8a87c 100%) !important;
    box-shadow: 0 4px 15px rgba(205, 127, 50, 0.25);
    border-left: 5px solid #cd7f32 !important;
    border-top: 1px solid #e8a87c;
    border-bottom: 1px solid #e8a87c;
}

.rank-bronze td {
    color: #1a1a1a !important;
    font-weight: 600 !important;
}

.rank-bronze .agent-name,
.rank-bronze .rental-code,
.rank-bronze .detail-value {
    color: #000000 !important;
    font-weight: 700 !important;
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
    box-shadow: 0 2px 8px rgba(30, 58, 95, 0.2);
}

.rank-gold .agent-avatar {
    background: linear-gradient(135deg, #f1c232, #ffd93d);
    color: #1a1a1a;
    border: 3px solid #d4af37;
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    font-weight: 900;
}

.rank-silver .agent-avatar {
    background: linear-gradient(135deg, #d1d5db, #e8e8e8);
    color: #1a1a1a;
    border: 3px solid #9ca3af;
    box-shadow: 0 4px 12px rgba(156, 163, 175, 0.3);
    font-weight: 900;
}

.rank-bronze .agent-avatar {
    background: linear-gradient(135deg, #e8a87c, #ffd7b8);
    color: #1a1a1a;
    border: 3px solid #cd7f32;
    box-shadow: 0 4px 12px rgba(205, 127, 50, 0.3);
    font-weight: 900;
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
    background: linear-gradient(135deg, #f1c232, #ffd93d);
    color: #1a1a1a;
    border: 2px solid #d4af37;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
}

.silver-badge {
    background: linear-gradient(135deg, #d1d5db, #e8e8e8);
    color: #1a1a1a;
    border: 2px solid #9ca3af;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(156, 163, 175, 0.3);
}

.bronze-badge {
    background: linear-gradient(135deg, #e8a87c, #ffd7b8);
    color: #1a1a1a;
    border: 2px solid #cd7f32;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(205, 127, 50, 0.3);
}

.agent-transactions {
    font-size: 12px;
    color: var(--gray);
}

.rank-gold .agent-transactions,
.rank-silver .agent-transactions,
.rank-bronze .agent-transactions {
    color: #1a1a1a !important;
    font-weight: 600 !important;
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

.rank-gold .earnings-amount,
.rank-silver .earnings-amount,
.rank-bronze .earnings-amount {
    color: #000000 !important;
}

.earnings-commission {
    font-size: 12px;
    color: var(--gray);
}

.rank-gold .earnings-commission,
.rank-silver .earnings-commission,
.rank-bronze .earnings-commission {
    color: #1a1a1a !important;
    font-weight: 600 !important;
}

.total-earnings {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 18px;
    color: var(--primary-navy);
}

.rank-gold .total-earnings,
.rank-silver .total-earnings,
.rank-bronze .total-earnings {
    color: #000000 !important;
}

.total-earnings svg {
    color: var(--gold);
    width: 16px;
    height: 16px;
}

.total-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray);
    display: block;
    margin-top: 4px;
}

.rank-gold .total-label,
.rank-silver .total-label,
.rank-bronze .total-label {
    color: #1a1a1a !important;
    font-weight: 700 !important;
}

.activity-date {
    font-size: 14px;
    color: var(--text-dark);
}

.rank-gold .activity-date,
.rank-silver .activity-date,
.rank-bronze .activity-date {
    color: #000000 !important;
    font-weight: 600 !important;
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
    text-decoration: none;
}

.btn-view-file:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    color: var(--white);
}

.rank-gold .btn-view-file,
.rank-silver .btn-view-file,
.rank-bronze .btn-view-file {
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.5);
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
    padding: 24px;
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

/* Shine effect for top rankings - Light mode */
@keyframes shine {
    0% {
        background-position: 200% center;
    }
    100% {
        background-position: -200% center;
    }
}

.rank-gold {
    background-size: 200% auto;
    animation: shine 4s linear infinite;
}

.rank-silver {
    background-size: 200% auto;
    animation: shine 5s linear infinite;
}

.rank-bronze {
    background-size: 200% auto;
    animation: shine 6s linear infinite;
}

@media (max-width: 768px) {
    .earnings-header {
        padding: 24px 0;
    }
    
    .earnings-header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .earnings-title {
        font-size: 28px;
    }
    
    .earnings-icon {
        width: 56px;
        height: 56px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .cycle-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .agent-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Earnings Header -->
<section class="earnings-header">
    <div class="container-fluid px-4">
        <div class="earnings-header-content">
            <div class="earnings-title-section">
                <div class="earnings-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                                <span>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                        <span class="date-badge">{{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days</span>
                            </div>
                        @endif
                    @if($isPayrollView)
                    <a href="{{ route('rental-codes.agent-earnings') }}" class="btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Back to All Agents
                        </a>
                    @else
                    <a href="{{ route('rental-codes.index') }}" class="btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Back to Rental Codes
        </a>
                    @endif
                    @if(count($agentEarnings) > 0)
                    <button onclick="exportToExcel()" class="btn-export">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
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
    <div class="container-fluid px-4">
        <button class="filters-toggle" onclick="toggleFilters()">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
            </svg>
            @if($isPayrollView) Payroll Filters @else Advanced Filters @endif
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" id="filterToggleIcon">
                <path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
                    </button>
        
        <div class="filters-content" id="filtersContent">
                @if($isPayrollView)
                <div style="background: rgba(59, 130, 246, 0.1); border: 2px solid rgba(59, 130, 246, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color: #3b82f6; margin-top: 2px;">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                            <div>
                            <h4 style="font-weight: 600; color: var(--primary-navy); margin-bottom: 8px;">Payroll View</h4>
                            <p style="color: var(--gray); font-size: 14px; margin: 0;">
                                    This view shows only <strong>approved</strong> rentals that this agent participated in (as rental agent or marketing agent). Outstanding amounts show what the agent is owed from unpaid rentals.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            
            <form method="GET" action="{{ route('rental-codes.agent-earnings') }}" id="filterForm">
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
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 3h-1V1h-2v2H7V1H5v2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V8h16v13z"/>
                                </svg>
                                Last 3 Months
                                </button>
                                
                            <button type="button" onclick="selectCycle('{{ $last6MonthsStart->toDateString() }}', '{{ $last6MonthsEnd }}')" 
                                    class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 3h-1V1h-2v2H7V1H5v2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V8h16v13z"/>
                                </svg>
                                Last 6 Months
                                </button>
                                
                            <button type="button" onclick="selectCycle('{{ $thisYearStart->toDateString() }}', '{{ $thisYearEnd }}')" 
                                    class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                                </svg>
                                This Year
                                </button>
                                
                            <button type="button" onclick="selectCycle('{{ $lastYearStart->toDateString() }}', '{{ $lastYearEnd->toDateString() }}')" 
                                    class="btn-secondary" style="font-size: 12px; padding: 10px; justify-content: center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/>
                                </svg>
                                Last Year
                                </button>
                            </div>
                        </div>
                    </div>

                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 3h-1V1h-2v2H7V1H5v2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V8h16v13z"/>
                            </svg>
                            Start Date
                            </label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input">
            </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 3h-1V1h-2v2H7V1H5v2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V8h16v13z"/>
                            </svg>
                            End Date
                            </label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input">
                        </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Agent Filter
                            </label>
                            @if($isPayrollView)
                            <div class="form-input" style="background: var(--off-white); color: var(--primary-navy); font-weight: 600;">
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
                                {{ auth()->check() ? auth()->user()->name : 'Guest' }} <span style="opacity: 0.7; font-weight: 400;">(Your Payroll Only)</span>
                                </div>
                                @auth
                                <input type="hidden" name="agent_search" value="{{ auth()->user()->name }}">
                                @endauth
                            @endif
                        </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Marketing Agent Filter
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
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Status
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
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                            </svg>
                            Payment Method
                            </label>
                        <select name="payment_method" class="form-select">
                                <option value="">All Methods</option>
                    <option value="Cash" {{ $paymentMethod==='Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Transfer" {{ $paymentMethod==='Transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        Apply Filters
                </button>
                    <a href="{{ route('rental-codes.agent-earnings') }}" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                        Clear
                    </a>
            </div>
        </form>
    </div>
</div>
</section>

<!-- Stats Cards -->
        @if(!$agentSearch)
<section class="stats-section">
    <div class="container-fluid px-4">
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
@endif

        <!-- Charts Section -->
@if(!$agentSearch && count($agentEarnings) > 0)
<section class="charts-section">
    <div class="container-fluid px-4">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <svg viewBox="0 0 24 24" fill="currentColor">
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
</section>
        @endif

<!-- Agent Leaderboard -->
<section class="leaderboard-section">
    <div class="container-fluid px-4">
        <div class="leaderboard-card">
            <div class="leaderboard-header">
                <h3 class="leaderboard-title">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2z"/>
                    </svg>
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
                            Agent Leaderboard
                                @else
                                    Agent Leaderboard
                                @endif
                            @endif
                        </h3>
                <div class="leaderboard-actions">
                    <span class="period-badge">
                                @if($startDate || $endDate)
                            Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            @else
                                All Time Data
                            @endif
            @if($status) • Status: {{ ucfirst($status) }} @endif
            @if($paymentMethod) • Payment: {{ $paymentMethod }} @endif
                        </span>
                </div>
    </div>
    
            @if(count($agentEarnings) > 0)
                <div class="table-responsive">
                    <table class="leaderboard-table">
                        <thead>
                            <tr>
                                <th>Agent</th>
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
                                    
                                    $rowClass = '';
                                    $badgeHtml = '';
                            
                            if ($loop->index === 0) {
                                        $rowClass = 'rank-gold';
                                        $badgeHtml = '<div class="agent-badge gold-badge">🥇 Gold - #1</div>';
                            } elseif ($loop->index === 1) {
                                        $rowClass = 'rank-silver';
                                        $badgeHtml = '<div class="agent-badge silver-badge">🥈 Silver - #2</div>';
                            } elseif ($loop->index === 2) {
                                        $rowClass = 'rank-bronze';
                                        $badgeHtml = '<div class="agent-badge bronze-badge">🥉 Bronze - #3</div>';
                                    }
                        @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <div class="agent-info">
                                            <div class="agent-avatar">
                                            {{ strtoupper(substr($agent['name'], 0, 2)) }}
                            </div>
                                    <div>
                                                <div class="agent-name">{{ $agent['name'] }}</div>
                                                {!! $badgeHtml !!}
                                                <div class="agent-transactions">{{ $agent['transaction_count'] }} total transactions</div>
                            </div>
                        </div>
                    </td>
                                    <td>
                                        <div class="total-earnings">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2z"/>
                                            </svg>
                            £{{ number_format($agent['total_earnings'], 2) }}
                                            <span class="total-label">{{ $agent['transaction_count'] }} total</span>
                        </div>
                    </td>
                                    <td>
                                        <div class="activity-date">
                                    {{ $agent['last_transaction_date'] ? $agent['last_transaction_date']->format('d M Y') : 'N/A' }}
                                </div>
                            </td>
                                    <td>
                                    <a href="{{ isset($agent['id']) ? route('rental-codes.agent-payroll', ['agentId' => $agent['id']]) : route('rental-codes.agent-payroll-by-name', ['agentName' => $agent['name']]) }}" 
                                           class="btn-view-file">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            View Commission File
                                        </a>
                    </td>
                </tr>
                                @empty
                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M23 8c0 1.1-.9 2-2 2-.18 0-.35-.02-.51-.07l-3.56 3.55c.05.16.07.34.07.52 0 1.1-.9 2-2 2s-2-.9-2-2c0-.18.02-.36.07-.52l-2.55-2.55c-.16.05-.34.07-.52.07s-.36-.02-.52-.07l-4.55 4.56c.05.16.07.33.07.51 0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2c.18 0 .35.02.51.07l4.56-4.55C8.02 9.36 8 9.18 8 9c0-1.1.9-2 2-2s2 .9 2 2c0 .18-.02.36-.07.52l2.55 2.55c.16-.05.34-.07.52-.07s.36.02.52.07l3.55-3.56C19.02 8.35 19 8.18 19 8c0-1.1.9-2 2-2s2 .9 2 2z"/>
                                            </svg>
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
                <div class="empty-state" style="padding: 60px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 8c0 1.1-.9 2-2 2-.18 0-.35-.02-.51-.07l-3.56 3.55c.05.16.07.34.07.52 0 1.1-.9 2-2 2s-2-.9-2-2c0-.18.02-.36.07-.52l-2.55-2.55c-.16.05-.34.07-.52.07s-.36-.02-.52-.07l-4.55 4.56c.05.16.07.33.07.51 0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2c.18 0 .35.02.51.07l4.56-4.55C8.02 9.36 8 9.18 8 9c0-1.1.9-2 2-2s2 .9 2 2c0 .18-.02.36-.07.52l2.55 2.55c.16-.05.34-.07.52-.07s.36.02.52.07l3.55-3.56C19.02 8.35 19 8.18 19 8c0-1.1.9-2 2-2s2 .9 2 2z"/>
                    </svg>
                    <h3 style="color: var(--primary-navy); margin-bottom: 8px;">No earnings data found</h3>
                    <p style="color: var(--gray);">No rental codes found for the selected criteria</p>
                                        </div>
                                    @endif
                                </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Filter toggle
function toggleFilters() {
    const content = document.getElementById('filtersContent');
    const icon = document.getElementById('filterToggleIcon');
    
    if (content.classList.contains('show')) {
        content.classList.remove('show');
        icon.style.transform = 'rotate(0deg)';
    } else {
        content.classList.add('show');
        icon.style.transform = 'rotate(180deg)';
    }
}

// Commission cycle selection
function selectCycle(startDate, endDate) {
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('filterForm').submit();
}

// Export to Excel
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
@endsection
