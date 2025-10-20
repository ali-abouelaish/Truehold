@extends('layouts.admin')

@section('title', 'Agent Earnings')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-0 text-primary fw-bold" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <i class="fas fa-chart-line text-primary me-2" style="filter: brightness(1.2);"></i>Earnings & Deductions
                    </h1>
                    <p class="text-muted mb-0">Detailed breakdown of your earnings, deductions, and payment history</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('agent.profile.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Internal Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <nav class="nav nav-pills nav-fill">
                        <a class="nav-link" href="{{ route('agent.profile.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.rental-codes') }}">
                            <i class="fas fa-receipt me-2"></i>My Rental Codes
                        </a>
                        <a class="nav-link active" href="{{ route('agent.profile.earnings') }}">
                            <i class="fas fa-chart-line me-2"></i>Earnings
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.deductions') }}">
                            <i class="fas fa-minus-circle me-2"></i>Deductions
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.clients') }}">
                            <i class="fas fa-users me-2"></i>My Clients
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Filter by Date Range</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('agent.profile.earnings') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Apply Filter
                                </button>
                                <a href="{{ route('agent.profile.earnings') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($earningsData['total_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pound-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rental Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($earningsData['rental_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Marketing Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($earningsData['marketing_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Net Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($earningsData['net_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deductions Breakdown -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Deductions Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 font-weight-bold text-danger">£{{ number_format($earningsData['vat_deductions'], 2) }}</div>
                                <div class="text-xs text-gray-600">VAT Deductions</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 font-weight-bold text-warning">£{{ number_format($earningsData['marketing_deductions'], 2) }}</div>
                                <div class="text-xs text-gray-600">Marketing Deductions</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h5 mb-0 font-weight-bold text-primary">
                            £{{ number_format($earningsData['vat_deductions'] + $earningsData['marketing_deductions'], 2) }}
                        </div>
                        <div class="text-xs text-gray-600">Total Deductions</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Status</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 font-weight-bold text-success">£{{ number_format($earningsData['paid_amount'], 2) }}</div>
                                <div class="text-xs text-gray-600">Paid Amount</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 font-weight-bold text-warning">£{{ number_format($earningsData['outstanding_amount'], 2) }}</div>
                                <div class="text-xs text-gray-600">Outstanding</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h5 mb-0 font-weight-bold text-primary">{{ $earningsData['transaction_count'] }}</div>
                        <div class="text-xs text-gray-600">Total Transactions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Earnings Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Rental Earnings</th>
                                    <th>Marketing Earnings</th>
                                    <th>Total Earnings</th>
                                    <th>VAT Deductions</th>
                                    <th>Marketing Deductions</th>
                                    <th>Net Earnings</th>
                                    <th>Transactions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyBreakdown as $month)
                                    <tr>
                                        <td>{{ $month['month'] }}</td>
                                        <td>£{{ number_format($month['earnings']['rental_earnings'], 2) }}</td>
                                        <td>£{{ number_format($month['earnings']['marketing_earnings'], 2) }}</td>
                                        <td>£{{ number_format($month['earnings']['total_earnings'], 2) }}</td>
                                        <td>£{{ number_format($month['earnings']['vat_deductions'], 2) }}</td>
                                        <td>£{{ number_format($month['earnings']['marketing_deductions'], 2) }}</td>
                                        <td class="font-weight-bold">£{{ number_format($month['earnings']['net_earnings'], 2) }}</td>
                                        <td>{{ $month['transaction_count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                </div>
                <div class="card-body">
                    @if($paymentHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Rental Code</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Fee</th>
                                        <th>Your Earnings</th>
                                        <th>Paid Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentHistory as $payment)
                                        <tr>
                                            <td>
                                                <a href="{{ route('rental-codes.show', $payment->id) }}" class="text-primary">
                                                    {{ $payment->rental_code }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->client->full_name ?? 'Unknown' }}</td>
                                            <td>{{ $payment->rental_date->format('M d, Y') }}</td>
                                            <td>£{{ number_format($payment->consultation_fee, 2) }}</td>
                                            <td class="font-weight-bold">
                                                @php
                                                    $totalFee = (float) $payment->consultation_fee;
                                                    $paymentMethod = $payment->payment_method ?? 'Cash';
                                                    $baseCommission = $paymentMethod === 'Transfer' ? $totalFee * 0.8 : $totalFee;
                                                    $clientCount = $payment->client_count ?? 1;
                                                    
                                                    $agentEarnings = 0;
                                                    $marketingEarnings = 0;
                                                    
                                                    $rentAgentName = $payment->rent_by_agent_name;
                                                    $marketingAgentName = $payment->marketing_agent_name;
                                                    
                                                    if ($rentAgentName === ($agent->company_name ?? $user->name)) {
                                                        $agentEarnings = $baseCommission * 0.55;
                                                        if (!empty($marketingAgentName) && $marketingAgentName !== ($agent->company_name ?? $user->name)) {
                                                            $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                                                            $agentEarnings -= $marketingDeduction;
                                                        }
                                                    }
                                                    
                                                    if ($marketingAgentName === $user->name && $marketingAgentName !== $rentAgentName) {
                                                        $marketingEarnings = $clientCount > 1 ? 40.0 : 30.0;
                                                    }
                                                    
                                                    $totalEarnings = $agentEarnings + $marketingEarnings;
                                                @endphp
                                                £{{ number_format($totalEarnings, 2) }}
                                            </td>
                                            <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-success">Paid</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-receipt fa-3x mb-3"></i>
                            <p>No payment history found for the selected date range</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
