<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonus_code',
        'date',
        'agent_id',
        'landlord',
        'property',
        'client',
        'commission',
        'bonus_split',
        'agent_commission',
        'agency_commission',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'commission' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'agency_commission' => 'decimal:2',
    ];

    /**
     * Get the agent that owns the bonus
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the user who created the bonus
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the agent's user information
     */
    public function agentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id', 'id')
            ->join('agents', 'users.id', '=', 'agents.user_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Generate a unique bonus code
     */
    public static function generateBonusCode()
    {
        $lastBonus = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastBonus ? (intval(substr($lastBonus->bonus_code, 2)) + 1) : 1;
        
        return 'LC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate commission splits based on bonus_split setting
     */
    public function calculateCommissionSplits()
    {
        if ($this->bonus_split === '100_0') {
            $this->agent_commission = $this->commission;
            $this->agency_commission = 0;
        } else {
            // Default 55/45 split
            $this->agent_commission = $this->commission * 0.55;
            $this->agency_commission = $this->commission * 0.45;
        }
    }

    /**
     * Get the agent's commission amount
     */
    public function getAgentCommissionAttribute()
    {
        if ($this->bonus_split === '100_0') {
            return $this->commission;
        }
        return $this->commission * 0.55;
    }

    /**
     * Get the agency's commission amount
     */
    public function getAgencyCommissionAttribute()
    {
        if ($this->bonus_split === '100_0') {
            return 0;
        }
        return $this->commission * 0.45;
    }
}
