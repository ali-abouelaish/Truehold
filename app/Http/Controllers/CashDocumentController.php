<?php

namespace App\Http\Controllers;

use App\Models\CashDocument;
use App\Models\Client;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CashDocumentController extends Controller
{
    /**
     * Display a listing of cash documents.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get cash documents based on user role
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            $cashDocuments = CashDocument::with(['client', 'agent', 'reviewer'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // For agents, only show their own documents
            $agent = Agent::where('user_id', $user->id)->first();
            if ($agent) {
                $cashDocuments = CashDocument::with(['client', 'agent', 'reviewer'])
                    ->where('agent_id', $agent->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
            } else {
                $cashDocuments = collect();
            }
        }

        return view('cash-documents.index', compact('cashDocuments'));
    }

    /**
     * Show the form for creating a new cash document.
     */
    public function create()
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->back()->with('error', 'You must be an agent to submit cash documents.');
        }

        // Get clients assigned to this agent
        $clients = Client::where('agent_id', $agent->id)
            ->orderBy('full_name')
            ->get();

        return view('cash-documents.create', compact('clients', 'agent'));
    }

    /**
     * Store a newly created cash document.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent) {
            return redirect()->back()->with('error', 'You must be an agent to submit cash documents.');
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'contact_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max per image
            'client_id_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'cash_receipt_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:1000',
        ], [
            'contact_images.*.required' => 'At least one contact document image is required.',
            'contact_images.*.image' => 'Contact document must be an image.',
            'contact_images.*.mimes' => 'Contact document must be a JPEG, PNG, JPG, or GIF image.',
            'contact_images.*.max' => 'Contact document image must not exceed 5MB.',
            'client_id_image.required' => 'Client ID image is required.',
            'client_id_image.image' => 'Client ID must be an image.',
            'client_id_image.mimes' => 'Client ID must be a JPEG, PNG, JPG, or GIF image.',
            'client_id_image.max' => 'Client ID image must not exceed 5MB.',
            'cash_receipt_image.required' => 'Cash receipt image is required.',
            'cash_receipt_image.image' => 'Cash receipt must be an image.',
            'cash_receipt_image.mimes' => 'Cash receipt must be a JPEG, PNG, JPG, or GIF image.',
            'cash_receipt_image.max' => 'Cash receipt image must not exceed 5MB.',
        ]);

        // Validate that we have between 1 and 4 contact images
        if ($request->hasFile('contact_images')) {
            $contactImagesCount = count($request->file('contact_images'));
            if ($contactImagesCount < 1 || $contactImagesCount > 4) {
                $validator->errors()->add('contact_images', 'You must upload between 1 and 4 contact document images.');
            }
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Upload contact images
            $contactImages = [];
            if ($request->hasFile('contact_images')) {
                foreach ($request->file('contact_images') as $image) {
                    $path = $image->store('cash-documents/contact-images', 'public');
                    $contactImages[] = $path;
                }
            }

            // Upload client ID image
            $clientIdPath = null;
            if ($request->hasFile('client_id_image')) {
                $clientIdPath = $request->file('client_id_image')->store('cash-documents/client-ids', 'public');
            }

            // Upload cash receipt image
            $cashReceiptPath = null;
            if ($request->hasFile('cash_receipt_image')) {
                $cashReceiptPath = $request->file('cash_receipt_image')->store('cash-documents/cash-receipts', 'public');
            }

            // Create cash document
            $cashDocument = CashDocument::create([
                'client_id' => $request->client_id,
                'agent_id' => $agent->id,
                'contact_images' => $contactImages,
                'client_id_image' => $clientIdPath,
                'cash_receipt_image' => $cashReceiptPath,
                'notes' => $request->notes,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            return redirect()->route('cash-documents.show', $cashDocument)
                ->with('success', 'Cash document submitted successfully. It will be reviewed by an administrator.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while uploading the documents. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified cash document.
     */
    public function show(CashDocument $cashDocument)
    {
        $user = Auth::user();
        
        // Check if user has permission to view this document
        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            $agent = Agent::where('user_id', $user->id)->first();
            if (!$agent || $cashDocument->agent_id !== $agent->id) {
                abort(403, 'You do not have permission to view this document.');
            }
        }

        $cashDocument->load(['client', 'agent', 'reviewer']);

        return view('cash-documents.show', compact('cashDocument'));
    }

    /**
     * Show the form for editing the specified cash document.
     */
    public function edit(CashDocument $cashDocument)
    {
        $user = Auth::user();
        
        // Only allow editing if document is pending and user is the agent who submitted it
        if ($cashDocument->status !== 'pending') {
            return redirect()->route('cash-documents.show', $cashDocument)
                ->with('error', 'You can only edit pending documents.');
        }

        $agent = Agent::where('user_id', $user->id)->first();
        if (!$agent || $cashDocument->agent_id !== $agent->id) {
            abort(403, 'You do not have permission to edit this document.');
        }

        $clients = Client::where('agent_id', $agent->id)
            ->orderBy('full_name')
            ->get();

        return view('cash-documents.edit', compact('cashDocument', 'clients', 'agent'));
    }

    /**
     * Update the specified cash document.
     */
    public function update(Request $request, CashDocument $cashDocument)
    {
        $user = Auth::user();
        $agent = Agent::where('user_id', $user->id)->first();
        
        if (!$agent || $cashDocument->agent_id !== $agent->id) {
            abort(403, 'You do not have permission to update this document.');
        }

        if ($cashDocument->status !== 'pending') {
            return redirect()->route('cash-documents.show', $cashDocument)
                ->with('error', 'You can only edit pending documents.');
        }

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

            // Handle contact images update
            if ($request->hasFile('contact_images')) {
                // Delete old contact images
                if ($cashDocument->contact_images) {
                    foreach ($cashDocument->contact_images as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                // Upload new contact images
                $contactImages = [];
                foreach ($request->file('contact_images') as $image) {
                    $path = $image->store('cash-documents/contact-images', 'public');
                    $contactImages[] = $path;
                }
                $updateData['contact_images'] = $contactImages;
            }

            // Handle client ID image update
            if ($request->hasFile('client_id_image')) {
                // Delete old client ID image
                if ($cashDocument->client_id_image) {
                    Storage::disk('public')->delete($cashDocument->client_id_image);
                }

                // Upload new client ID image
                $clientIdPath = $request->file('client_id_image')->store('cash-documents/client-ids', 'public');
                $updateData['client_id_image'] = $clientIdPath;
            }

            // Handle cash receipt image update
            if ($request->hasFile('cash_receipt_image')) {
                // Delete old cash receipt image
                if ($cashDocument->cash_receipt_image) {
                    Storage::disk('public')->delete($cashDocument->cash_receipt_image);
                }

                // Upload new cash receipt image
                $cashReceiptPath = $request->file('cash_receipt_image')->store('cash-documents/cash-receipts', 'public');
                $updateData['cash_receipt_image'] = $cashReceiptPath;
            }

            $cashDocument->update($updateData);

            return redirect()->route('cash-documents.show', $cashDocument)
                ->with('success', 'Cash document updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating the document. Please try again.')
                ->withInput();
        }
    }

    /**
     * Approve a cash document (admin only).
     */
    public function approve(CashDocument $cashDocument)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            abort(403, 'You do not have permission to approve documents.');
        }

        $cashDocument->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
        ]);

        return redirect()->route('cash-documents.show', $cashDocument)
            ->with('success', 'Cash document approved successfully.');
    }

    /**
     * Reject a cash document (admin only).
     */
    public function reject(Request $request, CashDocument $cashDocument)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            abort(403, 'You do not have permission to reject documents.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $cashDocument->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
            'notes' => $cashDocument->notes . "\n\nRejection Reason: " . $request->rejection_reason,
        ]);

        return redirect()->route('cash-documents.show', $cashDocument)
            ->with('success', 'Cash document rejected successfully.');
    }

    /**
     * Remove the specified cash document from storage.
     */
    public function destroy(CashDocument $cashDocument)
    {
        $user = Auth::user();
        
        // Only allow deletion if user is admin or the agent who submitted it
        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            $agent = Agent::where('user_id', $user->id)->first();
            if (!$agent || $cashDocument->agent_id !== $agent->id) {
                abort(403, 'You do not have permission to delete this document.');
            }
        }

        try {
            // Delete associated files
            if ($cashDocument->contact_images) {
                foreach ($cashDocument->contact_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            if ($cashDocument->client_id_image) {
                Storage::disk('public')->delete($cashDocument->client_id_image);
            }
            
            if ($cashDocument->cash_receipt_image) {
                Storage::disk('public')->delete($cashDocument->cash_receipt_image);
            }

            $cashDocument->delete();

            return redirect()->route('cash-documents.index')
                ->with('success', 'Cash document deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the document. Please try again.');
        }
    }
}
