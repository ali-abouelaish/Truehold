<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'link', 'title', 'location', 'latitude', 'longitude', 'status', 'price', 'description',
        'property_type', 'available_date', 'min_term', 'max_term', 'deposit', 'bills_included',
        'furnishings', 'parking', 'garden', 'broadband', 'housemates', 'total_rooms',
        'smoker', 'pets', 'occupation', 'gender', 'couples_ok', 'couples_allowed', 'smoking_ok', 'pets_ok',
        'pref_occupation', 'references', 'min_age', 'max_age', 'photo_count', 'first_photo_url',
        'all_photos', 'photos', 'contact_info', 'management_company', 'agent_name', 'amenities',
        'balcony_roof_terrace', 'disabled_access', 'living_room', 'agent_id'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'photo_count' => 'integer',
        'photos' => 'array',
    ];

    /**
     * Set the photos attribute - ensure it's always stored as JSON
     */
    public function setPhotosAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['photos'] = json_encode($value);
        } else {
            $this->attributes['photos'] = $value;
        }
    }

    // Get all photos as an array (from photos column)
    public function getPhotosArrayAttribute()
    {
        if (empty($this->photos)) {
            return [];
        }
        
        // If photos is already an array, return it
        if (is_array($this->photos)) {
            return $this->photos;
        }
        
        // If photos is a JSON string, decode it
        if (is_string($this->photos)) {
            $decoded = json_decode($this->photos, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    /**
     * Get the property URL (link).
     */
    public function getUrlAttribute()
    {
        return $this->link;
    }

    /**
     * Set the property URL (link).
     */
    public function setUrlAttribute($value)
    {
        $this->attributes['link'] = $value;
    }

    // Get original photos as an array (for thumbnails)
    public function getOriginalPhotosArrayAttribute()
    {
        return $this->getPhotosArrayAttribute();
    }

    // Get all photos as an array from the all_photos column
    public function getAllPhotosArrayAttribute()
    {
        if (empty($this->all_photos)) {
            return [];
        }
        
        // If all_photos is already an array, return it
        if (is_array($this->all_photos)) {
            return $this->all_photos;
        }
        
        // If all_photos is a string, split by comma and clean up
        if (is_string($this->all_photos)) {
            $photos = explode(',', $this->all_photos);
            $cleanedPhotos = [];
            
            foreach ($photos as $photo) {
                $photo = trim($photo);
                if (!empty($photo) && filter_var($photo, FILTER_VALIDATE_URL)) {
                    $cleanedPhotos[] = $photo;
                }
            }
            
            return $cleanedPhotos;
        }
        
        return [];
    }

    // Get high-quality photos array (converts square thumbnails to large images)
    public function getHighQualityPhotosArrayAttribute()
    {
        $photos = $this->getAllPhotosArrayAttribute();
        $highQualityPhotos = [];
        
        foreach ($photos as $photo) {
            // Convert Spareroom square thumbnails to large images
            if (strpos($photo, 'spareroom.co.uk') !== false && strpos($photo, '/square/') !== false) {
                $highQualityPhotos[] = str_replace('/square/', '/large/', $photo);
            } else {
                $highQualityPhotos[] = $photo;
            }
        }
        
        return $highQualityPhotos;
    }

    // Get formatted price with comprehensive error handling
    public function getFormattedPriceAttribute()
    {
        try {
            if (empty($this->price) || $this->price === 'N/A' || $this->price === 'undefined' || $this->price === 'null') {
                return 'N/A';
            }
            
            // Handle arrays
            if (is_array($this->price)) {
                return 'N/A';
            }
            
            // If price is already formatted (contains £ or currency symbol), return as is
            if (is_string($this->price) && (strpos($this->price, '£') !== false || strpos($this->price, 'pcm') !== false || strpos($this->price, 'per month') !== false)) {
                return $this->price;
            }
            
            // If price is numeric, format it
            if (is_numeric($this->price) && $this->price > 0) {
                return '£' . number_format($this->price, 2);
            }
            
            // If price is 0 or negative, return N/A
            if (is_numeric($this->price) && $this->price <= 0) {
                return 'N/A';
            }
            
            // For any other case, return N/A
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Scope for filtering by location
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    // Scope for filtering by price range
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereRaw('CAST(REPLACE(REPLACE(REPLACE(price, "£", ""), " pcm", ""), ",", "") AS DECIMAL(10,2)) BETWEEN ? AND ?', [$min, $max]);
    }

    // Scope for filtering by minimum price
    public function scopeByMinPrice($query, $minPrice)
    {
        return $query->whereRaw('CAST(REPLACE(REPLACE(REPLACE(price, "£", ""), " pcm", ""), ",", "") AS DECIMAL(10,2)) >= ?', [$minPrice]);
    }

    // Scope for filtering by maximum price
    public function scopeByMaxPrice($query, $maxPrice)
    {
        return $query->whereRaw('CAST(REPLACE(REPLACE(REPLACE(price, "£", ""), " pcm", ""), ",", "") AS DECIMAL(10,2)) <= ?', [$maxPrice]);
    }

    // Scope for properties with valid coordinates (MySQL compatible)
    public function scopeWithValidCoordinates($query)
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->whereRaw('CAST(latitude AS DECIMAL(10,8)) BETWEEN -90 AND 90')
            ->whereRaw('CAST(longitude AS DECIMAL(11,8)) BETWEEN -180 AND 180');
    }

    // Check if coordinates are valid
    public function hasValidCoordinates(): bool
    {
        if (empty($this->latitude) || empty($this->longitude)) {
            return false;
        }

        try {
            $lat = (float) $this->latitude;
            $lng = (float) $this->longitude;
            
            return (-90 <= $lat && $lat <= 90) && (-180 <= $lng && $lng <= 180);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    // Get formatted status with error handling
    public function getFormattedStatusAttribute()
    {
        try {
            if (empty($this->status) || $this->status === 'N/A' || $this->status === 'undefined' || $this->status === 'null' || !is_string($this->status)) {
                return 'Available';
            }
            
            // Handle arrays
            if (is_array($this->status)) {
                return 'Available';
            }
            
            return ucfirst($this->status);
        } catch (\Exception $e) {
            return 'Available';
        }
    }
    
    // Get formatted management company with error handling
    public function getFormattedManagementCompanyAttribute()
    {
        try {
            if (empty($this->management_company) || $this->management_company === 'N/A' || $this->management_company === 'undefined' || $this->management_company === 'null' || !is_string($this->management_company)) {
                return 'N/A';
            }
            
            // Handle arrays
            if (is_array($this->management_company)) {
                return 'N/A';
            }
            
            return $this->management_company;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    // Safe getter for any attribute that ensures string output
    public function getSafeStringAttribute($attribute, $default = 'N/A')
    {
        $value = $this->getAttribute($attribute);
        
        if (is_array($value)) {
            return is_array($value) ? implode(', ', $value) : $default;
        }
        
        if (is_null($value) || $value === 'N/A' || $value === 'undefined' || $value === 'null') {
            return $default;
        }
        
        return (string) $value;
    }

    // Additional validation scope for SQLite
    public function scopeWithValidCoordinatesStrict($query)
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->where(function($q) {
                $q->whereRaw('latitude GLOB "*[0-9]*"')
                  ->whereRaw('longitude GLOB "*[0-9]*"');
            });
    }

    // Scope for filtering by London area using coordinates
    public function scopeByLondonArea($query, $area)
    {
        if (!$area) return $query;
        
        return $query->withValidCoordinates()->where(function($q) use ($area) {
            switch (strtolower($area)) {
                case 'east':
                    // East London: longitude > -0.1 (east of central London)
                    $q->where('longitude', '>', -0.1);
                    break;
                case 'north':
                    // North London: latitude > 51.5 (north of central London)
                    $q->where('latitude', '>', 51.5);
                    break;
                case 'west':
                    // West London: longitude < -0.2 (west of central London)
                    $q->where('longitude', '<', -0.2);
                    break;
                case 'south':
                    // South London: latitude < 51.5 (south of central London)
                    $q->where('latitude', '<', 51.5);
                    break;
                case 'central':
                    // Central London: within a box around central London
                    $q->where('latitude', 'between', [51.45, 51.55])
                      ->where('longitude', 'between', [-0.2, 0.1]);
                    break;
            }
        });
    }

    // Scope for filtering by management company
    public function scopeByManagementCompany($query, $company)
    {
        if (!$company) return $query;
        
        return $query->where('management_company', 'like', "%{$company}%");
    }

    // Scope for filtering by couples allowed
    public function scopeByCouplesAllowed($query, $couplesAllowed)
    {
        if (!$couplesAllowed) return $query;
        
        if ($couplesAllowed === 'yes') {
            return $query->where(function($q) {
                $q->where('couples_ok', 'like', '%yes%')
                  ->orWhere('couples_ok', 'like', '%couples%')
                  ->orWhere('couples_ok', 'like', '%allowed%')
                  ->orWhere('couples_ok', 'like', '%welcome%');
            });
        } elseif ($couplesAllowed === 'no') {
            return $query->where(function($q) {
                $q->where('couples_ok', 'like', '%no%')
                  ->orWhere('couples_ok', 'like', '%not%')
                  ->orWhere('couples_ok', 'like', '%single%')
                  ->orWhere('couples_ok', 'like', '%individual%');
            });
        }
        
        return $query;
    }

    // Scope for filtering by London borough (approximate using coordinates)
    public function scopeByLondonBorough($query, $borough)
    {
        if (!$borough) return $query;
        
        // Define approximate coordinate boundaries for major London boroughs
        $boroughs = [
            'camden' => ['lat' => [51.52, 51.57], 'lng' => [-0.18, -0.12]],
            'islington' => ['lat' => [51.52, 51.57], 'lng' => [-0.12, -0.08]],
            'hackney' => ['lat' => [51.52, 51.57], 'lng' => [-0.08, -0.02]],
            'tower_hamlets' => ['lat' => [51.50, 51.55], 'lng' => [-0.08, 0.02]],
            'greenwich' => ['lat' => [51.45, 51.50], 'lng' => [-0.02, 0.08]],
            'lewisham' => ['lat' => [51.40, 51.50], 'lng' => [-0.02, 0.08]],
            'southwark' => ['lat' => [51.45, 51.52], 'lng' => [-0.12, -0.02]],
            'lambeth' => ['lat' => [51.45, 51.52], 'lng' => [-0.18, -0.12]],
            'wandsworth' => ['lat' => [51.40, 51.52], 'lng' => [-0.25, -0.18]],
            'hammersmith' => ['lat' => [51.45, 51.52], 'lng' => [-0.25, -0.18]],
            'kensington' => ['lat' => [51.48, 51.52], 'lng' => [-0.22, -0.18]],
            'westminster' => ['lat' => [51.48, 51.52], 'lng' => [-0.18, -0.12]],
            'city' => ['lat' => [51.50, 51.52], 'lng' => [-0.12, -0.08]],
        ];
        
        if (isset($boroughs[strtolower($borough)])) {
            $bounds = $boroughs[strtolower($borough)];
            return $query->withValidCoordinates()
                        ->where('latitude', 'between', $bounds['lat'])
                        ->where('longitude', 'between', $bounds['lng']);
        }
        
        return $query;
    }

    // Static method to get company color for consistent coloring across the application
    public static function getCompanyColor($company)
    {
        if (!$company || $company === 'N/A' || $company === '') {
            return ['fill' => '#6b7280', 'stroke' => '#ffffff']; // Gray for unknown
        }
        
        // Define color scheme for different companies
        $companyColors = [
            'iFlatShare' => ['fill' => '#3b82f6', 'stroke' => '#ffffff'], // Blue
            'AK&PROPERTIES' => ['fill' => '#ef4444', 'stroke' => '#ffffff'], // Red
            'Banksia Limited' => ['fill' => '#10b981', 'stroke' => '#ffffff'], // Green
            'Built Asset Management Limited' => ['fill' => '#f59e0b', 'stroke' => '#ffffff'], // Amber
            'Capital Living' => ['fill' => '#8b5cf6', 'stroke' => '#ffffff'], // Purple
            'JD Corp Management' => ['fill' => '#ec4899', 'stroke' => '#ffffff'], // Pink
            'North Kensington Property Consultants' => ['fill' => '#06b6d4', 'stroke' => '#ffffff'], // Cyan
            'Pisoria Ltd' => ['fill' => '#84cc16', 'stroke' => '#ffffff'], // Lime
            'UK London Flat' => ['fill' => '#f97316', 'stroke' => '#ffffff'], // Orange
            'COME TO LONDON LIMITED' => ['fill' => '#6366f1', 'stroke' => '#ffffff'], // Indigo
        ];
        
        // Check for exact matches first
        if (isset($companyColors[$company])) {
            return $companyColors[$company];
        }
        
        // Check for partial matches
        foreach ($companyColors as $key => $color) {
            if (stripos($company, $key) !== false || stripos($key, $company) !== false) {
                return $color;
            }
        }
        
        // Generate a consistent color based on company name hash
        $hash = 0;
        for ($i = 0; $i < strlen($company); $i++) {
            $hash = (($hash << 5) - $hash + ord($company[$i])) & 0xFFFFFFFF;
        }
        
        $colors = [
            ['fill' => '#3b82f6', 'stroke' => '#ffffff'], // Blue
            ['fill' => '#ef4444', 'stroke' => '#ffffff'], // Red
            ['fill' => '#10b981', 'stroke' => '#ffffff'], // Green
            ['fill' => '#f59e0b', 'stroke' => '#ffffff'], // Amber
            ['fill' => '#8b5cf6', 'stroke' => '#ffffff'], // Purple
            ['fill' => '#ec4899', 'stroke' => '#ffffff'], // Pink
            ['fill' => '#06b6d4', 'stroke' => '#ffffff'], // Cyan
            ['fill' => '#84cc16', 'stroke' => '#ffffff'], // Lime
            ['fill' => '#f97316', 'stroke' => '#ffffff'], // Orange
            ['fill' => '#6366f1', 'stroke' => '#ffffff'], // Indigo
        ];
        
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Get the agent that manages this property.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Interested clients (pivot entries) for this property.
     */
    public function interests(): HasMany
    {
        return $this->hasMany(PropertyInterest::class);
    }

    /**
     * Get the clients interested in this property.
     */
    public function interestedClients()
    {
        return $this->belongsToMany(Client::class, 'property_interests')
                    ->withPivot(['notes', 'added_by_user_id', 'created_at', 'updated_at'])
                    ->withTimestamps();
    }

    /**
     * Check if the property has an assigned agent.
     */
    public function hasAgent(): bool
    {
        return $this->agent()->exists();
    }

    /**
     * Get the agent's display name for this property.
     */
    public function getAgentDisplayNameAttribute(): string
    {
        if (!$this->agent) {
            return 'No agent assigned';
        }
        
        return $this->agent->display_name;
    }

    /**
     * Get the agent's contact information for this property.
     */
    public function getAgentContactAttribute(): string
    {
        if (!$this->agent) {
            return 'N/A';
        }
        
        return $this->agent->primary_contact;
    }

    /**
     * Get the agent's email for this property.
     */
    public function getAgentEmailAttribute(): string
    {
        if (!$this->agent) {
            return 'N/A';
        }
        
        return $this->agent->primary_email;
    }
}
