<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Agent;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMonthlyPaymentSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:monthly-summary {--month=} {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly payment summary for all agents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ?: now()->subMonth()->month;
        $year = $this->option('year') ?: now()->subMonth()->year;
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $this->info("Generating monthly payment summary for {$startDate->format('F Y')}");
        
        // Get all agents with payments in the specified month
        $agents = Agent::whereHas('payments', function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })->with('user')->get();
        
        if ($agents->isEmpty()) {
            $this->warn('No agents found with payments for the specified month.');
            return;
        }
        
        $this->info("Found {$agents->count()} agents with payments for {$startDate->format('F Y')}");
        
        // Generate summary for each agent
        $summary = [];
        $totalOutstanding = 0;
        $totalPaid = 0;
        $totalRolled = 0;
        
        foreach ($agents as $agent) {
            $earnings = Payment::calculateAgentEarnings($agent->id, $startDate, $endDate);
            
            $summary[] = [
                'agent' => $agent,
                'total_owed' => $earnings['unpaid_earnings'] + $earnings['rolled_earnings'],
                'paid' => $earnings['paid_earnings'],
                'rolled' => $earnings['rolled_earnings'],
                'payment_count' => $earnings['payment_count'],
                'unpaid_count' => $earnings['unpaid_count'],
                'paid_count' => $earnings['paid_count'],
                'rolled_count' => $earnings['rolled_count'],
            ];
            
            $totalOutstanding += $earnings['unpaid_earnings'] + $earnings['rolled_earnings'];
            $totalPaid += $earnings['paid_earnings'];
            $totalRolled += $earnings['rolled_earnings'];
        }
        
        // Display summary table
        $headers = ['Agent', 'Total Owed', 'Paid', 'Rolled', 'Payments', 'Unpaid', 'Paid', 'Rolled'];
        $rows = [];
        
        foreach ($summary as $item) {
            $rows[] = [
                $item['agent']->display_name,
                '£' . number_format($item['total_owed'], 2),
                '£' . number_format($item['paid'], 2),
                '£' . number_format($item['rolled'], 2),
                $item['payment_count'],
                $item['unpaid_count'],
                $item['paid_count'],
                $item['rolled_count'],
            ];
        }
        
        $this->table($headers, $rows);
        
        // Display totals
        $this->info("\n=== MONTHLY SUMMARY TOTALS ===");
        $this->info("Total Outstanding: £" . number_format($totalOutstanding, 2));
        $this->info("Total Paid: £" . number_format($totalPaid, 2));
        $this->info("Total Rolled: £" . number_format($totalRolled, 2));
        $this->info("Total Agents: " . count($summary));
        
        // Save summary to file (optional)
        $filename = storage_path("app/monthly-payment-summary-{$year}-{$month}.json");
        file_put_contents($filename, json_encode([
            'month' => $month,
            'year' => $year,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'summary' => $summary,
            'totals' => [
                'total_outstanding' => $totalOutstanding,
                'total_paid' => $totalPaid,
                'total_rolled' => $totalRolled,
                'agent_count' => count($summary),
            ],
            'generated_at' => now()->toISOString(),
        ], JSON_PRETTY_PRINT));
        
        $this->info("\nSummary saved to: {$filename}");
        
        return Command::SUCCESS;
    }
}
