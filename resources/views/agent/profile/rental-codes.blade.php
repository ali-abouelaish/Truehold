@extends('layouts.admin')

@section('title', 'My Rental Codes')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-0 text-primary fw-bold" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <i class="fas fa-receipt text-primary me-2" style="filter: brightness(1.2);"></i>My Rental Codes
                    </h1>
                    <p class="text-muted mb-0">View and manage all your rental code applications</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('agent.profile.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('rental-codes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create New
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
                        <a class="nav-link active" href="{{ route('agent.profile.rental-codes') }}">
                            <i class="fas fa-receipt me-2"></i>My Rental Codes
                        </a>
                        <a class="nav-link" href="{{ route('agent.profile.earnings') }}">
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

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Rental Codes</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('agent.profile.rental-codes') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">All Methods</option>
                                    <option value="Cash" {{ request('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Transfer" {{ request('payment_method') === 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('agent.profile.rental-codes') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental Codes Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Rental Codes ({{ $rentalCodes->total() }} total)</h6>
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
                                        <th>Your Role</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalCodes as $code)
                                        <tr>
                                            <td>
                                                <a href="{{ route('rental-codes.show', $code->id) }}" class="text-primary fw-bold">
                                                    {{ $code->rental_code }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $code->client->full_name ?? 'Unknown' }}</span>
                                                    <small class="text-muted">{{ $code->client->email ?? '' }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $code->rental_date->format('M d, Y') }}</td>
                                            <td class="fw-bold">£{{ number_format($code->consultation_fee, 2) }}</td>
                                            <td>
                                                @php
                                                    $isRentalAgent = $code->rent_by_agent_name === ($agent->company_name ?? $user->name);
                                                    $isMarketingAgent = $code->marketing_agent_name === $user->name;
                                                @endphp
                                                @if($isRentalAgent && $isMarketingAgent)
                                                    <span class="badge badge-primary">Both</span>
                                                @elseif($isRentalAgent)
                                                    <span class="badge badge-success">Rental Agent</span>
                                                @elseif($isMarketingAgent)
                                                    <span class="badge badge-info">Marketing Agent</span>
                                                @else
                                                    <span class="badge badge-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $code->status === 'completed' ? 'success' : ($code->status === 'approved' ? 'primary' : ($code->status === 'pending' ? 'warning' : 'danger')) }}">
                                                    {{ ucfirst($code->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $paymentMethod = $code->payment_method ?? 'N/A';
                                                    $emoji = '';
                                                    if (strtolower($paymentMethod) === 'transfer' || strtolower($paymentMethod) === 'card machine') {
                                                        $emoji = '⚡';
                                                    } elseif (strtolower($paymentMethod) === 'cash') {
                                                        $emoji = '💰';
                                                    }
                                                @endphp
                                                <div class="d-flex flex-column">
                                                    <span class="badge badge-{{ strtolower($paymentMethod) === 'transfer' || strtolower($paymentMethod) === 'card machine' ? 'info' : (strtolower($paymentMethod) === 'cash' ? 'success' : 'secondary') }}">
                                                        @if($emoji)
                                                            <span class="me-1">{{ $emoji }}</span>
                                                        @endif
                                                        {{ $paymentMethod }}
                                                    </span>
                                                    @if($code->paid)
                                                        <small class="text-success mt-1">
                                                            <i class="fas fa-check me-1"></i>Paid
                                                        </small>
                                                        @if($code->paid_at)
                                                            <small class="text-muted">{{ $code->paid_at->format('M d, Y') }}</small>
                                                        @endif
                                                    @else
                                                        <small class="text-warning mt-1">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rental-codes.show', $code->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($code->status !== 'cancelled')
                                                        <a href="{{ route('rental-codes.edit', $code->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $rentalCodes->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-receipt fa-4x mb-3"></i>
                            <h4>No Rental Codes Found</h4>
                            <p>You haven't created any rental codes yet, or no codes match your current filters.</p>
                            <a href="{{ route('rental-codes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Your First Rental Code
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
