@extends('layouts.admin')

@section('page-title', 'Marketing Agents Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-bullhorn mr-2"></i>Marketing Agents Management
                    </h3>
                    <div>
                        <a href="{{ route('rental-codes.agent-earnings') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-line mr-1"></i>View Earnings
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Add Marketing Agent Form -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-plus mr-2"></i>Add Marketing Agent
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('marketing-agents.store') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">Select User</label>
                                            <select name="user_id" id="user_id" class="form-select" required>
                                                <option value="">Choose a user...</option>
                                                @foreach($allUsers as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus mr-1"></i>Assign as Marketing Agent
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>Marketing Agent Info
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>What are Marketing Agents?</strong></p>
                                    <ul class="mb-0 small">
                                        <li>Users who can receive marketing commissions</li>
                                        <li>Earn £30-£40 per rental transaction they market</li>
                                        <li>Can be assigned to rental codes as marketing agents</li>
                                        <li>Separate from regular rent agents</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Marketing Agents -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fas fa-users mr-2"></i>Current Marketing Agents ({{ $marketingAgents->count() }})
                            </h5>
                            
                            @if($marketingAgents->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($marketingAgents as $agent)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                {{ substr($agent->name, 0, 1) }}
                                                            </div>
                                                            <strong>{{ $agent->name }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>{{ $agent->email }}</td>
                                                    <td>
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-bullhorn mr-1"></i>Marketing Agent
                                                        </span>
                                                    </td>
                                                    <td>{{ $agent->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <form action="{{ route('marketing-agents.remove', $agent) }}" method="POST" class="d-inline" 
                                                              onsubmit="return confirm('Are you sure you want to remove this marketing agent role?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-user-minus mr-1"></i>Remove Role
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Marketing Agents</h5>
                                    <p class="text-muted">Add users as marketing agents to start managing marketing commissions.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endsection
