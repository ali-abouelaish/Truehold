<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section',
        'can_access'
    ];

    protected $casts = [
        'can_access' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all available sections
     */
    public static function getAvailableSections()
    {
        return [
            'dashboard' => 'Dashboard',
            'properties' => 'Properties',
            'clients' => 'Clients',
            'rental_codes' => 'Rental Codes',
            'invoices' => 'Invoices',
            'group_viewings' => 'Group Viewings',
            'call_logs' => 'Call Logs',
            'users' => 'Users',
            'admin_permissions' => 'Admin Permissions'
        ];
    }
}