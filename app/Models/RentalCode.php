<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_code',
        'rental_date',
        'consultation_fee',
        'payment_method',
        'property',
        'licensor',
        'client_id',
        'client_full_name',
        'client_date_of_birth',
        'client_phone_number',
        'client_email',
        'client_nationality',
        'client_current_address',
        'client_company_university_name',
        'client_company_university_address',
        'client_position_role',
        'rent_by_agent',
        'client_by_agent',
        'notes',
        'status',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'client_date_of_birth' => 'date',
        'consultation_fee' => 'decimal:2',
    ];

    /**
     * Generate a unique rental code
     */
    public static function generateRentalCode(): string
    {
        do {
            $code = 'CC' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('rental_code', $code)->exists());

        return $code;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('rental_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted rental date
     */
    public function getFormattedRentalDateAttribute(): string
    {
        return $this->rental_date->format('d/m/Y');
    }

    /**
     * Get formatted client date of birth
     */
    public function getFormattedClientDateOfBirthAttribute(): string
    {
        return $this->client_date_of_birth->format('M d, Y');
    }

    /**
     * Get the client associated with this rental code
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
