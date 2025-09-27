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
        'advertised_rent',
        'availability_date',
        'vacant_keys',
        'furnished',
        
        // Discovery & Compliance
        'works_pending',
        'compliance_epc',
        'compliance_eicr',
        'compliance_gas',
        'compliance_licence',
        'landlord_priority',
        'discovery_notes',
        
        // Offer Presentation
        'packages_discussed',
        'landlord_preference',
        
        // Objection Handling
        'objections',
        'objection_response',
        
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
        'compliance_epc' => 'boolean',
        'compliance_eicr' => 'boolean',
        'compliance_gas' => 'boolean',
        'compliance_licence' => 'boolean',
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
}
