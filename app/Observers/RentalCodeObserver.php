<?php

namespace App\Observers;

use App\Models\RentalCode;

class RentalCodeObserver
{
    /**
     * Handle the RentalCode "created" event.
     */
    public function created(RentalCode $rentalCode): void
    {
        // Log the creation for debugging
        \Log::info('Rental code created', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code,
            'consultation_fee' => $rentalCode->consultation_fee,
            'rent_by_agent' => $rentalCode->rent_by_agent,
            'marketing_agent' => $rentalCode->marketing_agent,
            'payment_method' => $rentalCode->payment_method
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();
    }

    /**
     * Handle the RentalCode "updated" event.
     */
    public function updated(RentalCode $rentalCode): void
    {
        // Log the update for debugging
        \Log::info('Rental code updated', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code,
            'consultation_fee' => $rentalCode->consultation_fee,
            'rent_by_agent' => $rentalCode->rent_by_agent,
            'marketing_agent' => $rentalCode->marketing_agent,
            'payment_method' => $rentalCode->payment_method,
            'paid' => $rentalCode->paid
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();
    }

    /**
     * Handle the RentalCode "deleted" event.
     */
    public function deleted(RentalCode $rentalCode): void
    {
        // Log the deletion for debugging
        \Log::info('Rental code deleted', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();
    }

    /**
     * Handle the RentalCode "restored" event.
     */
    public function restored(RentalCode $rentalCode): void
    {
        //
    }

    /**
     * Handle the RentalCode "force deleted" event.
     */
    public function forceDeleted(RentalCode $rentalCode): void
    {
        // Log the force deletion for debugging
        \Log::info('Rental code force deleted', [
            'rental_code_id' => $rentalCode->id,
            'rental_code' => $rentalCode->rental_code
        ]);
        
        // Clear any earnings cache if it exists
        $this->clearEarningsCache();
    }
    
    /**
     * Clear earnings cache if it exists
     */
    private function clearEarningsCache(): void
    {
        // Clear any potential cache keys related to earnings
        $cacheKeys = [
            'agent_earnings_*',
            'rental_codes_earnings_*',
            'agent_analytics_*'
        ];
        
        foreach ($cacheKeys as $pattern) {
            if (function_exists('cache')) {
                // Clear cache by pattern if supported
                try {
                    cache()->forget($pattern);
                } catch (\Exception $e) {
                    // Cache driver might not support pattern clearing
                    \Log::info('Cache pattern clearing not supported', ['pattern' => $pattern]);
                }
            }
        }
        
        \Log::info('Earnings cache cleared after rental code change');
    }
}
