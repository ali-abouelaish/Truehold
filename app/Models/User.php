<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'roles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }

    /**
     * Get the agent profile associated with the user.
     */
    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class);
    }

    /**
     * Check if the user is an agent.
     */
    public function isAgent(): bool
    {
        return $this->agent()->exists();
    }

    /**
     * Check if the user is a verified agent.
     */
    public function isVerifiedAgent(): bool
    {
        return $this->agent && $this->agent->is_verified;
    }

    /**
     * Check if the user is a featured agent.
     */
    public function isFeaturedAgent(): bool
    {
        return $this->agent && $this->agent->is_featured;
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        // Check both old role system and new roles array
        return $this->role === $role || in_array($role, $this->roles ?? []);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an agent (by role or agent profile).
     */
    public function isAgentByRole(): bool
    {
        return $this->hasRole('agent');
    }

    /**
     * Check if the user is a marketing agent.
     */
    public function isMarketingAgent(): bool
    {
        return $this->hasRole('marketing_agent');
    }

    /**
     * Add a role to the user.
     */
    public function addRole(string $role): void
    {
        $roles = $this->roles ?? [];
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $this->roles = $roles;
            $this->save();
        }
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string $role): void
    {
        $roles = $this->roles ?? [];
        $roles = array_filter($roles, fn($r) => $r !== $role);
        $this->roles = array_values($roles);
        $this->save();
    }

    /**
     * Get all roles for the user.
     */
    public function getAllRoles(): array
    {
        $roles = $this->roles ?? [];
        // Include the legacy role if it exists and is not already in roles
        if ($this->role && !in_array($this->role, $roles)) {
            $roles[] = $this->role;
        }
        return array_unique($roles);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the call logs for this agent.
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'agent_id');
    }

    /**
     * Get the admin permissions for this user.
     */
    public function adminPermissions(): HasMany
    {
        return $this->hasMany(AdminPermission::class);
    }

    /**
     * Get the user permissions for this user.
     */
    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Check if user has permission for a specific section and action.
     */
    public function hasAdminPermission(string $section, string $action = 'view'): bool
    {
        // Super admin has all permissions
        if ($this->email === 'admin@letconnect.com') {
            return true;
        }
        
        // Check new simplified permission system first
        $userPermission = $this->userPermissions()
            ->where('section', $section)
            ->where('can_access', true)
            ->first();
            
        if ($userPermission) {
            return true; // If they have access to the section, they can do everything
        }
        
        // Fallback to old complex system for backward compatibility
        $permission = $this->adminPermissions()
            ->where('section', $section)
            ->first();

        if (!$permission) {
            return false;
        }

        return match($action) {
            'view' => $permission->can_view,
            'create' => $permission->can_create,
            'edit' => $permission->can_edit,
            'delete' => $permission->can_delete,
            default => false
        };
    }

    /**
     * Check if the user can edit a rental code
     */
    public function canEditRentalCode($rentalCode): bool
    {
        // Admin can edit any rental code
        if ($this->isAdmin()) {
            return true;
        }
        
        // User can edit rental codes they created (rent_by_agent_id matches their agent user_id)
        if ($this->agent && $rentalCode->rental_agent_id == $this->agent->user_id) {
            return true;
        }
        
        return false;
    }
}
