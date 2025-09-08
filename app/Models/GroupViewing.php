<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupViewing extends Model
{
    protected $fillable = [
        'property_id',
        'scheduled_at',
        'location',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(GroupViewingAttendee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}


