@extends('layouts.community')

@section('title', 'Create New Group')
@section('page-title', 'Create New Group')
@section('breadcrumb', 'Create Group')

@section('community-content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Group</h1>
        
        <!-- Display validation errors prominently -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                <div class="font-bold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Please fix the following errors:
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('student.community-hub.store') }}" method="POST" id="createGroupForm">
            @csrf
            
            <!-- Debug: Display all errors -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <strong class="font-bold">Validation Errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Group Name *</label>
                <div class="relative">
                    <input type="text" 
                           id="groupName" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           class="w-full border-2 rounded-lg p-2 focus:outline-none focus:ring-2 @error('name') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-uthm-blue @enderror" 
                           placeholder="Enter group name">
                    <span id="nameCheckIndicator" class="absolute right-3 top-2 hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
                <div id="nameCheckMessage" class="hidden mt-2 p-3 rounded-lg"></div>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description *</label>
                <textarea name="description" rows="4" required class="w-full border-2 rounded-lg p-2 focus:outline-none focus:ring-2 @error('description') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-uthm-blue @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Category *</label>
                <select name="category" required class="w-full border-2 border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-uthm-blue">
                    <option value="Academic" {{ old('category') == 'Academic' ? 'selected' : '' }}>Academic</option>
                    <option value="Sports" {{ old('category') == 'Sports' ? 'selected' : '' }}>Sports</option>
                    <option value="Arts" {{ old('category') == 'Arts' ? 'selected' : '' }}>Arts</option>
                    <option value="Technology" {{ old('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                    <option value="Social" {{ old('category') == 'Social' ? 'selected' : '' }}>Social</option>
                    <option value="Career" {{ old('category') == 'Career' ? 'selected' : '' }}>Career</option>
                </select>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Privacy *</label>
                <select name="privacy" required class="w-full border-2 border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-uthm-blue">
                    <option value="public" {{ old('privacy') == 'public' ? 'selected' : '' }}>Public - Anyone can join</option>
                    <option value="by_approval" {{ old('privacy') == 'by_approval' ? 'selected' : '' }}>By Approval - Request to join</option>
                    
                </select>
                @error('privacy')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Max Members (Optional)</label>
                <input type="number" name="max_members" value="{{ old('max_members') }}" class="w-full border-2 border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-uthm-blue">
                <p class="text-sm text-gray-500 mt-1">Leave empty for unlimited</p>
                @error('max_members')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('student.community-hub') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700">Create Group</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const groupNameInput = document.getElementById('groupName');
    const nameCheckMessage = document.getElementById('nameCheckMessage');
    const nameCheckIndicator = document.getElementById('nameCheckIndicator');
    const form = document.getElementById('createGroupForm');
    const submitBtn = document.getElementById('submitBtn');
    let nameExists = false;
    let isChecking = false;
    let checkTimeout;

    // Function to show error message
    function showErrorMessage(message) {
        nameCheckMessage.innerHTML = `
            <div class="flex items-start">
                <svg class="h-5 w-5 text-red-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-red-700 font-medium">Group name already exists</p>
                    <p class="text-red-600 text-sm mt-1">${message}</p>
                </div>
            </div>
        `;
        nameCheckMessage.className = 'mt-2 p-3 rounded-lg bg-red-50 border border-red-200';
        groupNameInput.classList.add('border-red-500');
        groupNameInput.classList.remove('border-green-500');
    }

    // Function to show success message
    function showSuccessMessage(message) {
        nameCheckMessage.innerHTML = `
            <div class="flex items-start">
                <svg class="h-5 w-5 text-green-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-green-700 font-medium">Group name is available</p>
                    <p class="text-green-600 text-sm mt-1">${message}</p>
                </div>
            </div>
        `;
        nameCheckMessage.className = 'mt-2 p-3 rounded-lg bg-green-50 border border-green-200';
        groupNameInput.classList.remove('border-red-500');
        groupNameInput.classList.add('border-green-500');
    }

    // Check group name when user types
    groupNameInput.addEventListener('input', function() {
        const name = this.value.trim();
        
        clearTimeout(checkTimeout);
        
        if (name.length < 3) {
            nameCheckMessage.classList.add('hidden');
            nameCheckIndicator.classList.add('hidden');
            nameExists = false;
            groupNameInput.classList.remove('border-red-500', 'border-green-500');
            groupNameInput.classList.add('border-gray-300');
            return;
        }
        
        nameCheckIndicator.classList.remove('hidden');
        isChecking = true;
        
        checkTimeout = setTimeout(() => {
            const csrfToken = document.querySelector('input[name="_token"]').value;
            
            fetch('{{ route("student.community-hub.check-group-name") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                nameCheckIndicator.classList.add('hidden');
                isChecking = false;
                nameCheckMessage.classList.remove('hidden');
                
                if (data.exists) {
                    nameExists = true;
                    showErrorMessage(data.message);
                } else {
                    nameExists = false;
                    showSuccessMessage(data.message);
                }
            })
            .catch(error => {
                console.error('Error checking group name:', error);
                nameCheckIndicator.classList.add('hidden');
                isChecking = false;
                nameExists = false;
                nameCheckMessage.classList.add('hidden');
            });
        }, 500);
    });
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        const name = groupNameInput.value.trim();
        
        // Check if name is empty or too short
        if (name.length < 3) {
            e.preventDefault();
            showErrorMessage('Group name must be at least 3 characters long.');
            groupNameInput.focus();
            return false;
        }
        
        // Check if still checking
        if (isChecking) {
            e.preventDefault();
            alert('Please wait while we check if the group name is available...');
            return false;
        }
        
        // Check if name already exists
        if (nameExists) {
            e.preventDefault();
            showErrorMessage('This group name is already taken. Please choose a different name.');
            groupNameInput.focus();
            groupNameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';
        
        // Allow form to submit
        return true;
    });
    
    // Trigger check on blur
    groupNameInput.addEventListener('blur', function() {
        const name = this.value.trim();
        if (name.length >= 3 && nameCheckMessage.classList.contains('hidden')) {
            // Trigger a check
            this.dispatchEvent(new Event('input'));
        }
    });
});
</script>
@endpush