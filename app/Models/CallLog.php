<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Call Metadata
        'agent_id',
        'call_type',
        'call_status',
        'call_datetime',
        
        // Landlord Details
        'landlord_name',
        'landlord_phone',
        'landlord_email',
        'landlord_company',
        'contact_source',
        
        // Property Details
        'property_address',
        'property_type',
        'number_of_beds',
        'number_of_bathrooms',
        'advertised_rent',
        'availability_date',
        'vacant_keys',
        'furnished',
        
        // Discovery & Compliance
        'room_link',
        'landlord_priority',
        'discovery_notes',
        
        // Offer Presentation
        'packages_discussed',
        'landlord_preference',
        
        // Objection Handling
        'objections',
        
        // Outcome & Next Steps
        'viewing_booked',
        'viewing_datetime',
        'follow_up_needed',
        'follow_up_datetime',
        'next_step_status',
        'call_outcome',
        'agent_notes',
        
        // Automation Hooks
        'send_sms',
        'send_email',
        'send_whatsapp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'call_datetime' => 'datetime',
        'availability_date' => 'date',
        'viewing_datetime' => 'datetime',
        'follow_up_datetime' => 'datetime',
        'vacant_keys' => 'boolean',
        'viewing_booked' => 'boolean',
        'follow_up_needed' => 'boolean',
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
        'send_whatsapp' => 'boolean',
        'advertised_rent' => 'decimal:2',
        'packages_discussed' => 'array',
        'objections' => 'array',
    ];

    /**
     * Get the agent that owns the call log.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Scope a query to only include call logs for a specific agent.
     */
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope a query to filter by call status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('call_status', $status);
    }

    /**
     * Scope a query to filter by call type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('call_type', $type);
    }

    /**
     * Scope a query to filter by landlord name.
     */
    public function scopeByLandlord($query, $landlordName)
    {
        return $query->where('landlord_name', 'like', "%{$landlordName}%");
    }

    /**
     * Scope a query to filter by property address.
     */
    public function scopeByProperty($query, $address)
    {
        return $query->where('property_address', 'like', "%{$address}%");
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('call_datetime', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by phone number.
     */
    public function scopeByPhone($query, $phone)
    {
        // Extract just the digits from the input phone number
        $inputDigits = preg_replace('/[^\d]/', '', $phone);
        
        return $query->where(function($q) use ($phone, $inputDigits) {
            // Exact match
            $q->where('landlord_phone', $phone)
              // Match by digits only (ignoring spaces, dashes, etc.)
              ->orWhereRaw("REGEXP_REPLACE(landlord_phone, '[^0-9]', '') = ?", [$inputDigits]);
        });
    }

    /**
     * Check if a phone number has been called before.
     */
    public static function hasBeenCalledBefore($phone, $excludeId = null)
    {
        if (empty($phone)) {
            return false;
        }

        $query = static::byPhone($phone);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get previous call logs for a phone number.
     */
    public static function getPreviousCalls($phone, $excludeId = null)
    {
        if (empty($phone)) {
            return collect();
        }

        $query = static::byPhone($phone)
            ->with('agent')
            ->orderBy('call_datetime', 'desc');
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    /**
     * Get call history summary for a phone number.
     */
    public static function getCallHistorySummary($phone, $excludeId = null)
    {
        if (empty($phone)) {
            return null;
        }

        $query = static::byPhone($phone);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $calls = $query->get();
        
        if ($calls->isEmpty()) {
            return null;
        }

        return [
            'total_calls' => $calls->count(),
            'last_call_date' => $calls->first()->call_datetime,
            'call_outcomes' => $calls->pluck('call_outcome')->unique()->values(),
            'landlord_names' => $calls->pluck('landlord_name')->unique()->values(),
            'property_addresses' => $calls->pluck('property_address')->unique()->values(),
            'recent_notes' => $calls->take(3)->pluck('agent_notes')->filter()->values(),
        ];
    }
}
