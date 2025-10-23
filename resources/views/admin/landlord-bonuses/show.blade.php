@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord-bonuses.index') }}">Landlord Bonuses</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
                <h4 class="page-title">Landlord Bonus Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Landlord Bonus Information</h5>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('landlord-bonuses.edit', $landlordBonus) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <a href="{{ route('landlord-bonuses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Bonus Code</h6>
                                <p class="mb-0">
                                    <span class="badge bg-primary fs-5">{{ $landlordBonus->bonus_code }}</span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Date</h6>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    {{ $landlordBonus->date->format('F d, Y') }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Agent</h6>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user-tie text-muted"></i>
                                    </div>
                                    <span class="fw-bold">{{ $landlordBonus->agent->user->name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Landlord</h6>
                                <p class="mb-0">
                                    <i class="fas fa-building text-info me-2"></i>
                                    <span class="fw-bold">{{ $landlordBonus->landlord }}</span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Property</h6>
                                <p class="mb-0">
                                    <i class="fas fa-home text-success me-2"></i>
                                    <span class="text-primary">{{ $landlordBonus->property }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Client</h6>
                                <p class="mb-0">
                                    <i class="fas fa-user text-warning me-2"></i>
                                    <span class="text-success fw-bold">{{ $landlordBonus->client }}</span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Commission Amount</h6>
                                <p class="mb-0">
                                    <i class="fas fa-pound-sign text-success me-2"></i>
                                    <span class="fw-bold text-success fs-4">£{{ number_format($landlordBonus->commission, 2) }}</span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Bonus Split</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-primary fw-bold">Agent Commission</span>
                                                <span class="text-primary fw-bold">£{{ number_format($landlordBonus->agent_commission, 2) }}</span>
                                            </div>
                                            <small class="text-muted">
                                                @if($landlordBonus->bonus_split === '100_0')
                                                    100% of total
                                                @else
                                                    55% of total
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary fw-bold">Agency Commission</span>
                                                <span class="text-secondary fw-bold">£{{ number_format($landlordBonus->agency_commission, 2) }}</span>
                                            </div>
                                            <small class="text-muted">
                                                @if($landlordBonus->bonus_split === '100_0')
                                                    0% of total
                                                @else
                                                    45% of total
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Status</h6>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                                        'paid' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Paid'],
                                        'cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Cancelled']
                                    ];
                                    $config = $statusConfig[$landlordBonus->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }} fs-6">
                                    <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                    {{ $config['text'] }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Created By</h6>
                                <p class="mb-0">
                                    <i class="fas fa-user-plus text-secondary me-2"></i>
                                    {{ $landlordBonus->creator->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($landlordBonus->notes)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{{ $landlordBonus->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Timestamps</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            Created: {{ $landlordBonus->created_at->format('M d, Y \a\t g:i A') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-edit me-1"></i>
                                            Updated: {{ $landlordBonus->updated_at->format('M d, Y \a\t g:i A') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
