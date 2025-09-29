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
                    <button class="btn btn-outline-info me-2" onclick="testJavaScript()">
                        <i class="fas fa-bug me-1"></i> Test JS
                    </button>
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
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
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
                        <button class="btn btn-success btn-sm ms-2" onclick="markSelectedAsPaid()" id="bulkMarkPaidBtn" disabled>
                            <i class="fas fa-check me-1"></i>Mark Selected as Paid
                        </button>
                        <button class="btn btn-warning btn-sm ms-1" onclick="markSelectedAsUnpaid()" id="bulkMarkUnpaidBtn" disabled>
                            <i class="fas fa-times me-1"></i>Mark Selected as Unpaid
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rentalCodesTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        <label for="selectAll" class="ms-1">All</label>
                                    </th>
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
                                        <input type="checkbox" class="rental-checkbox" value="{{ $rental->id }}" data-rental-id="{{ $rental->id }}">
                                    </td>
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
                                                <button class="btn btn-sm btn-outline-warning" onclick="markAsUnpaid({{ $rental->id }})" title="Mark as Unpaid">
                                                    <i class="fas fa-undo"></i> Mark Unpaid
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-success" onclick="markAsPaid({{ $rental->id }})" title="Mark as Paid">
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
<div class="modal fade" id="rentalDetailsModal" tabindex="-1" aria-labelledby="rentalDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rentalDetailsModalLabel">
                    <i class="fas fa-info-circle text-primary me-2"></i>Rental Code Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="rentalDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Test JavaScript function
