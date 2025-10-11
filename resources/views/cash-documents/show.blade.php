@extends('layouts.admin')

@section('title', 'Cash Document Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Cash Document Details</h1>
                <p class="text-gray-600 mt-1">Document ID: #{{ $cashDocument->id }}</p>
            </div>
            <div class="flex space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $cashDocument->status_badge_class }}">
                    <i class="{{ $cashDocument->status_icon }} mr-1"></i>
                    {{ ucfirst($cashDocument->status) }}
                </span>
                @if($cashDocument->status === 'pending' && auth()->user()->id === $cashDocument->agent->user_id)
                    <a href="{{ route('cash-documents.edit', $cashDocument) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Client Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2"></i>Client Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->client->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->client->phone_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->client->email ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nationality</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->client->nationality ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Documents -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-id-card mr-2"></i>Contact Documents
                    </h2>
                    @if($cashDocument->contact_images && count($cashDocument->contact_images) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($cashDocument->contact_images as $index => $image)
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}" alt="Contact document {{ $index + 1 }}" class="w-full h-32 object-cover rounded-lg shadow cursor-pointer" onclick="openImageModal('{{ Storage::url($image) }}', 'Contact Document {{ $index + 1 }}')">
                                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                        Document {{ $index + 1 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No contact documents uploaded</p>
                    @endif
                </div>

                <!-- Client ID Document -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-id-badge mr-2"></i>Client ID Document
                    </h2>
                    @if($cashDocument->client_id_image)
                        <div class="max-w-md">
                            <img src="{{ Storage::url($cashDocument->client_id_image) }}" alt="Client ID document" class="w-full h-48 object-cover rounded-lg shadow cursor-pointer" onclick="openImageModal('{{ Storage::url($cashDocument->client_id_image) }}', 'Client ID Document')">
                        </div>
                    @else
                        <p class="text-gray-500 italic">No client ID document uploaded</p>
                    @endif
                </div>

                <!-- Cash Receipt -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-money-bill-wave mr-2"></i>Cash Receipt
                    </h2>
                    @if($cashDocument->cash_receipt_image)
                        <div class="max-w-md">
                            <img src="{{ Storage::url($cashDocument->cash_receipt_image) }}" alt="Cash receipt" class="w-full h-48 object-cover rounded-lg shadow cursor-pointer" onclick="openImageModal('{{ Storage::url($cashDocument->cash_receipt_image) }}', 'Cash Receipt')">
                        </div>
                    @else
                        <p class="text-gray-500 italic">No cash receipt uploaded</p>
                    @endif
                </div>

                <!-- Notes -->
                @if($cashDocument->notes)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <i class="fas fa-sticky-note mr-2"></i>Notes
                        </h2>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $cashDocument->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Document Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Document Status</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cashDocument->status_badge_class }}">
                                <i class="{{ $cashDocument->status_icon }} mr-1"></i>
                                {{ ucfirst($cashDocument->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Submitted</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->formatted_submitted_date }}</p>
                        </div>
                        @if($cashDocument->reviewed_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reviewed</label>
                                <p class="text-sm text-gray-900">{{ $cashDocument->formatted_reviewed_date }}</p>
                            </div>
                            @if($cashDocument->reviewer)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reviewed By</label>
                                    <p class="text-sm text-gray-900">{{ $cashDocument->reviewer->name }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Agent Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agent Name</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->agent->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-sm text-gray-900">{{ $cashDocument->agent->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin'))
                    @if($cashDocument->status === 'pending')
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
                            <div class="space-y-3">
                                <form action="{{ route('cash-documents.approve', $cashDocument) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200" onclick="return confirm('Are you sure you want to approve this document?')">
                                        <i class="fas fa-check mr-2"></i>Approve Document
                                    </button>
                                </form>
                                <button onclick="openRejectModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-times mr-2"></i>Reject Document
                                </button>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Navigation -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="space-y-3">
                        <a href="{{ route('cash-documents.index') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 text-center block">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                        @if($cashDocument->status === 'pending' && auth()->user()->id === $cashDocument->agent->user_id)
                            <a href="{{ route('cash-documents.edit', $cashDocument) }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 text-center block">
                                <i class="fas fa-edit mr-2"></i>Edit Document
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="imageModalTitle" class="text-lg font-medium text-gray-900"></h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="text-center">
            <img id="modalImage" class="max-w-full max-h-96 mx-auto rounded-lg shadow" alt="Document image">
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Cash Document</h3>
            <form action="{{ route('cash-documents.reject', $cashDocument) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Reject Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

// Close modals when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection
