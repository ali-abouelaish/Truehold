<?php

namespace App\Http\Controllers;

use App\Models\RentalCode;
use App\Models\Client;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentProfileController extends Controller
{
    /**
     * Display the agent's profile dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found.');
        }

        // Get agent's rental codes - only where they were rental agent or marketing agent
        $rentalCodes = RentalCode::with('client')
            ->where(function($query) use ($agent, $user) {
                $query->where('rent_by_agent', $agent->company_name ?? $user->name)
                      ->orWhere('marketing_agent', $user->name);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate earnings and statistics
        $earningsData = $this->calculateAgentEarnings($user, $agent);
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($user, $agent);
        
        // Get monthly earnings for chart
        $monthlyEarnings = $this->getMonthlyEarnings($user, $agent);

        return view('agent.profile.dashboard', compact(
            'user', 
            'agent', 
            'rentalCodes', 
            'earningsData', 
            'recentActivity',
            'monthlyEarnings'
        ));
    }

    /**
     * Display agent's rental codes
     */
    public function rentalCodes(Request $request)
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found.');
        }

        $query = RentalCode::with('client')
            ->where(function($q) use ($agent, $user) {
                $q->where('rent_by_agent', $agent->company_name ?? $user->name)
                  ->orWhere('marketing_agent', $user->name);
            });

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('date_from')) {
            $query->where('rental_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('rental_date', '<=', $request->date_to);
        }

        $rentalCodes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('agent.profile.rental-codes', compact('rentalCodes', 'user', 'agent'));
    }

    /**
     * Display agent's earnings
     */
    public function earnings(Request $request)
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found.');
        }

        // Get date range
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Calculate detailed earnings
        $earningsData = $this->calculateDetailedEarnings($user, $agent, $startDate, $endDate);
        
        // Get monthly breakdown
        $monthlyBreakdown = $this->getMonthlyBreakdown($user, $agent);
        
        // Get payment history
        $paymentHistory = $this->getPaymentHistory($user, $agent, $startDate, $endDate);

        return view('agent.profile.earnings', compact(
            'user', 
            'agent', 
            'earningsData', 
            'monthlyBreakdown',
            'paymentHistory',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display agent's deductions
     */
    public function deductions()
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found.');
        }

        // Calculate deductions data
        $deductionsData = $this->calculateDeductions($user, $agent);
        
        // Get detailed deductions history
        $deductionsHistory = $this->getDeductionsHistory($user, $agent);

        return view('agent.profile.deductions', compact(
            'user', 
            'agent', 
            'deductionsData',
            'deductionsHistory'
        ));
    }

    /**
     * Display agent's clients
     */
    public function clients()
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found.');
        }

        $clients = Client::where('agent_id', $agent->id)
            ->with(['rentalCodes' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('agent.profile.clients', compact('clients', 'user', 'agent'));
    }

    /**
     * Calculate agent earnings
     */
    private function calculateAgentEarnings($user, $agent)
    {
        $rentalCodes = RentalCode::where(function($q) use ($agent, $user) {
            $q->where('rent_by_agent', $agent->company_name ?? $user->name)
              ->orWhere('marketing_agent', $user->name);
        })->get();

        $totalEarnings = 0;
        $paidAmount = 0;
        $outstandingAmount = 0;
        $totalTransactions = $rentalCodes->count();
        $paidTransactions = $rentalCodes->where('paid', true)->count();

        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;
            
            // Calculate base commission after VAT
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                $baseCommission = $totalFee * 0.8;
            }

            $agentEarnings = 0;
            $marketingEarnings = 0;
            
            // Check if agent is the rental agent
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            if ($rentAgentName === ($agent->company_name ?? $user->name)) {
                // Agent is the rental agent - calculate rental earnings
                $agentEarnings = $baseCommission * 0.55;
                
                // Check for marketing deduction if there's a different marketing agent
                if (!empty($marketingAgentName) && $marketingAgentName !== ($agent->company_name ?? $user->name)) {
                    $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                    $agentEarnings -= $marketingDeduction;
                }
            }
            
            if ($marketingAgentName === $user->name && $marketingAgentName !== $rentAgentName) {
                // Agent is the marketing agent (and not the rental agent) - calculate marketing earnings
                $marketingEarnings = $clientCount > 1 ? 40.0 : 30.0;
            }
            
            $totalEarningsForThisCode = $agentEarnings + $marketingEarnings;
            $totalEarnings += $totalEarningsForThisCode;
            
            if ($code->paid) {
                $paidAmount += $totalEarningsForThisCode;
            } else {
                $outstandingAmount += $totalEarningsForThisCode;
            }
        }

        return [
            'total_earnings' => $totalEarnings,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => max(0, $outstandingAmount),
            'total_transactions' => $totalTransactions,
            'paid_transactions' => $paidTransactions,
            'unpaid_transactions' => $totalTransactions - $paidTransactions,
            'payment_rate' => $totalTransactions > 0 ? ($paidTransactions / $totalTransactions) * 100 : 0,
            'avg_earnings_per_transaction' => $totalTransactions > 0 ? $totalEarnings / $totalTransactions : 0,
        ];
    }

    /**
     * Calculate detailed earnings with breakdown
     */
    private function calculateDetailedEarnings($user, $agent, $startDate, $endDate)
    {
        $rentalCodes = RentalCode::where(function($q) use ($agent, $user) {
            $q->where('rent_by_agent', $agent->company_name ?? $user->name)
              ->orWhere('marketing_agent', $user->name);
        })
        ->whereBetween('rental_date', [$startDate, $endDate])
        ->get();

        $earnings = [
            'rental_earnings' => 0,
            'marketing_earnings' => 0,
            'total_earnings' => 0,
            'vat_deductions' => 0,
            'marketing_deductions' => 0,
            'net_earnings' => 0,
            'paid_amount' => 0,
            'outstanding_amount' => 0,
            'transaction_count' => 0,
        ];

        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;
            
            // Calculate base commission after VAT
            $baseCommission = $totalFee;
            if ($paymentMethod === 'Transfer') {
                $baseCommission = $totalFee * 0.8;
                $earnings['vat_deductions'] += $totalFee - $baseCommission;
            }

            $agentEarnings = 0;
            $marketingEarnings = 0;
            
            // Check if agent is the rental agent
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            if ($rentAgentName === ($agent->company_name ?? $user->name)) {
                // Agent is the rental agent
                $agentEarnings = $baseCommission * 0.55;
                
                // Check for marketing deduction
                if (!empty($marketingAgentName) && $marketingAgentName !== ($agent->company_name ?? $user->name)) {
                    $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                    $agentEarnings -= $marketingDeduction;
                    $earnings['marketing_deductions'] += $marketingDeduction;
                }
            }
            
            if ($marketingAgentName === $user->name && $marketingAgentName !== $rentAgentName) {
                // Agent is the marketing agent
                $marketingEarnings = $clientCount > 1 ? 40.0 : 30.0;
            }
            
            $totalEarningsForThisCode = $agentEarnings + $marketingEarnings;
            $earnings['rental_earnings'] += $agentEarnings;
            $earnings['marketing_earnings'] += $marketingEarnings;
            $earnings['total_earnings'] += $totalEarningsForThisCode;
            $earnings['transaction_count'] += 1;
            
            if ($code->paid) {
                $earnings['paid_amount'] += $totalEarningsForThisCode;
            } else {
                $earnings['outstanding_amount'] += $totalEarningsForThisCode;
            }
        }

        $earnings['net_earnings'] = $earnings['total_earnings'] - $earnings['vat_deductions'] - $earnings['marketing_deductions'];

        return $earnings;
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity($user, $agent)
    {
        return RentalCode::with('client')
            ->where(function($q) use ($agent, $user) {
                $q->where('rent_by_agent', $agent->company_name ?? $user->name)
                  ->orWhere('marketing_agent', $user->name);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get monthly earnings for chart
     */
    private function getMonthlyEarnings($user, $agent)
    {
        $monthlyData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $monthlyCodes = RentalCode::where(function($q) use ($agent, $user) {
                $q->where('rent_by_agent', $agent->company_name ?? $user->name)
                  ->orWhere('marketing_agent', $user->name);
            })
            ->whereBetween('rental_date', [$startOfMonth, $endOfMonth])
            ->get();

            $monthlyEarnings = 0;
            foreach ($monthlyCodes as $code) {
                $totalFee = (float) ($code->consultation_fee ?? 0);
                $paymentMethod = $code->payment_method ?? 'Cash';
                $clientCount = $code->client_count ?? 1;
                
                $baseCommission = $totalFee;
                if ($paymentMethod === 'Transfer') {
                    $baseCommission = $totalFee * 0.8;
                }

                $agentEarnings = 0;
                $marketingEarnings = 0;
                
                $rentAgentName = $code->rent_by_agent_name;
                $marketingAgentName = $code->marketing_agent_name;
                
                if ($rentAgentName === ($agent->company_name ?? $user->name)) {
                    $agentEarnings = $baseCommission * 0.55;
                    
                    if (!empty($marketingAgentName) && $marketingAgentName !== ($agent->company_name ?? $user->name)) {
                        $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                        $agentEarnings -= $marketingDeduction;
                    }
                }
                
                if ($marketingAgentName === $user->name && $marketingAgentName !== $rentAgentName) {
                    $marketingEarnings = $clientCount > 1 ? 40.0 : 30.0;
                }
                
                $monthlyEarnings += $agentEarnings + $marketingEarnings;
            }
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'earnings' => $monthlyEarnings,
                'transactions' => $monthlyCodes->count()
            ];
        }
        
        return $monthlyData;
    }

    /**
     * Get monthly breakdown
     */
    private function getMonthlyBreakdown($user, $agent)
    {
        $breakdown = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $monthlyCodes = RentalCode::where(function($q) use ($agent, $user) {
                $q->where('rent_by_agent', $agent->company_name ?? $user->name)
                  ->orWhere('marketing_agent', $user->name);
            })
            ->whereBetween('rental_date', [$startOfMonth, $endOfMonth])
            ->get();

            $monthlyEarnings = $this->calculateDetailedEarnings($user, $agent, $startOfMonth, $endOfMonth);
            
            $breakdown[] = [
                'month' => $date->format('M Y'),
                'month_key' => $date->format('Y-m'),
                'earnings' => $monthlyEarnings,
                'transaction_count' => $monthlyCodes->count()
            ];
        }
        
        return $breakdown;
    }

    /**
     * Get payment history
     */
    private function getPaymentHistory($user, $agent, $startDate, $endDate)
    {
        return RentalCode::with('client')
            ->where(function($q) use ($agent, $user) {
                $q->where('rent_by_agent', $agent->company_name ?? $user->name)
                  ->orWhere('marketing_agent', $user->name);
            })
            ->whereBetween('rental_date', [$startDate, $endDate])
            ->where('paid', true)
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    /**
     * Calculate deductions data
     */
    private function calculateDeductions($user, $agent)
    {
        $rentalCodes = RentalCode::where(function($q) use ($agent, $user) {
            $q->where('rent_by_agent', $agent->company_name ?? $user->name)
              ->orWhere('marketing_agent', $user->name);
        })->get();

        $deductions = [
            'total_vat_deductions' => 0,
            'total_marketing_deductions' => 0,
            'total_agency_cut' => 0,
            'total_deductions' => 0,
        ];

        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;
            
            // Calculate VAT deductions (20% on transfers)
            if ($paymentMethod === 'Transfer') {
                $vatDeduction = $totalFee * 0.2;
                $deductions['total_vat_deductions'] += $vatDeduction;
            }

            // Calculate agency cut (45% of base commission)
            $baseCommission = $paymentMethod === 'Transfer' ? $totalFee * 0.8 : $totalFee;
            $agencyCut = $baseCommission * 0.45;
            $deductions['total_agency_cut'] += $agencyCut;

            // Calculate marketing deductions
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            if ($rentAgentName === ($agent->company_name ?? $user->name) && 
                !empty($marketingAgentName) && 
                $marketingAgentName !== ($agent->company_name ?? $user->name)) {
                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                $deductions['total_marketing_deductions'] += $marketingDeduction;
            }
        }

        $deductions['total_deductions'] = $deductions['total_vat_deductions'] + 
                                        $deductions['total_marketing_deductions'] + 
                                        $deductions['total_agency_cut'];

        return $deductions;
    }

    /**
     * Get detailed deductions history
     */
    private function getDeductionsHistory($user, $agent)
    {
        $rentalCodes = RentalCode::with('client')
            ->where(function($q) use ($agent, $user) {
                $q->where('rent_by_agent', $agent->company_name ?? $user->name)
                  ->orWhere('marketing_agent', $user->name);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $deductionsHistory = collect();

        foreach ($rentalCodes as $code) {
            $totalFee = (float) ($code->consultation_fee ?? 0);
            $paymentMethod = $code->payment_method ?? 'Cash';
            $clientCount = $code->client_count ?? 1;
            $rentAgentName = $code->rent_by_agent_name;
            $marketingAgentName = $code->marketing_agent_name;
            
            // VAT deduction
            if ($paymentMethod === 'Transfer') {
                $vatDeduction = $totalFee * 0.2;
                $deductionsHistory->push([
                    'date' => $code->created_at,
                    'rental_code' => $code->rental_code,
                    'rental_code_id' => $code->id,
                    'client_name' => $code->client->full_name ?? 'Unknown',
                    'type' => 'vat',
                    'amount' => $vatDeduction,
                    'reason' => '20% VAT on bank transfer payment'
                ]);
            }

            // Agency cut
            $baseCommission = $paymentMethod === 'Transfer' ? $totalFee * 0.8 : $totalFee;
            $agencyCut = $baseCommission * 0.45;
            $deductionsHistory->push([
                'date' => $code->created_at,
                'rental_code' => $code->rental_code,
                'rental_code_id' => $code->id,
                'client_name' => $code->client->full_name ?? 'Unknown',
                'type' => 'agency',
                'amount' => $agencyCut,
                'reason' => '45% agency commission'
            ]);

            // Marketing deduction
            if ($rentAgentName === ($agent->company_name ?? $user->name) && 
                !empty($marketingAgentName) && 
                $marketingAgentName !== ($agent->company_name ?? $user->name)) {
                $marketingDeduction = $clientCount > 1 ? 40.0 : 30.0;
                $deductionsHistory->push([
                    'date' => $code->created_at,
                    'rental_code' => $code->rental_code,
                    'rental_code_id' => $code->id,
                    'client_name' => $code->client->full_name ?? 'Unknown',
                    'type' => 'marketing',
                    'amount' => $marketingDeduction,
                    'reason' => 'Marketing agent fee (' . ($clientCount > 1 ? '£40' : '£30') . ')'
                ]);
            }
        }

        return $deductionsHistory->sortByDesc('date');
    }
}
