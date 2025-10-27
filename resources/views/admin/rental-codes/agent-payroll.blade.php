@extends('layouts.admin')

@section('title', $agentName . ' - Commission File')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $agentName }} - Commission File</h1>
            <p class="text-muted">Complete commission breakdown for {{ $agentName }}</p>
        </div>
        <div>
            <a href="{{ route('rental-codes.agent-earnings') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Commission Report
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151 !important; border-left: 4px solid #4e73df !important; border-radius: 12px; box-shadow: 0 10px 24px rgba(0,0,0,0.35) !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="letter-spacing: .04em; opacity:.9;">Total Earnings</div>
                            <div class="h3 mb-0 font-weight-bold text-white">£{{ number_format($agent['total_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <div style="background: rgba(255,255,255,0.12); border-radius: 10px; padding: 10px;">
                                <i class="fas fa-pound-sign fa-lg text-white" style="opacity: 0.95;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151 !important; border-left: 4px solid #1cc88a !important; border-radius: 12px; box-shadow: 0 10px 24px rgba(0,0,0,0.35) !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="letter-spacing: .04em; opacity:.9;">Agent Earnings</div>
                            <div class="h3 mb-0 font-weight-bold text-white">£{{ number_format($agent['agent_earnings'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <div style="background: rgba(255,255,255,0.12); border-radius: 10px; padding: 10px;">
                                <i class="fas fa-user-tie fa-lg text-white" style="opacity: 0.95;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151 !important; border-left: 4px solid #36b9cc !important; border-radius: 12px; box-shadow: 0 10px 24px rgba(0,0,0,0.35) !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="letter-spacing: .04em; opacity:.9;">Total Transactions</div>
                            <div class="h3 mb-0 font-weight-bold text-white">{{ $agent['transaction_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div style="background: rgba(255,255,255,0.12); border-radius: 10px; padding: 10px;">
                                <i class="fas fa-list fa-lg text-white" style="opacity: 0.95;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151 !important; border-left: 4px solid #f6c23e !important; border-radius: 12px; box-shadow: 0 10px 24px rgba(0,0,0,0.35) !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="letter-spacing: .04em; opacity:.9;">Outstanding</div>
                            <div class="h3 mb-0 font-weight-bold text-white">£{{ number_format($agent['outstanding_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <div style="background: rgba(255,255,255,0.12); border-radius: 10px; padding: 10px;">
                                <i class="fas fa-clock fa-lg text-white" style="opacity: 0.95;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Rental Codes Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-contract mr-2"></i>Rental Codes ({{ count($agent['transactions']) }})
                        </h6>
                        @auth
                        @if(auth()->user()->role === 'admin' && count($agent['transactions']) > 0)
                        <form id="bulkPaidForm" method="POST" action="{{ route('rental-codes.bulk-mark-paid') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="rental_code_ids[]" value="" id="dummyIdsPlaceholder" style="display:none;">
                            <button type="submit" class="btn btn-success btn-sm" onclick="return submitBulkPaid(event)">
                                <i class="fas fa-check mr-1"></i> Mark Selected Paid
                            </button>
                        </form>
                        @endif
                        @endauth
                    </div>
                </div>
                <div class="card-body">
                    @if(count($agent['transactions']) > 0)
                        @auth
                        @if(auth()->user()->role === 'admin')
                        <div class="mb-3">
                            <label class="mr-2"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"> Select All</label>
                        </div>
                        @endif
                        @endauth
                        @foreach($agent['transactions'] as $transaction)
                        <div class="row mb-3 p-3 border rounded" style="{{ $transaction['paid'] ? 'background: linear-gradient(135deg, #065f46 0%, #059669 100%) !important; border: 2px solid #10b981 !important;' : 'background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important; border: 2px solid #495057 !important;' }}">
                            <div class="col-md-8 d-flex align-items-start">
                                @auth
                                @if(auth()->user()->role === 'admin' && !$transaction['paid'])
                                <div class="mr-3 mt-1">
                                    <input type="checkbox" class="bulk-checkbox" value="{{ $transaction['id'] }}">
                                </div>
                                @endif
                                @endauth
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-{{ $transaction['payment_method'] === 'Transfer' || $transaction['payment_method'] === 'Card Machine' ? 'purple' : ($transaction['payment_method'] === 'Cash' ? 'success' : 'secondary') }} mr-2" style="font-size: 0.9rem; padding: 0.5rem 0.8rem;">
                                        @if($transaction['payment_method'] === 'Transfer' || $transaction['payment_method'] === 'Card Machine')
                                            ⚡ {{ $transaction['payment_method'] }}
                                        @elseif($transaction['payment_method'] === 'Cash')
                                            💰 {{ $transaction['payment_method'] }}
                                        @else
                                            {{ $transaction['payment_method'] }}
                                        @endif
                                    </span>
                                    <strong style="font-size: 1.1rem; color: #ffffff;">{{ $transaction['code'] }}</strong>
                                    @if($transaction['is_marketing_earnings'] ?? false)
                                        <span class="badge badge-warning ml-2" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">Marketing</span>
                                    @endif
                                    @if($transaction['paid'])
                                        <span class="badge badge-success ml-2" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">✓ Paid</span>
                                    @else
                                        <span class="badge badge-warning ml-2" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">Pending</span>
                                    @endif
                                </div>
                                <div class="text-muted small" style="color: #bdc3c7 !important; font-weight: 500;">
                                    Date: {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                                    @if($transaction['client_count'] > 1)
                                        | Clients: {{ $transaction['client_count'] }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row text-sm">
                                    <div class="col-6">
                                        <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">Total Fee:</div>
                                        <div class="font-weight-bold" style="color: #ffffff !important; font-size: 1.1rem;">£{{ number_format($transaction['total_fee'], 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">Agent Cut:</div>
                                        <div class="font-weight-bold text-success" style="color: #2ecc71 !important; font-size: 1.1rem;">£{{ number_format($transaction['agent_cut'], 2) }}</div>
                                    </div>
                                </div>
                                @if($transaction['vat_amount'] > 0 || $transaction['marketing_deduction'] > 0)
                                <div class="mt-2 pt-2 border-top" style="border-top: 2px solid #7f8c8d !important;">
                                    <div class="text-xs text-muted" style="color: #bdc3c7 !important; font-weight: 600;">Deductions:</div>
                                    @if($transaction['vat_amount'] > 0)
                                        <div class="text-xs text-warning" style="color: #f39c12 !important; font-weight: 600;">VAT: £{{ number_format($transaction['vat_amount'], 2) }}</div>
                                    @endif
                                    @if($transaction['marketing_deduction'] > 0)
                                        <div class="text-xs text-danger" style="color: #e74c3c !important; font-weight: 600;">Marketing: £{{ number_format($transaction['marketing_deduction'], 2) }}</div>
                                    @endif
                                    @if($transaction['marketing_agent'] ?? false)
                                        <div class="text-xs text-info" style="color: #3498db !important; font-weight: 600;">Marketing Agent: {{ $transaction['marketing_agent'] }}</div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-file-contract fa-3x mb-3"></i>
                            <p>No rental codes found for this agent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Landlord Bonuses Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-gift mr-2"></i>Landlord Bonuses ({{ count($agent['landlord_bonuses'] ?? []) }})
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($agent['landlord_bonuses'] ?? []) > 0)
                        @foreach($agent['landlord_bonuses'] as $bonus)
                        <div class="row mb-3 p-3 border rounded bg-green-50 border-green-200">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-info mr-2">
                                        <i class="fas fa-gift mr-1"></i>{{ $bonus['bonus_code'] }}
                                    </span>
                                    <strong>{{ $bonus['property'] }}</strong>
                                    @if($bonus['status'] === 'paid')
                                        <span class="badge badge-success ml-2">✓ Paid</span>
                                    @else
                                        <span class="badge badge-warning ml-2">Pending</span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    Landlord: {{ $bonus['landlord'] }} | Client: {{ $bonus['client'] }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row text-sm">
                                    <div class="col-6">
                                        <div class="text-muted">Total Commission:</div>
                                        <div class="font-weight-bold">£{{ number_format($bonus['commission'], 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted">Agent Commission:</div>
                                        <div class="font-weight-bold text-success">£{{ number_format($bonus['agent_commission'], 2) }}</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="text-xs text-muted">Split: {{ $bonus['bonus_split'] === '100_0' ? '100% Agent' : '55% Agent, 45% Agency' }}</div>
                                    <div class="text-xs text-muted">Date: {{ \Carbon\Carbon::parse($bonus['date'])->format('M d, Y') }}</div>
                                </div>
                                @if($bonus['notes'])
                                <div class="mt-2 text-xs text-muted">
                                    <strong>Notes:</strong> {{ $bonus['notes'] }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-gift fa-3x mb-3"></i>
                            <p>No landlord bonuses found for this agent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Deductions Summary -->
    @if($agent['vat_deductions'] > 0 || $agent['marketing_deductions'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deductions Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($agent['vat_deductions'] > 0)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">VAT Deductions:</span>
                                <span class="font-weight-bold text-warning">£{{ number_format($agent['vat_deductions'], 2) }}</span>
                            </div>
                        </div>
                        @endif
                        @if($agent['marketing_deductions'] > 0)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Marketing Deductions:</span>
                                <span class="font-weight-bold text-danger">£{{ number_format($agent['marketing_deductions'], 2) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleSelectAll(source) {
    document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = source.checked);
}

function submitBulkPaid(e) {
    e.preventDefault();
    const ids = Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) {
        alert('Select at least one unpaid rental.');
        return false;
    }
    const form = document.getElementById('bulkPaidForm');
    // Clear previous hidden inputs (except placeholder)
    Array.from(form.querySelectorAll('input[name="rental_code_ids[]"]')).forEach(el => el.remove());
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'rental_code_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    form.submit();
    return true;
}
</script>
@endpush