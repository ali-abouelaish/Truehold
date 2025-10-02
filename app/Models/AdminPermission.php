<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete'
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all available admin sections
     */
    public static function getAvailableSections(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'call_logs' => 'Call Logs',
            'properties' => 'Properties',
            'invoices' => 'Invoices',
            'users' => 'User Management',
            'admin_permissions' => 'Admin Permissions'
        ];
    }
}