function testJavaScript() {
    console.log('JavaScript is working!');
    alert('JavaScript is working! Check the console for more details.');
    
    // Test if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap is loaded');
    } else {
        console.log('Bootstrap is NOT loaded');
    }
    
    // Test if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js is loaded');
    } else {
        console.log('Chart.js is NOT loaded');
    }
    
    // Test CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        console.log('CSRF token found:', csrfToken.getAttribute('content'));
    } else {
        console.log('CSRF token NOT found');
    }
}

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
        maintainAspectRatio: false,
        height: 200,
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
    console.log('markAsPaid function called with rentalId:', rentalId);
    
    console.log('Marking rental as paid:', rentalId);
    
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    button.disabled = true;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page and try again.');
            return;
        }
        
        fetch(`/admin/rental-codes/${rentalId}/mark-paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Show success message
                alert('Rental marked as paid successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
                // Restore button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the rental code: ' + error.message);
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        });
}

// Mark as unpaid function
function markAsUnpaid(rentalId) {
    console.log('markAsUnpaid function called with rentalId:', rentalId);
    
    console.log('Marking rental as unpaid:', rentalId);
    
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    button.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page and try again.');
        return;
    }
    
    fetch(`/rental-codes/${rentalId}/mark-unpaid`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Show success message
            alert('Rental marked as unpaid successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the rental code: ' + error.message);
        // Restore button
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Show rental details function
function showRentalDetails(rentalId) {
    console.log('showRentalDetails function called with rentalId:', rentalId);
    
    // Show loading spinner
    document.getElementById('rentalDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
            <p class="text-muted">Loading rental details...</p>
        </div>
    `;
    
    // Initialize and show modal
    const modalElement = document.getElementById('rentalDetailsModal');
    if (!modalElement) {
        console.error('Modal element not found');
        alert('Modal not found. Please refresh the page and try again.');
        return;
    }
    
    // Show the modal first
    const modal = new bootstrap.Modal(modalElement);
    console.log('Modal object created:', modal);
    modal.show();
    console.log('Modal show() called');
    
    // Fetch actual rental details
    console.log('Fetching rental details for ID:', rentalId);
        fetch(`/admin/rental-codes/${rentalId}/details`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Rental details data:', data);
        // Display the rental details
        document.getElementById('rentalDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-info-circle text-primary me-2"></i>Rental Information</h6>
                    <div class="mb-3">
                        <strong>Rental Code:</strong> ${data.rental_code || 'N/A'}
                    </div>
                    <div class="mb-3">
                        <strong>Date:</strong> ${data.rental_date ? new Date(data.rental_date).toLocaleDateString() : 'N/A'}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> 
                        <span class="badge bg-${data.status === 'completed' ? 'success' : (data.status === 'approved' ? 'primary' : 'warning')}">
                            ${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Property:</strong> ${data.property || 'N/A'}
                    </div>
                    <div class="mb-3">
                        <strong>Licensor:</strong> ${data.licensor || 'N/A'}
                    </div>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-pound-sign text-success me-2"></i>Financial Details</h6>
                    <div class="mb-3">
                        <strong>Consultation Fee:</strong> £${parseFloat(data.consultation_fee || 0).toFixed(2)}
                    </div>
                    <div class="mb-3">
                        <strong>Payment Method:</strong> 
                        <span class="badge bg-${data.payment_method === 'Transfer' ? 'info' : 'secondary'}">
                            ${data.payment_method || 'N/A'}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Payment Status:</strong> 
                        <span class="badge bg-${data.paid ? 'success' : 'warning'}">
                            <i class="fas fa-${data.paid ? 'check' : 'clock'} me-1"></i>
                            ${data.paid ? 'Paid' : 'Pending'}
                        </span>
                    </div>
                    ${data.paid_at ? `
                    <div class="mb-3">
                        <strong>Paid Date:</strong> ${new Date(data.paid_at).toLocaleDateString()}
                    </div>
                    ` : ''}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6><i class="fas fa-user text-info me-2"></i>Agent Information</h6>
                    <div class="mb-3">
                        <strong>Rent Agent:</strong> ${data.rent_by_agent_name || 'N/A'}
                    </div>
                    ${data.marketing_agent_name ? `
                    <div class="mb-3">
                        <strong>Marketing Agent:</strong> ${data.marketing_agent_name}
                    </div>
                    ` : ''}
                    ${data.client_count > 1 ? `
                    <div class="mb-3">
                        <strong>Client Count:</strong> ${data.client_count} clients
                    </div>
                    ` : ''}
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-user-friends text-warning me-2"></i>Client Information</h6>
                    ${data.client ? `
                    <div class="mb-3">
                        <strong>Client Name:</strong> ${data.client.full_name || 'N/A'}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong> ${data.client.email || 'N/A'}
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong> ${data.client.phone || 'N/A'}
                    </div>
                    ` : `
                    <div class="mb-3 text-muted">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        No client information available
                    </div>
                    `}
                </div>
            </div>
            ${data.notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6><i class="fas fa-sticky-note text-secondary me-2"></i>Notes</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">${data.notes}</p>
                    </div>
                </div>
            </div>
            ` : ''}
        `;
    })
    .catch(error => {
        console.error('Error fetching rental details:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            rentalId: rentalId
        });
        document.getElementById('rentalDetailsContent').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                <h6 class="text-danger">Error Loading Details</h6>
                <p class="text-muted">Unable to load rental code details. Please try again.</p>
                <p class="text-muted small">Error: ${error.message}</p>
                <button class="btn btn-outline-primary btn-sm" onclick="showRentalDetails(${rentalId})">
                    <i class="fas fa-refresh me-1"></i>Retry
                </button>
            </div>
        `;
    });
}

// Multiple select functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.rental-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkButtons();
}

function updateBulkButtons() {
    const selectedCheckboxes = document.querySelectorAll('.rental-checkbox:checked');
    const bulkMarkPaidBtn = document.getElementById('bulkMarkPaidBtn');
    const bulkMarkUnpaidBtn = document.getElementById('bulkMarkUnpaidBtn');
    
    if (selectedCheckboxes.length > 0) {
        bulkMarkPaidBtn.disabled = false;
        bulkMarkUnpaidBtn.disabled = false;
    } else {
        bulkMarkPaidBtn.disabled = true;
        bulkMarkUnpaidBtn.disabled = true;
    }
}

function markSelectedAsPaid() {
    const selectedCheckboxes = document.querySelectorAll('.rental-checkbox:checked');
    const rentalIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.rentalId);
    
    if (rentalIds.length === 0) {
        alert('Please select at least one rental code.');
        return;
    }
    
    console.log('Marking multiple rentals as paid:', rentalIds);
    
    // Show loading state
    const button = document.getElementById('bulkMarkPaidBtn');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    button.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page and try again.');
        return;
    }
    
    // Process each rental
    let completed = 0;
    let errors = 0;
    
    rentalIds.forEach(rentalId => {
        fetch(`/admin/rental-codes/${rentalId}/mark-paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            completed++;
            if (completed + errors === rentalIds.length) {
                if (errors === 0) {
                    alert(`Successfully marked ${completed} rental(s) as paid!`);
                    location.reload();
                } else {
                    alert(`Marked ${completed} rental(s) as paid, but ${errors} failed.`);
                }
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error marking rental as paid:', error);
            errors++;
            if (completed + errors === rentalIds.length) {
                if (completed > 0) {
                    alert(`Marked ${completed} rental(s) as paid, but ${errors} failed.`);
                } else {
                    alert('Failed to mark rentals as paid. Please try again.');
                }
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    });
}

function markSelectedAsUnpaid() {
    const selectedCheckboxes = document.querySelectorAll('.rental-checkbox:checked');
    const rentalIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.rentalId);
    
    if (rentalIds.length === 0) {
        alert('Please select at least one rental code.');
        return;
    }
    
    console.log('Marking multiple rentals as unpaid:', rentalIds);
    
    // Show loading state
    const button = document.getElementById('bulkMarkUnpaidBtn');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    button.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page and try again.');
        return;
    }
    
    // Process each rental
    let completed = 0;
    let errors = 0;
    
    rentalIds.forEach(rentalId => {
        fetch(`/admin/rental-codes/${rentalId}/mark-unpaid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            completed++;
            if (completed + errors === rentalIds.length) {
                if (errors === 0) {
                    alert(`Successfully marked ${completed} rental(s) as unpaid!`);
                    location.reload();
                } else {
                    alert(`Marked ${completed} rental(s) as unpaid, but ${errors} failed.`);
                }
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error marking rental as unpaid:', error);
            errors++;
            if (completed + errors === rentalIds.length) {
                if (completed > 0) {
                    alert(`Marked ${completed} rental(s) as unpaid, but ${errors} failed.`);
                } else {
                    alert('Failed to mark rentals as unpaid. Please try again.');
                }
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    });
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.rental-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButtons);
    });
});
</script>
@endpush

@push('styles')
<style>
/* Force modal to be perfectly centered */
#rentalDetailsModal .modal-dialog {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    margin: 0 !important;
    max-width: 800px !important;
    width: 90% !important;
    max-height: 90vh !important;
    z-index: 1055 !important;
}

#rentalDetailsModal .modal-content {
    max-height: 90vh;
    overflow-y: auto;
}

/* Make charts 60% smaller */
.chart-container {
    height: 200px !important;
    width: 100% !important;
}

#monthlyPerformanceChart {
    height: 200px !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #rentalDetailsModal .modal-dialog {
        width: 95% !important;
        max-width: 95% !important;
    }
    
    .chart-container {
        height: 150px !important;
    }
}
</style>
@endpush
