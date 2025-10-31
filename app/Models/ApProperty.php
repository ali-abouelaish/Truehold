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
    ];

    protected $casts = [
        'availability' => 'date',
        'images_url' => 'array',
        'pcm' => 'integer',
        'n_rooms' => 'integer',
        'n_bathrooms' => 'integer',
    ];

    /**
     * Human-friendly availability label.
     */
    public function getAvailabilityLabelAttribute(): string
    {
        if ($this->availability && $this->availability->isToday()) {
            return 'Available now';
        }

        return $this->availability ? $this->availability->format('d/m/Y') : 'TBC';
    }
}


