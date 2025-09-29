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
        return $this->role === $role;
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
        return $this->role === 'agent';
    }

    /**
     * Check if the user is a marketing agent.
     */
    public function isMarketingAgent(): bool
    {
        return $this->role === 'marketing_agent';
    }

    /**
     * Get the call logs for this agent.
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'agent_id');
    }
}
