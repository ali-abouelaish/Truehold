<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Payment extends Model
{
    protected $fillable = [
        'date',
        'agent_id',
        'landlord',
        'property',
        'client',
        'full_commission',
        'agent_commission',
        'type',
        'invoice_sent_to_management',
        'payment_status',
        'payment_method',
        'paid_date',
        'notes',
        'admin_notes',
        'is_readonly'
    ];

    protected $casts = [
        'date' => 'date',
        'full_commission' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'invoice_sent_to_management' => 'boolean',
        'is_readonly' => 'boolean',
        'paid_date' => 'date',
    ];

    /**
     * Get the agent that owns the payment.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Scope for unpaid payments
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope for paid payments
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for rolled payments
     */
    public function scopeRolled(Builder $query): Builder
    {
        return $query->where('payment_status', 'rolled');
    }

    /**
     * Scope for payments by type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for payments by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for current month payments
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    /**
     * Scope for previous month payments
     */
    public function scopePreviousMonth(Builder $query): Builder
    {
        $previousMonth = now()->subMonth();
        return $query->whereMonth('date', $previousMonth->month)
                    ->whereYear('date', $previousMonth->year);
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(string $method = 'transfer', string $adminNotes = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'paid_date' => now(),
            'admin_notes' => $adminNotes,
            'is_readonly' => true
        ]);
    }

    /**
     * Mark payment as rolled over
     */
    public function markAsRolled(string $adminNotes = null): void
    {
        $this->update([
            'payment_status' => 'rolled',
            'payment_method' => 'roll_to_next_month',
            'admin_notes' => $adminNotes
        ]);
    }

    /**
     * Check if payment can be edited
     */
    public function canBeEdited(): bool
    {
        return !$this->is_readonly;
    }

    /**
     * Get payment type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'bonus' => 'Bonus',
            'letting_deal' => 'Letting Deal',
            'renewal' => 'Renewal',
            'marketing' => 'Marketing',
            'referral' => 'Referral',
            'other' => 'Other',
            default => ucfirst($this->type)
        };
    }

    /**
     * Get payment status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
            'rolled' => 'Rolled Over',
            default => ucfirst($this->payment_status)
        };
    }

    /**
     * Get payment method display name
     */
    public function getMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'transfer' => 'Transfer',
            'cash' => 'Cash',
            'roll_to_next_month' => 'Roll to Next Month',
            default => $this->payment_method ? ucfirst($this->payment_method) : 'N/A'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'badge-warning',
            'paid' => 'badge-success',
            'rolled' => 'badge-info',
            default => 'badge-secondary'
        };
    }

    /**
     * Calculate total earnings for an agent in a date range
     */
    public static function calculateAgentEarnings(int $agentId, $startDate, $endDate): array
    {
        $payments = self::where('agent_id', $agentId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalEarnings = $payments->sum('agent_commission');
        $paidEarnings = $payments->where('payment_status', 'paid')->sum('agent_commission');
        $unpaidEarnings = $payments->where('payment_status', 'unpaid')->sum('agent_commission');
        $rolledEarnings = $payments->where('payment_status', 'rolled')->sum('agent_commission');

        return [
            'total_earnings' => $totalEarnings,
            'paid_earnings' => $paidEarnings,
            'unpaid_earnings' => $unpaidEarnings,
            'rolled_earnings' => $rolledEarnings,
            'payment_count' => $payments->count(),
            'paid_count' => $payments->where('payment_status', 'paid')->count(),
            'unpaid_count' => $payments->where('payment_status', 'unpaid')->count(),
            'rolled_count' => $payments->where('payment_status', 'rolled')->count(),
        ];
    }

    /**
     * Get monthly summary for all agents
     */
    public static function getMonthlySummary($startDate, $endDate): array
    {
        $payments = self::with('agent')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('agent_id');

        $summary = [];
        foreach ($payments as $agentId => $agentPayments) {
            $agent = $agentPayments->first()->agent;
            $earnings = self::calculateAgentEarnings($agentId, $startDate, $endDate);
            
            $summary[] = [
                'agent' => $agent,
                'agent_id' => $agentId,
                'total_owed' => $earnings['unpaid_earnings'] + $earnings['rolled_earnings'],
                'paid' => $earnings['paid_earnings'],
                'rolled' => $earnings['rolled_earnings'],
                'payment_count' => $earnings['payment_count'],
                'unpaid_count' => $earnings['unpaid_count'],
                'paid_count' => $earnings['paid_count'],
                'rolled_count' => $earnings['rolled_count'],
            ];
        }

        return $summary;
    }
}
