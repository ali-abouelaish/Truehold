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
        'client_count',
        'rent_by_agent',
        'rental_agent_id',
        'marketing_agent_id',
        'notes',
        'status',
        'paid',
        'paid_at',
        'refunded',
        'refunded_at',
        // Document upload fields
        'client_contract',
        'payment_proof',
        'client_id_document',
        // Cash document fields (merged from cash_documents table)
        'contact_images',
        'client_id_image',
        'cash_receipt_image',
        'cash_document_status',
        'cash_document_submitted_at',
        'cash_document_reviewed_at',
        'cash_document_reviewed_by',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'consultation_fee' => 'decimal:2',
        'paid' => 'boolean',
        'paid_at' => 'datetime',
        'refunded' => 'boolean',
        'refunded_at' => 'datetime',
        'contact_images' => 'array',
        'cash_document_submitted_at' => 'datetime',
        'cash_document_reviewed_at' => 'datetime',
    ];

    /**
     * Get display name for rent_by_agent (handles ID or name string)
     */
    public function getRentByAgentNameAttribute(): string
    {
        try {
            $value = $this->rent_by_agent;
            if (empty($value)) {
                return 'N/A';
            }
            // If numeric, treat as user_id and resolve to user's name
            if (is_numeric($value)) {
                $user = \App\Models\User::find((int) $value);
                return $user?->name ?? (string) $value;
            }
            // Otherwise already a name
            return (string) $value;
        } catch (\Throwable $e) {
            return (string) ($this->rent_by_agent ?? 'N/A');
        }
    }


    /**
     * Get display name for marketing_agent (uses marketing_agent_id foreign key)
     */
    public function getMarketingAgentNameAttribute(): string
    {
        try {
            if (empty($this->marketing_agent_id)) {
                return 'N/A';
            }
            $user = \App\Models\User::find($this->marketing_agent_id);
            return $user?->name ?? 'N/A';
        } catch (\Throwable $e) {
            return 'N/A';
        }
    }

    /**
     * Generate a unique rental code
     */
    public static function generateRentalCode(): string
    {
        // Get the last rental code
        $lastRentalCode = self::orderBy('id', 'desc')->first();
        
        if (!$lastRentalCode) {
            // First rental code starts from CC0121
            $nextNumber = 121;
        } else {
            // Extract number from last code (e.g., "CC0121" -> 121)
            preg_match('/CC(\d+)/', $lastRentalCode->rental_code, $matches);
            if (isset($matches[1])) {
                $lastNumber = (int)$matches[1];
                // If the last number is less than 121, start from 121
                $nextNumber = $lastNumber >= 121 ? $lastNumber + 1 : 121;
            } else {
                // If no valid number found, start from 121
                $nextNumber = 121;
            }
        }
        
        // Format as CC0121, CC0122, etc.
        return 'CC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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
     * Scope for filtering rental codes that have cash documents
     */
    public function scopeWithCashDocuments($query)
    {
        return $query->whereNotNull('contact_images')
            ->orWhereNotNull('client_id_image')
            ->orWhereNotNull('cash_receipt_image');
    }

    /**
     * Get the client that owns the rental code
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who reviewed the cash documents
     */
    public function cashDocumentReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cash_document_reviewed_by');
    }

    /**
     * Get formatted rental date
     */
    public function getFormattedRentalDateAttribute(): string
    {
        return $this->rental_date->format('d/m/Y');
    }

    /**
     * Get the rental agent user
     */
    public function rentalAgent()
    {
        return $this->belongsTo(User::class, 'rental_agent_id');
    }

    /**
     * Get the marketing agent user
     */
    public function marketingAgentUser()
    {
        return $this->belongsTo(User::class, 'marketing_agent_id');
    }

    /**
     * Check if this rental code has any documents uploaded
     */
    public function getHasDocumentsAttribute(): bool
    {
        // Check client_contract (can be JSON array, single string, or null)
        if ($this->hasValidDocument('client_contract')) {
            return true;
        }

        // Check payment_proof (can be JSON array, single string, or null)
        if ($this->hasValidDocument('payment_proof')) {
            return true;
        }

        // Check client_id_document (can be JSON array, single string, or null)
        if ($this->hasValidDocument('client_id_document')) {
            return true;
        }

        // Check other document fields
        if (!empty($this->client_id_image) || !empty($this->cash_receipt_image)) {
            return true;
        }

        // Check contact_images array
        if (!empty($this->contact_images) && is_array($this->contact_images) && count($this->contact_images) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Helper to check if a document field has valid content
     */
    private function hasValidDocument(string $field): bool
    {
        $value = $this->$field;

        if (empty($value)) {
            return false;
        }

        // If it's a string, check if it's a JSON array or a file path
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            // Valid JSON array with at least one non-empty item
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validItems = array_filter($decoded, function($item) {
                    return !empty($item) && is_string($item);
                });
                return count($validItems) > 0;
            }
            // Not JSON, check if it's a non-empty string (file path)
            return !empty(trim($value));
        }

        // If it's already an array
        if (is_array($value)) {
            $validItems = array_filter($value, function($item) {
                return !empty($item) && is_string($item);
            });
            return count($validItems) > 0;
        }

        return false;
    }

}
