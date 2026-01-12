<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('admin.invoices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'required|string',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'payment_terms' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        // Get agent name from authenticated user
        $agentName = auth()->user()->name ?? 'Unknown Agent';

        DB::beginTransaction();
        try {
            $invoice = new Invoice();
            $invoice->invoice_number = $invoice->generateInvoiceNumber();
            $invoice->fill($validated);
            $invoice->agent_name = $agentName;
            
            // Set company details (you can make these configurable)
            $invoice->company_name = 'Truehold Group Limited';
            $invoice->company_address = 'Business Banking';
            $invoice->account_holder_name = 'TRUEHOLD GROUP LTD';
            $invoice->account_number = '63935841';
            $invoice->sort_code = '20-41-50';
            $invoice->bank_name = 'Business Banking';
            
            $invoice->calculateTotals();
            $invoice->save();

            // Send email to board@truehold.co.uk
            $this->sendInvoiceNotification($invoice);

            DB::commit();
            return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice created successfully and notification sent!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return view('admin.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'required|string',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'payment_terms' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        // Get agent name from authenticated user for updates
        $agentName = auth()->user()->name ?? 'Unknown Agent';

        DB::beginTransaction();
        try {
            $invoice->fill($validated);
            $invoice->agent_name = $agentName;
            $invoice->calculateTotals();
            $invoice->save();

            // Send email notification for invoice update
            $this->sendInvoiceUpdateNotification($invoice);
            
            DB::commit();
            return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice updated successfully and notification sent!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update invoice: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted successfully!');
    }

    public function pdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function markAsSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);
        return back()->with('success', 'Invoice marked as sent!');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'amount_paid' => $invoice->total_amount,
            'balance_due' => 0
        ]);
        return back()->with('success', 'Invoice marked as paid!');
    }

    public function duplicate(Invoice $invoice)
    {
        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = $newInvoice->generateInvoiceNumber();
        $newInvoice->invoice_date = now()->toDateString();
        $newInvoice->due_date = now()->addDays(7)->toDateString();
        $newInvoice->payment_terms = 'Net 7';
        $newInvoice->status = 'draft';
        $newInvoice->amount_paid = 0;
        $newInvoice->calculateTotals();
        $newInvoice->save();

        return redirect()->route('admin.invoices.edit', $newInvoice)->with('success', 'Invoice duplicated successfully!');
    }

    /**
     * Send invoice notification email to board@truehold.co.uk
     */
    private function sendInvoiceNotification(Invoice $invoice)
    {
        try {
            $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
            
            Mail::send('emails.invoice-notification', [
                'invoice' => $invoice,
                'agentName' => $invoice->agent_name
            ], function ($message) use ($invoice, $pdf) {
                $message->from('crm@truehold.co.uk', 'Truehold Group System')
                        ->to('board@truehold.co.uk')
                        ->subject('New Invoice Generated - ' . $invoice->invoice_number)
                        ->attachData($pdf->output(), "invoice-{$invoice->invoice_number}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });
        } catch (\Exception $e) {
            // Log the error but don't fail the invoice creation
            \Log::error('Failed to send invoice notification: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice update notification email to board@truehold.co.uk
     */
    private function sendInvoiceUpdateNotification(Invoice $invoice)
    {
        try {
            $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
            
            Mail::send('emails.invoice-update-notification', [
                'invoice' => $invoice,
                'agentName' => $invoice->agent_name
            ], function ($message) use ($invoice, $pdf) {
                $message->from('crm@truehold.co.uk', 'Truehold Group System')
                        ->to('board@truehold.co.uk')
                        ->subject('Invoice Updated - ' . $invoice->invoice_number)
                        ->attachData($pdf->output(), "invoice-{$invoice->invoice_number}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });
        } catch (\Exception $e) {
            // Log the error but don't fail the invoice update
            \Log::error('Failed to send invoice update notification: ' . $e->getMessage());
        }
    }
}
