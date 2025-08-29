<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'user_id',
        'agent_code',
        'company_name',
        'license_number',
        'phone',
        'mobile',
        'website',
        'bio',
        'specialization',
        'experience_years',
        'languages',
        'office_address',
        'office_city',
        'office_postcode',
        'office_phone',
        'office_email',
        'profile_photo',
        'social_media',
        'certifications',
        'awards',
        'working_hours',
        'is_verified',
        'is_featured',
        'properties_count',
        'rating',
        'reviews_count',
        'last_active',
    ];

    protected $casts = [
        'social_media' => 'array',
        'certifications' => 'array',
        'awards' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'properties_count' => 'integer',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'last_active' => 'datetime',
    ];

    /**
     * Get the user that owns the agent profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the properties managed by this agent.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    /**
     * Get the clients managed by this agent.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the full office address.
     */
    public function getFullOfficeAddressAttribute(): string
    {
        $parts = array_filter([
            $this->office_address,
            $this->office_city,
            $this->office_postcode
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get the display name (company name or user name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: $this->user->name;
    }

    /**
     * Get the primary contact method.
     */
    public function getPrimaryContactAttribute(): string
    {
        return $this->mobile ?: $this->phone ?: $this->office_phone ?: 'N/A';
    }

    /**
     * Get the primary email.
     */
    public function getPrimaryEmailAttribute(): string
    {
        return $this->office_email ?: $this->user->email;
    }

    /**
     * Get formatted rating.
     */
    public function getFormattedRatingAttribute(): string
    {
        if (!$this->rating) {
            return 'No rating';
        }
        
        return number_format($this->rating, 1) . '/5.0';
    }

    /**
     * Get experience display text.
     */
    public function getExperienceDisplayAttribute(): string
    {
        if (!$this->experience_years) {
            return 'Experience not specified';
        }
        
        return $this->experience_years . ' years';
    }

    /**
     * Get languages as array.
     */
    public function getLanguagesArrayAttribute(): array
    {
        if (!$this->languages) {
            return [];
        }
        
        return explode(',', $this->languages);
    }

    /**
     * Get specialization as array.
     */
    public function getSpecializationArrayAttribute(): array
    {
        if (!$this->specialization) {
            return [];
        }
        
        return explode(',', $this->specialization);
    }

    /**
     * Check if agent is active (logged in within last 30 days).
     */
    public function getIsActiveAttribute(): bool
    {
        if (!$this->last_active) {
            return false;
        }
        
        return $this->last_active->diffInDays(now()) <= 30;
    }

    /**
     * Scope for verified agents.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for featured agents.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for active agents.
     */
    public function scopeActive($query)
    {
        return $query->where('last_active', '>=', now()->subDays(30));
    }

    /**
     * Scope for agents by specialization.
     */
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }

    /**
     * Scope for agents by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('office_city', 'like', "%{$location}%");
    }

    /**
     * Update last active timestamp.
     */
    public function updateLastActive(): void
    {
        $this->update(['last_active' => now()]);
    }

    /**
     * Increment properties count.
     */
    public function incrementPropertiesCount(): void
    {
        $this->increment('properties_count');
    }

    /**
     * Decrement properties count.
     */
    public function decrementPropertiesCount(): void
    {
        $this->decrement('properties_count');
    }

    /**
     * Update rating and reviews count.
     */
    public function updateRating(float $newRating, int $reviewsCount): void
    {
        $this->update([
            'rating' => $newRating,
            'reviews_count' => $reviewsCount
        ]);
    }
}
