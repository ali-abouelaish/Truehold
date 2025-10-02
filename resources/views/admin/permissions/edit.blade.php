@extends('layouts.admin')

@section('title', 'Edit Admin Permissions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Admin Permissions</h1>
            <p class="text-gray-600 mt-1">Configure permissions for {{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.permissions.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Permissions
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.permissions.update', $user) }}">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Section Permissions</h2>
                <p class="text-sm text-gray-600 mt-1">Select what {{ $user->name }} can do in each admin section</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($sections as $sectionKey => $sectionName)
                        @php
                            $permission = $permissions->get($sectionKey);
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $sectionName }}</h3>
                            
                            <input type="hidden" name="permissions[{{ $loop->index }}][section]" value="{{ $sectionKey }}">
                            
                            <div class="space-y-3">
                                <label class="flex items-center">
                                <input type="checkbox" 
                                       name="permissions[{{ $loop->index }}][can_view]" 
                                       value="1"
                                       {{ $permission && $permission->can_view ? 'checked' : '' }}
                                       class="section-permission h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       data-section="{{ $sectionKey }}">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Full Access</span>
                            </label>
                            
                            
                            <div class="ml-6 text-xs text-gray-500">
                                <div>✓ View • Create • Edit • Delete</div>
                            </div>
                            
                            <!-- Hidden inputs for individual permissions (will be set by JavaScript) -->
                            <input type="hidden" name="permissions[{{ $loop->index }}][can_create]" value="{{ $permission && $permission->can_create ? '1' : '0' }}" class="create-permission">
                            <input type="hidden" name="permissions[{{ $loop->index }}][can_edit]" value="{{ $permission && $permission->can_edit ? '1' : '0' }}" class="edit-permission">
                            <input type="hidden" name="permissions[{{ $loop->index }}][can_delete]" value="{{ $permission && $permission->can_delete ? '1' : '0' }}" class="delete-permission">
                        </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <strong>Note:</strong> Users without "View" permission won't see the section in the admin navigation.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.permissions.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Permissions
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-blue-900 mb-2">Simplified Permission System</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• <strong>Full Access:</strong> One checkbox per section grants all permissions (View, Create, Edit, Delete)</li>
            <li>• <strong>No Access:</strong> Unchecked sections are completely hidden from the user</li>
            <li>• <strong>Easy Management:</strong> Simply check the sections you want the user to access</li>
            <li>• <strong>Admin Permissions:</strong> Only give this to trusted administrators</li>
            <li>• <strong>Visual Feedback:</strong> Green highlighting shows which sections are enabled</li>
        </ul>
    </div>
</div>

<script>
// Permission management with unique namespace
window.PermissionManager = (function() {
    'use strict';
    
    function init() {
        // Clear any existing listeners
        document.querySelectorAll('.section-permission').forEach(checkbox => {
            if (checkbox._permissionHandler) {
                checkbox.removeEventListener('change', checkbox._permissionHandler);
            }
        });
        
        // Add new listeners
        document.querySelectorAll('.section-permission').forEach((checkbox, index) => {
            checkbox._permissionHandler = function(event) {
                event.stopPropagation();
                event.preventDefault();
                
                const sectionContainer = this.closest('.border');
                const sectionTitle = sectionContainer.querySelector('h3');
                
                // Only affect this specific section
                const createInput = sectionContainer.querySelector('.create-permission');
                const editInput = sectionContainer.querySelector('.edit-permission');
                const deleteInput = sectionContainer.querySelector('.delete-permission');
                
                if (this.checked) {
                    if (createInput) createInput.value = '1';
                    if (editInput) editInput.value = '1';
                    if (deleteInput) deleteInput.value = '1';
                    sectionContainer.classList.add('bg-green-50', 'border-green-200');
                } else {
                    if (createInput) createInput.value = '0';
                    if (editInput) editInput.value = '0';
                    if (deleteInput) deleteInput.value = '0';
                    sectionContainer.classList.remove('bg-green-50', 'border-green-200');
                }
                
                return false;
            };
            
            checkbox.addEventListener('change', checkbox._permissionHandler, true);
            
            // Initialize state
            if (checkbox.checked) {
                checkbox.closest('.border').classList.add('bg-green-50', 'border-green-200');
            }
        });
    }
    
    return {
        init: init
    };
})();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    PermissionManager.init();
});
</script>
@endsection
