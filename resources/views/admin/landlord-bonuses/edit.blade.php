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
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Landlord Bonus</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Landlord Bonus</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('landlord-bonuses.update', $landlordBonus) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', $landlordBonus->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="agent_id" class="form-label">Agent <span class="text-danger">*</span></label>
                                <select class="form-select @error('agent_id') is-invalid @enderror" 
                                        id="agent_id" name="agent_id" required>
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" 
                                                {{ old('agent_id', $landlordBonus->agent_id) == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="landlord" class="form-label">Landlord <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('landlord') is-invalid @enderror" 
                                       id="landlord" name="landlord" value="{{ old('landlord', $landlordBonus->landlord) }}" 
                                       placeholder="Enter landlord name" required>
                                @error('landlord')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="property" class="form-label">Property <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('property') is-invalid @enderror" 
                                       id="property" name="property" value="{{ old('property', $landlordBonus->property) }}" 
                                       placeholder="Enter property address" required>
                                @error('property')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client" class="form-label">Client <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('client') is-invalid @enderror" 
                                       id="client" name="client" value="{{ old('client', $landlordBonus->client) }}" 
                                       placeholder="Enter client name" required>
                                @error('client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="commission" class="form-label">Commission Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('commission') is-invalid @enderror" 
                                           id="commission" name="commission" value="{{ old('commission', $landlordBonus->commission) }}" 
                                           placeholder="0.00" required>
                                    @error('commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bonus_split" class="form-label">Bonus Split <span class="text-danger">*</span></label>
                                <select class="form-select @error('bonus_split') is-invalid @enderror" 
                                        id="bonus_split" name="bonus_split" required>
                                    <option value="55_45" {{ old('bonus_split', $landlordBonus->bonus_split) == '55_45' ? 'selected' : '' }}>
                                        Standard Split (55% Agent, 45% Agency)
                                    </option>
                                    <option value="100_0" {{ old('bonus_split', $landlordBonus->bonus_split) == '100_0' ? 'selected' : '' }}>
                                        Full Bonus (100% Agent, 0% Agency)
                                    </option>
                                </select>
                                @error('bonus_split')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Choose how the bonus is split between agent and agency</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', $landlordBonus->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('status', $landlordBonus->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="cancelled" {{ old('status', $landlordBonus->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Enter any additional notes">{{ old('notes', $landlordBonus->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('landlord-bonuses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Landlord Bonus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
