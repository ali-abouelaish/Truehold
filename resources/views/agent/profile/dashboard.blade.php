@extends('layouts.admin')

@section('title', 'Agent Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-0 text-primary fw-bold" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <i class="fas fa-user-tie text-primary me-2" style="filter: brightness(1.2);"></i>Agent Dashboard
                    </h1>
                    <p class="text-muted mb-0">Welcome back, {{ $user->name }}! Here's your performance overview.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('agent.profile.rental-codes') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>View All Rental Codes
                    </a>
                    <a href="{{ route('agent.profile.earnings') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line me-1"></i>View Earnings
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
                        <a class="nav-link active" href="{{ route('agent.profile.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.rental-codes') }}">
                            <i class="fas fa-receipt me-2"></i>My Rental Codes
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.earnings') }}">
                            <i class="fas fa-chart-line me-2"></i>Earnings & Deductions
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.clients') }}">
                            <i class="fas fa-users me-2"></i>My Clients
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Overview Cards -->
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($earningsData['paid_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Outstanding</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($earningsData['outstanding_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $earningsData['total_transactions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Rate</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h3 mb-0 font-weight-bold text-primary">{{ number_format($earningsData['payment_rate'], 1) }}%</div>
                        <small class="text-muted">{{ $earningsData['paid_transactions'] }} of {{ $earningsData['total_transactions'] }} transactions paid</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Average per Transaction</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h3 mb-0 font-weight-bold text-primary">£{{ number_format($earningsData['avg_earnings_per_transaction'], 2) }}</div>
                        <small class="text-muted">Average earnings per rental code</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Payments</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h3 mb-0 font-weight-bold text-warning">{{ $earningsData['unpaid_transactions'] }}</div>
                        <small class="text-muted">Transactions awaiting payment</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="row">
        <!-- Monthly Earnings Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Earnings</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyEarningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        @foreach($recentActivity as $activity)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    @if($activity->status === 'completed')
                                        <i class="fas fa-check-circle text-success"></i>
                                    @elseif($activity->status === 'approved')
                                        <i class="fas fa-thumbs-up text-primary"></i>
                                    @elseif($activity->status === 'pending')
                                        <i class="fas fa-clock text-warning"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-sm font-weight-bold text-gray-800">{{ $activity->rental_code }}</div>
                                    <div class="text-xs text-gray-600">{{ $activity->client->full_name ?? 'Unknown Client' }}</div>
                                    <div class="text-xs text-gray-500">{{ $activity->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge badge-{{ $activity->status === 'completed' ? 'success' : ($activity->status === 'approved' ? 'primary' : ($activity->status === 'pending' ? 'warning' : 'danger')) }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Rental Codes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Rental Codes</h6>
                    <a href="{{ route('agent.profile.rental-codes') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($rentalCodes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Rental Code</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Fee</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalCodes->take(5) as $code)
                                        <tr>
                                            <td>
                                                <a href="{{ route('rental-codes.show', $code->id) }}" class="text-primary">
                                                    {{ $code->rental_code }}
                                                </a>
                                            </td>
                                            <td>{{ $code->client->full_name ?? 'Unknown' }}</td>
                                            <td>{{ $code->rental_date->format('M d, Y') }}</td>
                                            <td>£{{ number_format($code->consultation_fee, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $code->status === 'completed' ? 'success' : ($code->status === 'approved' ? 'primary' : ($code->status === 'pending' ? 'warning' : 'danger')) }}">
                                                    {{ ucfirst($code->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($code->paid)
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-receipt fa-3x mb-3"></i>
                            <p>No rental codes found</p>
                            <a href="{{ route('rental-codes.create') }}" class="btn btn-primary">Create First Rental Code</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Earnings Chart
const ctx = document.getElementById('monthlyEarningsChart').getContext('2d');
const monthlyEarningsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($monthlyEarnings)->pluck('month')) !!},
        datasets: [{
            label: 'Earnings',
            data: {!! json_encode(collect($monthlyEarnings)->pluck('earnings')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '£' + value.toFixed(2);
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
