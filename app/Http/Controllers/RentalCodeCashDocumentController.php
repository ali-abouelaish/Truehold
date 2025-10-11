<?php

namespace App\Http\Controllers;

use App\Models\RentalCode;
use App\Models\Client;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RentalCodeCashDocumentController extends Controller
{
    /**
     * Display a listing of rental codes with cash documents.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get rental codes with cash documents based on user role
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            $rentalCodes = RentalCode::with(['client', 'cashDocumentReviewer'])
                ->withCashDocuments()
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // For agents, only show their own rental codes with cash documents
            $agent = Agent::where('user_id', $user->id)->first();
            if ($agent) {
                $rentalCodes = RentalCode::with(['client', 'cashDocumentReviewer'])
                    ->withCashDocuments()
                    ->where('rent_by_agent', $agent->company_name ?? $user->name)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
            } else {
                $rentalCodes = collect();
            }
        }

        return view('rental-codes.cash-documents.index', compact('rentalCodes'));
    }

    /**
     * Show the form for creating cash documents for a rental code.
     */
    public function create($rentalCodeId)
    {
        $rentalCode = RentalCode::with('client')->findOrFail($rentalCodeId);
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        // Get existing clients for selection
        $existingClients = Client::orderBy('full_name')->get();
        
        return view('rental-codes.cash-documents.create', compact('rentalCode', 'existingClients', 'agent'));
    }

    /**
     * Store cash documents for a rental code.
     */
    public function store(Request $request, $rentalCodeId)
    {
        $rentalCode = RentalCode::findOrFail($rentalCodeId);
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'contact_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'client_id_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'cash_receipt_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:1000',
        ], [
            'client_id.required' => 'Client selection is required.',
            'client_id.exists' => 'Selected client does not exist.',
            'contact_images.*.required' => 'At least one contact image is required.',
            'contact_images.*.image' => 'Contact images must be image files.',
            'contact_images.*.mimes' => 'Contact images must be JPEG, PNG, JPG, or GIF files.',
            'contact_images.*.max' => 'Contact images must not exceed 5MB.',
            'client_id_image.required' => 'Client ID image is required.',
            'client_id_image.image' => 'Client ID must be an image.',
            'client_id_image.mimes' => 'Client ID must be a JPEG, PNG, JPG, or GIF image.',
            'client_id_image.max' => 'Client ID image must not exceed 5MB.',
            'cash_receipt_image.required' => 'Cash receipt image is required.',
            'cash_receipt_image.image' => 'Cash receipt must be an image.',
            'cash_receipt_image.mimes' => 'Cash receipt must be a JPEG, PNG, JPG, or GIF image.',
            'cash_receipt_image.max' => 'Cash receipt image must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle contact images (up to 4)
            $contactImages = [];
            if ($request->hasFile('contact_images')) {
                foreach ($request->file('contact_images') as $index => $file) {
                    if ($index < 4) { // Limit to 4 images
                        $path = $file->store('cash-documents/contacts', 'public');
                        $contactImages[] = $path;
                    }
                }
            }

            // Handle client ID image
            $clientIdPath = null;
            if ($request->hasFile('client_id_image')) {
                $clientIdPath = $request->file('client_id_image')->store('cash-documents/client-ids', 'public');
            }

            // Handle cash receipt image
            $cashReceiptPath = null;
            if ($request->hasFile('cash_receipt_image')) {
                $cashReceiptPath = $request->file('cash_receipt_image')->store('cash-documents/cash-receipts', 'public');
            }

            // Update rental code with cash document data
            $rentalCode->update([
                'client_id' => $request->client_id,
                'contact_images' => $contactImages,
                'client_id_image' => $clientIdPath,
                'cash_receipt_image' => $cashReceiptPath,
                'notes' => $request->notes,
                'cash_document_status' => 'pending',
                'cash_document_submitted_at' => now(),
            ]);

            return redirect()->route('rental-codes.cash-documents.show', $rentalCode->id)
                ->with('success', 'Cash documents submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit cash documents: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified rental code with cash documents.
     */
    public function show($rentalCodeId)
    {
        $rentalCode = RentalCode::with(['client', 'cashDocumentReviewer'])
            ->findOrFail($rentalCodeId);

        return view('rental-codes.cash-documents.show', compact('rentalCode'));
    }

    /**
     * Show the form for editing cash documents.
     */
    public function edit($rentalCodeId)
    {
        $rentalCode = RentalCode::with('client')->findOrFail($rentalCodeId);
        $existingClients = Client::orderBy('full_name')->get();

        return view('rental-codes.cash-documents.edit', compact('rentalCode', 'existingClients'));
    }

    /**
     * Update cash documents for a rental code.
     */
    public function update(Request $request, $rentalCodeId)
    {
        $rentalCode = RentalCode::findOrFail($rentalCodeId);

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'contact_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'client_id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'cash_receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'client_id' => $request->client_id,
                'notes' => $request->notes,
            ];

            // Handle contact images
            if ($request->hasFile('contact_images')) {
                // Delete old contact images
                if ($rentalCode->contact_images) {
                    foreach ($rentalCode->contact_images as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }

                $contactImages = [];
                foreach ($request->file('contact_images') as $index => $file) {
                    if ($index < 4) {
                        $path = $file->store('cash-documents/contacts', 'public');
                        $contactImages[] = $path;
                    }
                }
                $updateData['contact_images'] = $contactImages;
            }

            // Handle client ID image
            if ($request->hasFile('client_id_image')) {
                // Delete old client ID image
                if ($rentalCode->client_id_image) {
                    Storage::disk('public')->delete($rentalCode->client_id_image);
                }
                $updateData['client_id_image'] = $request->file('client_id_image')->store('cash-documents/client-ids', 'public');
            }

            // Handle cash receipt image
            if ($request->hasFile('cash_receipt_image')) {
                // Delete old cash receipt image
                if ($rentalCode->cash_receipt_image) {
                    Storage::disk('public')->delete($rentalCode->cash_receipt_image);
                }
                $updateData['cash_receipt_image'] = $request->file('cash_receipt_image')->store('cash-documents/cash-receipts', 'public');
            }

            $rentalCode->update($updateData);

            return redirect()->route('rental-codes.cash-documents.show', $rentalCode->id)
                ->with('success', 'Cash documents updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update cash documents: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Approve cash documents.
     */
    public function approve($rentalCodeId)
    {
        $rentalCode = RentalCode::findOrFail($rentalCodeId);
        
        $rentalCode->update([
            'cash_document_status' => 'approved',
            'cash_document_reviewed_at' => now(),
            'cash_document_reviewed_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Cash documents approved successfully!');
    }

    /**
     * Reject cash documents.
     */
    public function reject($rentalCodeId)
    {
        $rentalCode = RentalCode::findOrFail($rentalCodeId);
        
        $rentalCode->update([
            'cash_document_status' => 'rejected',
            'cash_document_reviewed_at' => now(),
            'cash_document_reviewed_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Cash documents rejected successfully!');
    }

    /**
     * Remove cash documents from a rental code.
     */
    public function destroy($rentalCodeId)
    {
        $rentalCode = RentalCode::findOrFail($rentalCodeId);

        try {
            // Delete files from storage
            if ($rentalCode->contact_images) {
                foreach ($rentalCode->contact_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            if ($rentalCode->client_id_image) {
                Storage::disk('public')->delete($rentalCode->client_id_image);
            }
            if ($rentalCode->cash_receipt_image) {
                Storage::disk('public')->delete($rentalCode->cash_receipt_image);
            }

            // Clear cash document fields
            $rentalCode->update([
                'contact_images' => null,
                'client_id_image' => null,
                'cash_receipt_image' => null,
                'cash_document_status' => null,
                'cash_document_submitted_at' => null,
                'cash_document_reviewed_at' => null,
                'cash_document_reviewed_by' => null,
            ]);

            return redirect()->route('rental-codes.cash-documents.index')
                ->with('success', 'Cash documents removed successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove cash documents: ' . $e->getMessage());
        }
    }
}
