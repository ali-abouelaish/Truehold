@extends('layouts.admin')

@section('title', 'My Clients')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-0 text-primary fw-bold" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <i class="fas fa-users text-primary me-2" style="filter: brightness(1.2);"></i>My Clients
                    </h1>
                    <p class="text-muted mb-0">Manage your client relationships and track their rental history</p>
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
                        <a class="nav-link" href="{{ route('agent.profile.earnings') }}">
                            <i class="fas fa-chart-line me-2"></i>Earnings & Deductions
                        </a>
                        <a class="nav-link active" href="{{ route('agent.profile.clients') }}">
                            <i class="fas fa-users me-2"></i>My Clients
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Client List ({{ $clients->total() }} total)</h6>
                </div>
                <div class="card-body">
                    @if($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Client Details</th>
                                        <th>Contact Info</th>
                                        <th>Company/University</th>
                                        <th>Rental Codes</th>
                                        <th>Total Value</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-primary">{{ $client->full_name }}</span>
                                                    <small class="text-muted">DOB: {{ $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('M d, Y') : 'N/A' }}</small>
                                                    <small class="text-muted">Nationality: {{ $client->nationality ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span><i class="fas fa-envelope me-1"></i>{{ $client->email }}</span>
                                                    <span><i class="fas fa-phone me-1"></i>{{ $client->phone_number }}</span>
                                                    <span><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($client->current_address, 30) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $client->company_university_name ?? 'N/A' }}</span>
                                                    <small class="text-muted">{{ $client->position_role ?? 'N/A' }}</small>
                                                    @if($client->company_university_address)
                                                        <small class="text-muted">{{ Str::limit($client->company_university_address, 30) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="h5 mb-0 font-weight-bold text-primary">{{ $client->rentalCodes->count() }}</div>
                                                    <small class="text-muted">Total Codes</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $totalValue = $client->rentalCodes->sum('consultation_fee');
                                                @endphp
                                                <div class="text-center">
                                                    <div class="h5 mb-0 font-weight-bold text-success">£{{ number_format($totalValue, 2) }}</div>
                                                    <small class="text-muted">Total Value</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($client->rentalCodes->count() > 0)
                                                    @php
                                                        $lastRental = $client->rentalCodes->sortByDesc('created_at')->first();
                                                    @endphp
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold">{{ $lastRental->rental_code }}</span>
                                                        <small class="text-muted">{{ $lastRental->created_at->format('M d, Y') }}</small>
                                                        <span class="badge badge-{{ $lastRental->status === 'completed' ? 'success' : ($lastRental->status === 'approved' ? 'primary' : ($lastRental->status === 'pending' ? 'warning' : 'danger')) }}">
                                                            {{ ucfirst($lastRental->status) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No activity</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#clientModal{{ $client->id }}" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('rental-codes.create') }}?client_id={{ $client->id }}" 
                                                       class="btn btn-sm btn-outline-success" title="Create Rental Code">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $clients->links() }}
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-users fa-4x mb-3"></i>
                            <h4>No Clients Found</h4>
                            <p>You haven't been assigned to any clients yet.</p>
                            <a href="{{ route('rental-codes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Rental Code with New Client
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Client Detail Modals -->
@foreach($clients as $client)
<div class="modal fade" id="clientModal{{ $client->id }}" tabindex="-1" aria-labelledby="clientModalLabel{{ $client->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel{{ $client->id }}">Client Details - {{ $client->full_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Personal Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Full Name:</strong></td>
                                <td>{{ $client->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date of Birth:</strong></td>
                                <td>{{ $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nationality:</strong></td>
                                <td>{{ $client->nationality ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Address:</strong></td>
                                <td>{{ $client->current_address ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Contact Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $client->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $client->phone_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Company/University:</strong></td>
                                <td>{{ $client->company_university_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Position/Role:</strong></td>
                                <td>{{ $client->position_role ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($client->rentalCodes->count() > 0)
                    <hr>
                    <h6 class="text-primary">Rental History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Rental Code</th>
                                    <th>Date</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->rentalCodes as $rental)
                                    <tr>
                                        <td>
                                            <a href="{{ route('rental-codes.show', $rental->id) }}" class="text-primary">
                                                {{ $rental->rental_code }}
                                            </a>
                                        </td>
                                        <td>{{ $rental->rental_date->format('M d, Y') }}</td>
                                        <td>£{{ number_format($rental->consultation_fee, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $rental->status === 'completed' ? 'success' : ($rental->status === 'approved' ? 'primary' : ($rental->status === 'pending' ? 'warning' : 'danger')) }}">
                                                {{ ucfirst($rental->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($rental->paid)
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
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('rental-codes.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create New Rental Code
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
