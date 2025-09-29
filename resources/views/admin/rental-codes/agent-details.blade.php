@extends('layouts.admin')

@section('page-title', 'Agent Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-tie text-primary me-2"></i>
                        Agent Details: {{ $agentName }}
                    </h1>
                    <p class="text-muted">Detailed earnings and performance analysis</p>
                </div>
                <div>
                    <a href="{{ route('rental-codes.agent-earnings') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Earnings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($performanceMetrics['total_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pound-sign fa-2x text-primary"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($performanceMetrics['paid_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($performanceMetrics['outstanding_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Payment Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($performanceMetrics['payment_rate'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('rental-codes.agent-details', $agentName) }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="">All Methods</option>
                                <option value="Cash" {{ $paymentMethod === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Transfer" {{ $paymentMethod === 'Transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('rental-codes.agent-details', $agentName) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Performance</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental Codes Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Rental Codes ({{ $agentRentals->count() }} total)</h6>
                    <div>
                        <span class="badge bg-success">{{ $performanceMetrics['paid_transactions'] }} Paid</span>
                        <span class="badge bg-warning">{{ $performanceMetrics['unpaid_transactions'] }} Outstanding</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rentalCodesTable">
                            <thead>
                                <tr>
                                    <th>Rental Code</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Fee</th>
                                    <th>Agent Earnings</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agentRentals as $rental)
                                <tr>
                                    <td>
                                        <strong>{{ $rental->rental_code }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $rental->property ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $rental->rental_date->format('d M Y') }}</td>
                                    <td>
                                        @if($rental->client)
                                            <div>
                                                <strong>{{ $rental->client->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $rental->client->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No client data</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>£{{ number_format($rental->consultation_fee, 2) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $rental->payment_method }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $totalFee = (float) $rental->consultation_fee;
                                            $paymentMethod = $rental->payment_method;
                                            $clientCount = $rental->client_count ?? 1;
                                            
                                            $baseCommission = $totalFee;
                                            if ($paymentMethod === 'Transfer') {
                                                $baseCommission = $totalFee * 0.8;
                                            }
                                            
                                            $agentEarnings = $baseCommission * 0.55;
                                            
                                            $marketingAgent = $rental->marketing_agent;
                                            $agentId = $rental->client_by_agent ?: $rental->rent_by_agent;
                                            
                                            if (!empty($marketingAgent) && $marketingAgent != $agentId) {
                                                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                                                $agentEarnings -= $marketingDeduction;
                                            }
                                        @endphp
                                        <strong>£{{ number_format($agentEarnings, 2) }}</strong>
                                        @if($marketingDeduction ?? 0 > 0)
                                            <br>
                                            <small class="text-danger">-£{{ number_format($marketingDeduction, 2) }} marketing</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $rental->status === 'completed' ? 'success' : ($rental->status === 'approved' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($rental->paid)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Paid
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $rental->paid_at->format('d M Y') }}</small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($rental->paid)
                                                <button class="btn btn-sm btn-outline-warning" onclick="markAsUnpaid({{ $rental->id }})">
                                                    <i class="fas fa-undo"></i> Mark Unpaid
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-success" onclick="markAsPaid({{ $rental->id }})">
                                                    <i class="fas fa-check"></i> Mark Paid
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-info" onclick="showRentalDetails({{ $rental->id }})">
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No rental codes found for this agent.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rental Details Modal -->
<div class="modal fade" id="rentalDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rental Code Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="rentalDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Performance Chart
const monthlyData = @json($monthlyBreakdown);
const monthlyLabels = Object.keys(monthlyData).map(month => {
    const date = new Date(month + '-01');
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
});
const monthlyEarnings = Object.values(monthlyData).map(data => data.total_earnings);
const monthlyPaid = Object.values(monthlyData).map(data => data.paid_amount);
const monthlyOutstanding = Object.values(monthlyData).map(data => data.outstanding_amount);

const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Total Earnings',
            data: monthlyEarnings,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Paid Amount',
            data: monthlyPaid,
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }, {
            label: 'Outstanding',
            data: monthlyOutstanding,
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
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
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': £' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});

// Mark as paid function
function markAsPaid(rentalId) {
    if (confirm('Are you sure you want to mark this rental as paid?')) {
        fetch(`/rental-codes/${rentalId}/mark-paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the rental code.');
        });
    }
}

// Mark as unpaid function
function markAsUnpaid(rentalId) {
    if (confirm('Are you sure you want to mark this rental as unpaid?')) {
        fetch(`/rental-codes/${rentalId}/mark-unpaid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the rental code.');
        });
    }
}

// Show rental details function
function showRentalDetails(rentalId) {
    // This would load detailed rental information
    // For now, we'll show a placeholder
    document.getElementById('rentalDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
            <p class="text-muted">Loading rental details...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('rentalDetailsModal'));
    modal.show();
    
    // Here you would typically make an AJAX call to get detailed rental information
    // For now, we'll show a placeholder
    setTimeout(() => {
        document.getElementById('rentalDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Rental Information</h6>
                    <p><strong>Code:</strong> RC${rentalId.toString().padStart(4, '0')}</p>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Status:</strong> <span class="badge bg-primary">Active</span></p>
                </div>
                <div class="col-md-6">
                    <h6>Financial Details</h6>
                    <p><strong>Fee:</strong> £1,200.00</p>
                    <p><strong>Agent Earnings:</strong> £660.00</p>
                    <p><strong>Payment Status:</strong> <span class="badge bg-warning">Pending</span></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Additional Information</h6>
                    <p class="text-muted">Detailed rental information would be displayed here, including client details, property information, and transaction history.</p>
                </div>
            </div>
        `;
    }, 1000);
}
</script>
@endpush
