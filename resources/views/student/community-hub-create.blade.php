@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Group</h1>
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <form action="{{ route('student.community-hub.store') }}" method="POST" id="createGroupForm">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Group Name *</label>
                <div class="relative">
                    <input type="text" id="groupName" name="name" value="{{ old('name') }}" required class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg p-2 focus:outline-none @error('name') focus:border-red-500 @else focus:border-uthm-blue @enderror" placeholder="Enter group name">
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
                <textarea name="description" rows="4" required class="w-full border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg p-2 focus:outline-none @error('description') focus:border-red-500 @else focus:border-uthm-blue @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Category *</label>
                <select name="category" required class="w-full border @error('category') border-red-500 @else border-gray-300 @enderror rounded-lg p-2">
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
                <select name="privacy" required class="w-full border @error('privacy') border-red-500 @else border-gray-300 @enderror rounded-lg p-2">
                    <option value="public" {{ old('privacy') == 'public' ? 'selected' : '' }}>Public - Anyone can join</option>
                    <option value="by_approval" {{ old('privacy') == 'by_approval' ? 'selected' : '' }}>By Approval - Request to join</option>
                    <option value="private" {{ old('privacy') == 'private' ? 'selected' : '' }}>Private - Invite only</option>
                </select>
                @error('privacy')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Max Members (Optional)</label>
                <input type="number" name="max_members" value="{{ old('max_members') }}" class="w-full border @error('max_members') border-red-500 @else border-gray-300 @enderror rounded-lg p-2">
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

    // Check group name when user types
    groupNameInput.addEventListener('input', function() {
        const name = this.value.trim();
        
        clearTimeout(checkTimeout);
        
        if (name.length < 3) {
            nameCheckMessage.classList.add('hidden');
            nameCheckIndicator.classList.add('hidden');
            nameExists = false;
            // Reset border style
            this.classList.remove('border-red-500', 'border-green-500');
            this.classList.add('border-gray-300');
            return;
        }
        
        // Show loading indicator
        nameCheckIndicator.classList.remove('hidden');
        isChecking = true;
        
        // Debounce the check by 500ms
        checkTimeout = setTimeout(() => {
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="_token"]').value;
            
            fetch('{{ url("/student/community-hub/check-group-name") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name })
            })
            .then(response => response.json())
            .then(data => {
                nameCheckIndicator.classList.add('hidden');
                isChecking = false;
                nameCheckMessage.classList.remove('hidden');
                
                if (data.exists) {
                    nameExists = true;
                    nameCheckMessage.innerHTML = `
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-red-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-red-700 font-medium">Group name already exists</p>
                                <p class="text-red-600 text-sm mt-1">${data.message || 'Please choose a different group name.'}</p>
                            </div>
                        </div>
                    `;
                    nameCheckMessage.className = 'mt-2 p-3 rounded-lg bg-red-50 border border-red-200';
                    groupNameInput.classList.remove('border-gray-300', 'border-green-500');
                    groupNameInput.classList.add('border-red-500');
                } else {
                    nameExists = false;
                    nameCheckMessage.innerHTML = `
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-green-700 font-medium">Group name is available</p>
                                <p class="text-green-600 text-sm mt-1">${data.message || 'You can use this group name.'}</p>
                            </div>
                        </div>
                    `;
                    nameCheckMessage.className = 'mt-2 p-3 rounded-lg bg-green-50 border border-green-200';
                    groupNameInput.classList.remove('border-red-500', 'border-gray-300');
                    groupNameInput.classList.add('border-green-500');
                }
            })
            .catch(error => {
                console.error('Error checking group name:', error);
                nameCheckIndicator.classList.add('hidden');
                isChecking = false;
                nameCheckMessage.classList.remove('hidden');
                nameCheckMessage.innerHTML = `
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-yellow-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-yellow-700 font-medium">Unable to check group name</p>
                            <p class="text-yellow-600 text-sm mt-1">Please try again or continue with submission.</p>
                        </div>
                    </div>
                `;
                nameCheckMessage.className = 'mt-2 p-3 rounded-lg bg-yellow-50 border border-yellow-200';
            });
        }, 500);
    });
    
    // Real-time validation on blur
    groupNameInput.addEventListener('blur', function() {
        const name = this.value.trim();
        if (name.length >= 3 && !isChecking && nameExists === false && nameCheckMessage.classList.contains('hidden')) {
            // Trigger check if not already done
            groupNameInput.dispatchEvent(new Event('input'));
        }
    });
    
    // Prevent form submission if name exists or still checking
    form.addEventListener('submit', function(e) {
        const name = groupNameInput.value.trim();
        
        if (name.length < 3) {
            e.preventDefault();
            nameCheckMessage.classList.remove('hidden');
            nameCheckMessage.innerHTML = `
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-red-700 font-medium">Group name is required</p>
                        <p class="text-red-600 text-sm mt-1">Please enter a group name with at least 3 characters.</p>
                    </div>
                </div>
            `;
            nameCheckMessage.className = 'mt-2 p-3 rounded-lg bg-red-50 border border-red-200';
            groupNameInput.focus();
            return false;
        }
        
        if (isChecking) {
            e.preventDefault();
            alert('Please wait while we check if the group name is available...');
            return false;
        }
        
        if (nameExists) {
            e.preventDefault();
            nameCheckMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            groupNameInput.focus();
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';
    });
});
</script>
@endsection