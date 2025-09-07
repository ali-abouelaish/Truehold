<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'payment_terms',
        'po_number',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'account_holder_name',
        'account_number',
        'sort_code',
        'bank_name',
        'client_name',
        'client_address',
        'client_email',
        'client_phone',
        'items',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'balance_due',
        'amount_paid',
        'status',
        'notes',
        'terms'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    protected function items(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true) ?? [],
            set: fn ($value) => json_encode($value)
        );
    }

    public function calculateTotals()
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += $item['quantity'] * $item['rate'];
        }
        
        $this->subtotal = $subtotal;
        $this->tax_amount = $subtotal * ($this->tax_rate / 100);
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->balance_due = $this->total_amount - $this->amount_paid;
    }

    public function generateInvoiceNumber()
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function isOverdue()
    {
        return $this->status !== 'paid' && $this->due_date < now()->toDateString();
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'overdue' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
