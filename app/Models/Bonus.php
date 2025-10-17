<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bonus extends Model
{
    protected $fillable = [
        'date',
        'agent_id',
        'landlord',
        'property',
        'client',
        'full_commission',
        'agent_commission',
        'invoice_sent_to_management',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'full_commission' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'invoice_sent_to_management' => 'boolean',
    ];

    /**
     * Get the agent that owns the bonus.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
