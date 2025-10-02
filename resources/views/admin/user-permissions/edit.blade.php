@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Edit Permissions for {{ $user->name }}</h1>
        <a href="{{ route('admin.user-permissions.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Users
        </a>
    </div>

    <form action="{{ route('admin.user-permissions.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Sections</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Check the sections this user should have access to. Unchecked sections will be removed.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($sections as $sectionKey => $sectionName)
                        @php
                            $hasPermission = $permissions->has($sectionKey) && $permissions->get($sectionKey)->can_access;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 {{ $hasPermission ? 'bg-green-50 border-green-200' : '' }}">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="permissions[{{ $sectionKey }}]" 
                                       value="1"
                                       {{ $hasPermission ? 'checked' : '' }}
                                       class="section-permission h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 font-medium">{{ $sectionName }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.user-permissions.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-save mr-2"></i>Save Permissions
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.section-permission');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const container = this.closest('.border');
            
            if (this.checked) {
                container.classList.add('bg-green-50', 'border-green-200');
            } else {
                container.classList.remove('bg-green-50', 'border-green-200');
            }
        });
        
        // Initialize visual state
        if (checkbox.checked) {
            checkbox.closest('.border').classList.add('bg-green-50', 'border-green-200');
        }
    });
});
</script>
@endsection
