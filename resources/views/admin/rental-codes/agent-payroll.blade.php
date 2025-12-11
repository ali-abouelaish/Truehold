@extends('layouts.admin')

@section('title', $agentName . ' - Commission File')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white">{{ $agentName }} - Commission File</h1>
            <p class="text-white-50">Complete commission breakdown for {{ $agentName }}</p>
            <!-- Date Range Indicator -->
            @if($startDate && $endDate)
                <div class="mt-2">
                    <span class="badge bg-primary" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        <span class="ms-2 opacity-75">({{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days)</span>
                    </span>
                </div>
            @endif
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
                            @php
                                // Calculate total agent earnings from all transactions (rental + marketing)
                                $totalAgentEarnings = 0;
                                foreach ($agent['transactions'] ?? [] as $transaction) {
                                    $totalAgentEarnings += (float) ($transaction['agent_cut'] ?? 0);
                                }
                                // Also include landlord bonuses
                                foreach ($agent['landlord_bonuses'] ?? [] as $bonus) {
                                    $totalAgentEarnings += (float) ($bonus['agent_commission'] ?? 0);
                                }
                            @endphp
                            <div class="h3 mb-0 font-weight-bold text-white">£{{ number_format($totalAgentEarnings, 2) }}</div>
                            <small class="text-white-50" style="font-size: 0.7rem;">Rental + Marketing</small>
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
            <div class="card shadow h-100 py-2" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151 !important; border-left: 4px solid #e83e8c !important; border-radius: 12px; box-shadow: 0 10px 24px rgba(0,0,0,0.35) !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1" style="letter-spacing: .04em; opacity:.9;">Marketing Earnings</div>
                            <div class="h3 mb-0 font-weight-bold text-white">£{{ number_format($agent['marketing_agent_earnings'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <div style="background: rgba(255,255,255,0.12); border-radius: 10px; padding: 10px;">
                                <i class="fas fa-bullhorn fa-lg text-white" style="opacity: 0.95;"></i>
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

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151 !important; border-radius: 12px;">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid #374151;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-filter mr-2"></i>Filters & Commission Cycles
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" class="row g-3" id="filterForm">
                        <!-- Quick Commission Cycle Selector -->
                        <div class="col-12 mb-3">
                            <div class="p-3" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 8px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="text-white mb-0">
                                        <i class="fas fa-calendar-check me-1"></i>Quick Select Commission Cycle (11th to 10th)
                                    </label>
                                    <span class="text-white-50 small">Click to apply</span>
                                </div>
                                
                                <!-- Commission Cycle Buttons -->
                                <div class="row g-2 mb-2">
                                    @php
                                        $currentDate = now();
                                        $cycles = [];
                                        
                                        // Generate last 8 commission cycles
                                        for ($i = 0; $i < 8; $i++) {
                                            $cycleDate = $currentDate->copy()->subMonths($i);
                                            
                                            if ($cycleDate->day <= 10) {
                                                $cycleStart = $cycleDate->copy()->subMonthNoOverflow()->day(11);
                                                $cycleEnd = $cycleDate->copy()->day(10);
                                            } else {
                                                $cycleStart = $cycleDate->copy()->day(11);
                                                $cycleEnd = $cycleDate->copy()->addMonthNoOverflow()->day(10);
                                            }
                                            
                                            // Check if this cycle matches current filter
                                            $isSelected = ($startDate === $cycleStart->toDateString() && 
                                                          $endDate === min($currentDate->toDateString(), $cycleEnd->toDateString()));
                                            
                                            $cycles[] = [
                                                'start' => $cycleStart->toDateString(),
                                                'end' => min($currentDate->toDateString(), $cycleEnd->toDateString()),
                                                'label' => $cycleStart->format('d-M') . ' to ' . $cycleEnd->format('d-M Y'),
                                                'shortLabel' => $cycleStart->format('M Y'),
                                                'isCurrent' => $i === 0,
                                                'isSelected' => $isSelected
                                            ];
                                        }
                                    @endphp
                                    
                                    @foreach($cycles as $cycle)
                                        <div class="col-6 col-md-3">
                                            <button type="button" 
                                                    onclick="selectCycle('{{ $cycle['start'] }}', '{{ $cycle['end'] }}')"
                                                    class="btn w-100 btn-sm {{ $cycle['isSelected'] ? 'btn-success' : ($cycle['isCurrent'] ? 'btn-primary' : 'btn-outline-light') }}" style="font-size: 0.75rem;">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        @if($cycle['isSelected'])
                                                            <i class="fas fa-check-circle me-1"></i>
                                                        @elseif($cycle['isCurrent'])
                                                            <i class="fas fa-star me-1"></i>
                                                        @endif
                                                        <span class="fw-bold">{{ $cycle['shortLabel'] }}</span>
                                                    </div>
                                                    <span class="small mt-1">{{ $cycle['label'] }}</span>
                                                </div>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Quick Preset Buttons -->
                                <div class="pt-2 mt-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-white-50 small fw-bold">Quick Presets:</span>
                                    </div>
                                    <div class="row g-2">
                                        @php
                                            $last3MonthsStart = $currentDate->copy()->subMonths(3);
                                            $last3MonthsEnd = $currentDate->toDateString();
                                            $last6MonthsStart = $currentDate->copy()->subMonths(6);
                                            $last6MonthsEnd = $currentDate->toDateString();
                                            $thisYearStart = $currentDate->copy()->startOfYear();
                                            $thisYearEnd = $currentDate->toDateString();
                                            $lastYearStart = $currentDate->copy()->subYear()->startOfYear();
                                            $lastYearEnd = $currentDate->copy()->subYear()->endOfYear();
                                        @endphp
                                        
                                        <div class="col-6 col-md-3">
                                            <button type="button" 
                                                    onclick="selectCycle('{{ $last3MonthsStart->toDateString() }}', '{{ $last3MonthsEnd }}')"
                                                    class="btn btn-outline-light btn-sm w-100" style="font-size: 0.75rem;">
                                                <i class="fas fa-calendar-week me-1"></i>Last 3 Months
                                            </button>
                                        </div>
                                        
                                        <div class="col-6 col-md-3">
                                            <button type="button" 
                                                    onclick="selectCycle('{{ $last6MonthsStart->toDateString() }}', '{{ $last6MonthsEnd }}')"
                                                    class="btn btn-outline-light btn-sm w-100" style="font-size: 0.75rem;">
                                                <i class="fas fa-calendar me-1"></i>Last 6 Months
                                            </button>
                                        </div>
                                        
                                        <div class="col-6 col-md-3">
                                            <button type="button" 
                                                    onclick="selectCycle('{{ $thisYearStart->toDateString() }}', '{{ $thisYearEnd }}')"
                                                    class="btn btn-outline-light btn-sm w-100" style="font-size: 0.75rem;">
                                                <i class="fas fa-calendar-alt me-1"></i>This Year
                                            </button>
                                        </div>
                                        
                                        <div class="col-6 col-md-3">
                                            <button type="button" 
                                                    onclick="selectCycle('{{ $lastYearStart->toDateString() }}', '{{ $lastYearEnd->toDateString() }}')"
                                                    class="btn btn-outline-light btn-sm w-100" style="font-size: 0.75rem;">
                                                <i class="fas fa-history me-1"></i>Last Year
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="start_date" class="form-label text-white">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label text-white">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-light">
                                <i class="fas fa-times me-1"></i> Clear Filters
                            </a>
                        </div>
                    </form>
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
                        @php
                            $rt = $agent['rental_totals'] ?? ['count'=>0,'agent_cut'=>0,'paid'=>0,'entitled'=>0,'outstanding'=>0];
                        @endphp
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">Rental Count</div>
                                <div class="font-weight-bold" style="color: #ffffff !important; font-size: 1.1rem;">{{ $rt['count'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">Agent Earnings</div>
                                <div class="font-weight-bold text-success" style="color: #2ecc71 !important; font-size: 1.1rem;">£{{ number_format($rt['agent_cut'], 2) }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">Paid</div>
                                <div class="font-weight-bold text-success" style="color: #10b981 !important; font-size: 1.1rem;">£{{ number_format($rt['paid'], 2) }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">To Be Paid</div>
                                <div class="font-weight-bold text-warning" style="color: #f59e0b !important; font-size: 1.1rem;">£{{ number_format($rt['entitled'], 2) }}</div>
                            </div>
                        </div>
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
                                    <span class="badge badge-{{ $transaction['payment_method'] === 'Transfer' || $transaction['payment_method'] === 'Card Machine' || $transaction['payment_method'] === 'Card machine' ? 'purple' : ($transaction['payment_method'] === 'Cash' ? 'success' : 'secondary') }} mr-2" style="font-size: 0.9rem; padding: 0.5rem 0.8rem;">
                                        @if($transaction['payment_method'] === 'Transfer')
                                            ⚡ {{ $transaction['payment_method'] }}
                                        @elseif($transaction['payment_method'] === 'Card Machine' || $transaction['payment_method'] === 'Card machine')
                                            💳 {{ $transaction['payment_method'] }}
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
                                        <div class="text-muted" style="color: #ecf0f1 !important; font-weight: 600;">{{ ($transaction['is_marketing_earnings'] ?? false) ? 'Marketing Cut:' : 'Agent Cut:' }}</div>
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
                        @php
                            $bt = $agent['bonus_totals'] ?? ['count'=>0,'agent_commission'=>0,'paid'=>0,'entitled'=>0,'outstanding'=>0];
                        @endphp
                        <div class="row mb-3 p-3" style="background-color: #374151 !important; border-radius: 8px; border: 1px solid #4b5563 !important;">
                            <div class="col-md-3">
                                <div style="color: #d1d5db !important; font-weight: 600; font-size: 0.95rem;">Bonuses</div>
                                <div class="font-weight-bold" style="color: #ffffff !important; font-size: 1.2rem;">{{ $bt['count'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div style="color: #d1d5db !important; font-weight: 600; font-size: 0.95rem;">Agent Commission</div>
                                <div class="font-weight-bold" style="color: #60a5fa !important; font-size: 1.3rem;">£{{ number_format($bt['agent_commission'], 2) }}</div>
                            </div>
                            <div class="col-md-3">
                                <div style="color: #d1d5db !important; font-weight: 600; font-size: 0.95rem;">Paid</div>
                                <div class="font-weight-bold" style="color: #34d399 !important; font-size: 1.2rem;">£{{ number_format($bt['paid'], 2) }}</div>
                            </div>
                            <div class="col-md-3">
                                <div style="color: #d1d5db !important; font-weight: 600; font-size: 0.95rem;">To Be Paid</div>
                                <div class="font-weight-bold" style="color: #fbbf24 !important; font-size: 1.3rem;">£{{ number_format($bt['entitled'], 2) }}</div>
                            </div>
                        </div>
                        @auth
                        @if(auth()->user()->role === 'admin')
                        <div class="mb-3">
                            <label class="mr-2"><input type="checkbox" id="bonusSelectAll" onchange="toggleSelectAllBonuses(this)"> Select All</label>
                            <form id="bonusBulkPaidForm" method="POST" action="{{ route('landlord-bonuses.bulk-mark-paid') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="landlord_bonus_ids[]" value="" id="dummyBonusIdsPlaceholder" style="display:none;">
                                <button type="submit" class="btn btn-success btn-sm" onclick="return submitBonusesBulkPaid(event)">
                                    <i class="fas fa-check mr-1"></i> Mark Selected Bonuses Paid
                                </button>
                            </form>
                        </div>
                        @endif
                        @endauth
                        @foreach($agent['landlord_bonuses'] as $bonus)
                        <div class="row mb-3 p-3 border rounded bg-dark text-light border-secondary" style="background-color: #1f2937 !important; color: #e5e7eb !important; border-color: #374151 !important;">
                            <div class="col-md-8 d-flex align-items-start">
                                @auth
                                @if(auth()->user()->role === 'admin' && ($bonus['status'] ?? 'pending') !== 'paid')
                                <div class="mr-3 mt-1">
                                    <input type="checkbox" class="bonus-bulk-checkbox" value="{{ $bonus['id'] ?? '' }}">
                                </div>
                                @endif
                                @endauth
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
                                <div class="text-white-50 small">
                                    Landlord: {{ $bonus['landlord'] }} | Client: {{ $bonus['client'] }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row text-sm">
                                    <div class="col-6">
                                        <div class="text-white-50" style="color: #e5e7eb !important;">Total Commission:</div>
                                        <div class="font-weight-bold">£{{ number_format($bonus['commission'], 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-white-50" style="color: #e5e7eb !important;">Agent Commission:</div>
                                        <div class="font-weight-bold text-success">£{{ number_format($bonus['agent_commission'], 2) }}</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="text-xs text-white-50" style="color: #e5e7eb !important;">Split: {{ $bonus['bonus_split'] === '100_0' ? '100% Agent' : '55% Agent, 45% Agency' }}</div>
                                    <div class="text-xs text-white-50" style="color: #e5e7eb !important;">Date: {{ \Carbon\Carbon::parse($bonus['date'])->format('M d, Y') }}</div>
                                </div>
                                @if($bonus['notes'])
                                <div class="mt-2 text-xs text-white-50" style="color: #e5e7eb !important;">
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

// Commission cycle selection function
function selectCycle(startDate, endDate) {
    // Update the date inputs
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    
    // Submit the form automatically
    document.getElementById('filterForm').submit();
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

function toggleSelectAllBonuses(source) {
    document.querySelectorAll('.bonus-bulk-checkbox').forEach(cb => cb.checked = source.checked);
}

function submitBonusesBulkPaid(e) {
    e.preventDefault();
    const ids = Array.from(document.querySelectorAll('.bonus-bulk-checkbox:checked')).map(cb => cb.value).filter(Boolean);
    if (ids.length === 0) {
        alert('Select at least one unpaid landlord bonus.');
        return false;
    }
    const form = document.getElementById('bonusBulkPaidForm');
    // Clear previous hidden inputs (except placeholder)
    Array.from(form.querySelectorAll('input[name="landlord_bonus_ids[]"]')).forEach(el => el.remove());
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'landlord_bonus_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    form.submit();
    return true;
}
</script>
@endpush