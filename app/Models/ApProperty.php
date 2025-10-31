<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApProperty extends Model
{
    use HasFactory;

    protected $table = 'ap_properties';

    protected $fillable = [
        'availability',
        'property_name',
        'pcm',
        'postcode',
        'area',
        'n_rooms',
        'n_bathrooms',
        'images_url',
        'status',
        'type',
        'is_room',
        'couples_allowed',
    ];

    protected $casts = [
        'availability' => 'date',
        'images_url' => 'array',
        'pcm' => 'integer',
        'n_rooms' => 'integer',
        'n_bathrooms' => 'integer',
        'type' => 'string',
        'is_room' => 'boolean',
        'couples_allowed' => 'boolean',
    ];

    /**
     * Human-friendly availability label.
     */
    public function getAvailabilityLabelAttribute(): string
    {
        return $this->availability ? $this->availability->format('d/m/Y') : 'TBC';
    }

    /**
     * Human-friendly status label (uses availability when applicable).
     */
    public function getStatusLabelAttribute(): string
    {
        $status = $this->status ?? 'empty_available_now';
        return match ($status) {
            'booked' => 'Booked',
            'available_on_date' => ($this->availability ? 'Available on ' . $this->availability->format('d/m/Y') : 'Available soon'),
            'renewal' => 'Renewal',
            'empty_available_now', 'available_now' => 'EMPTY AVAILABLE NOW',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}


