<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupViewingAttendee extends Model
{
    protected $fillable = [
        'group_viewing_id',
        'client_id',
        'status',
        'notes',
    ];

    public function groupViewing(): BelongsTo
    {
        return $this->belongsTo(GroupViewing::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}


