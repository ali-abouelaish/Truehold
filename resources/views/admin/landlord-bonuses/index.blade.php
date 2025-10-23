@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Landlord Bonuses</li>
                    </ol>
                </div>
                <h4 class="page-title">Landlord Bonuses</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Landlord Bonus Records</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('landlord-bonuses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Landlord Bonus
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Bonus Code</th>
                                    <th>Date</th>
                                    <th>Agent</th>
                                    <th>Landlord</th>
                                    <th>Property</th>
                                    <th>Client</th>
                                    <th>Commission</th>
                                    <th>Agent Split</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($landlordBonuses as $bonus)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $bonus->bonus_code }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $bonus->date->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user-tie text-muted"></i>
                                            </div>
                                            <span>{{ $bonus->agent->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $bonus->landlord }}</span>
                                    </td>
                                    <td>
                                        <span class="text-primary">{{ $bonus->property }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $bonus->client }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">£{{ number_format($bonus->commission, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">£{{ number_format($bonus->agent_commission, 2) }}</span>
                                            <small class="text-muted">
                                                @if($bonus->bonus_split === '100_0')
                                                    100% Agent
                                                @else
                                                    55% Agent
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                                                'paid' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Paid'],
                                                'cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Cancelled']
                                            ];
                                            $config = $statusConfig[$bonus->status] ?? $statusConfig['pending'];
                                        @endphp
                                        <span class="badge bg-{{ $config['class'] }}">
                                            <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                            {{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('landlord-bonuses.show', $bonus) }}" class="dropdown-item">
                                                        <i class="fas fa-eye me-2"></i>View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('landlord-bonuses.edit', $bonus) }}" class="dropdown-item">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('landlord-bonuses.destroy', $bonus) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this landlord bonus?')">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-gift fa-3x mb-3"></i>
                                            <h5>No Landlord Bonuses Found</h5>
                                            <p>Start by adding your first landlord bonus record.</p>
                                            <a href="{{ route('landlord-bonuses.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Add Landlord Bonus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($landlordBonuses->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $landlordBonuses->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
