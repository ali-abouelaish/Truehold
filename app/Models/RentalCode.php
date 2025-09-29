<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_code',
        'rental_date',
        'consultation_fee',
        'payment_method',
        'property',
        'licensor',
        'client_id',
        'client_count',
        'rent_by_agent',
        'client_by_agent',
        'marketing_agent',
        'notes',
        'status',
        'paid',
        'paid_at',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'consultation_fee' => 'decimal:2',
        'paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    /**
     * Get display name for rent_by_agent (handles ID or name string)
     */
    public function getRentByAgentNameAttribute(): string
    {
        try {
            $value = $this->rent_by_agent;
            if (empty($value)) {
                return 'N/A';
            }
            // If numeric, treat as user_id and resolve to user's name
            if (is_numeric($value)) {
                $user = \App\Models\User::find((int) $value);
                return $user?->name ?? (string) $value;
            }
            // Otherwise already a name
            return (string) $value;
        } catch (\Throwable $e) {
            return (string) ($this->rent_by_agent ?? 'N/A');
        }
    }

    /**
     * Get display name for client_by_agent (handles ID or name string)
     */
    public function getClientByAgentNameAttribute(): string
    {
        try {
            $value = $this->client_by_agent;
            if (empty($value)) {
                return 'N/A';
            }
            if (is_numeric($value)) {
                $user = \App\Models\User::find((int) $value);
                return $user?->name ?? (string) $value;
            }
            return (string) $value;
        } catch (\Throwable $e) {
            return (string) ($this->client_by_agent ?? 'N/A');
        }
    }

    /**
     * Get display name for marketing_agent (handles ID or name string)
     */
    public function getMarketingAgentNameAttribute(): string
    {
        try {
            $value = $this->marketing_agent;
            if (empty($value)) {
                return 'N/A';
            }
            if (is_numeric($value)) {
                $user = \App\Models\User::find((int) $value);
                return $user?->name ?? (string) $value;
            }
            return (string) $value;
        } catch (\Throwable $e) {
            return (string) ($this->marketing_agent ?? 'N/A');
        }
    }

    /**
     * Generate a unique rental code
     */
    public static function generateRentalCode(): string
    {
        do {
            $code = 'CC' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('rental_code', $code)->exists());

        return $code;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('rental_date', [$startDate, $endDate]);
    }

    /**
     * Get the client that owns the rental code
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get formatted rental date
     */
    public function getFormattedRentalDateAttribute(): string
    {
        return $this->rental_date->format('d/m/Y');
    }

}
