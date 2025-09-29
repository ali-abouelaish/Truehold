<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Client extends Model
{
    protected $fillable = [
        'full_name',
        'date_of_birth',
        'phone_number',
        'email',
        'nationality',
        'current_address',
        'company_university_name',
        'company_university_address',
        'position_role',
        'budget',
        'area_of_interest',
        'moving_date',
        'notes',
        'registration_status',
        'agent_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'moving_date' => 'date',
        'budget' => 'decimal:2',
    ];

    /**
     * Get the agent that manages this client.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the properties this client is interested in or has viewed.
     */
    public function propertyInterests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    /**
     * Get properties this client is interested in.
     */
    public function interestedProperties(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    /**
     * Get the client's age.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        return $this->date_of_birth->diffInYears(now());
    }

    /**
     * Get the client's age group.
     */
    public function getAgeGroupAttribute(): string
    {
        if (!$this->age) {
            return 'Unknown';
        }
        
        if ($this->age < 18) {
            return 'Under 18';
        } elseif ($this->age < 25) {
            return '18-24';
        } elseif ($this->age < 35) {
            return '25-34';
        } elseif ($this->age < 45) {
            return '35-44';
        } elseif ($this->age < 55) {
            return '45-54';
        } elseif ($this->age < 65) {
            return '55-64';
        } else {
            return '65+';
        }
    }

    /**
     * Get the client's display name (first name only).
     */
    public function getFirstNameAttribute(): string
    {
        $names = explode(' ', $this->full_name);
        return $names[0] ?? $this->full_name;
    }

    /**
     * Get the client's last name.
     */
    public function getLastNameAttribute(): string
    {
        $names = explode(' ', $this->full_name);
        if (count($names) > 1) {
            return end($names);
        }
        return '';
    }

    /**
     * Get formatted phone number.
     */
    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->phone_number) {
            return 'N/A';
        }
        
        // Basic UK phone number formatting
        $phone = preg_replace('/[^0-9]/', '', $this->phone_number);
        
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
        }
        
        return $this->phone_number;
    }

    /**
     * Get formatted date of birth.
     */
    public function getFormattedDateOfBirthAttribute(): string
    {
        if (!$this->date_of_birth) {
            return 'Not provided';
        }
        
        return $this->date_of_birth->format('jS F Y');
    }

    /**
     * Get formatted budget.
     */
    public function getFormattedBudgetAttribute(): string
    {
        if (!$this->budget) {
            return 'Not specified';
        }
        
        return '£' . number_format($this->budget, 2);
    }

    /**
     * Get formatted moving date.
     */
    public function getFormattedMovingDateAttribute(): string
    {
        if (!$this->moving_date) {
            return 'Not specified';
        }
        
        return $this->moving_date->format('jS F Y');
    }

    /**
     * Get urgency level based on moving date.
     */
    public function getUrgencyLevelAttribute(): string
    {
        if (!$this->moving_date) {
            return 'Unknown';
        }
        
        $daysUntilMoving = now()->diffInDays($this->moving_date, false);
        
        if ($daysUntilMoving < 0) {
            return 'Overdue';
        } elseif ($daysUntilMoving <= 7) {
            return 'Urgent';
        } elseif ($daysUntilMoving <= 30) {
            return 'High';
        } elseif ($daysUntilMoving <= 90) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    /**
     * Get urgency color for display.
     */
    public function getUrgencyColorAttribute(): string
    {
        $urgency = $this->urgency_level;
        
        switch ($urgency) {
            case 'Overdue':
                return 'text-red-600 bg-red-100';
            case 'Urgent':
                return 'text-red-600 bg-red-50';
            case 'High':
                return 'text-orange-600 bg-orange-50';
            case 'Medium':
                return 'text-yellow-600 bg-yellow-50';
            case 'Low':
                return 'text-green-600 bg-green-50';
            default:
                return 'text-gray-600 bg-gray-50';
        }
    }

    /**
     * Check if client is registered.
     */
    public function isRegistered(): bool
    {
        return $this->registration_status === 'registered';
    }

    /**
     * Check if client is unregistered.
     */
    public function isUnregistered(): bool
    {
        return $this->registration_status === 'unregistered';
    }

    /**
     * Get registration status badge class.
     */
    public function getRegistrationBadgeClassAttribute(): string
    {
        return $this->isRegistered() 
            ? 'bg-green-100 text-green-800' 
            : 'bg-gray-100 text-gray-800';
    }

    /**
     * Get registration status icon.
     */
    public function getRegistrationIconAttribute(): string
    {
        return $this->isRegistered() 
            ? 'fas fa-check-circle' 
            : 'fas fa-times-circle';
    }

    /**
     * Check if client is a student.
     */
    public function getIsStudentAttribute(): bool
    {
        return !empty($this->company_university_name) && 
               (stripos($this->company_university_name, 'university') !== false ||
                stripos($this->company_university_name, 'college') !== false ||
                stripos($this->company_university_name, 'school') !== false);
    }

    /**
     * Check if client is employed.
     */
    public function getIsEmployedAttribute(): bool
    {
        return !empty($this->company_university_name) && !$this->is_student;
    }

    /**
     * Get client type (Student, Professional, Other).
     */
    public function getClientTypeAttribute(): string
    {
        if ($this->is_student) {
            return 'Student';
        } elseif ($this->is_employed) {
            return 'Professional';
        } else {
            return 'Other';
        }
    }

    /**
     * Scope for clients by age group.
     */
    public function scopeByAgeGroup($query, $ageGroup)
    {
        switch ($ageGroup) {
            case '18-24':
                return $query->where('date_of_birth', '<=', now()->subYears(18))
                            ->where('date_of_birth', '>', now()->subYears(25));
            case '25-34':
                return $query->where('date_of_birth', '<=', now()->subYears(25))
                            ->where('date_of_birth', '>', now()->subYears(35));
            case '35-44':
                return $query->where('date_of_birth', '<=', now()->subYears(35))
                            ->where('date_of_birth', '>', now()->subYears(45));
            case 'student':
                return $query->whereNotNull('company_university_name')
                            ->where(function($q) {
                                $q->where('company_university_name', 'like', '%university%')
                                  ->orWhere('company_university_name', 'like', '%college%')
                                  ->orWhere('company_university_name', 'like', '%school%');
                            });
            case 'professional':
                return $query->whereNotNull('company_university_name')
                            ->where('company_university_name', 'not like', '%university%')
                            ->where('company_university_name', 'not like', '%college%')
                            ->where('company_university_name', 'not like', '%school%');
            default:
                return $query;
        }
    }

    /**
     * Scope for clients by nationality.
     */
    public function scopeByNationality($query, $nationality)
    {
        return $query->where('nationality', 'like', "%{$nationality}%");
    }

    /**
     * Scope for clients by agent.
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for clients with email.
     */
    public function scopeWithEmail($query)
    {
        return $query->whereNotNull('email')->where('email', '!=', '');
    }

    /**
     * Scope for clients with phone.
     */
    public function scopeWithPhone($query)
    {
        return $query->whereNotNull('phone_number')->where('phone_number', '!=', '');
    }

    /**
     * Get clients count by nationality.
     */
    public static function getNationalityStats()
    {
        return static::selectRaw('nationality, COUNT(*) as count')
                    ->whereNotNull('nationality')
                    ->groupBy('nationality')
                    ->orderBy('count', 'desc')
                    ->get();
    }

    /**
     * Get clients count by age group.
     */
    public static function getAgeGroupStats()
    {
        $clients = static::whereNotNull('date_of_birth')->get();
        
        $stats = [
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-54' => 0,
            '55-64' => 0,
            '65+' => 0
        ];
        
        foreach ($clients as $client) {
            $ageGroup = $client->age_group;
            if (isset($stats[$ageGroup])) {
                $stats[$ageGroup]++;
            }
        }
        
        return $stats;
    }
}
