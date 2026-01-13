<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .badge-urgent { background-color: #fee2e2; color: #dc2626; }
        .badge-academic { background-color: #dbeafe; color: #1d4ed8; }
        .badge-events { background-color: #f3e8ff; color: #7c3aed; }
        .badge-general { background-color: #f0f9ff; color: #0369a1; }
        .badge-important { background-color: #fef3c7; color: #d97706; }
        .badge-official { background-color: #dcfce7; color: #166534; }
        .badge-unofficial { background-color: #fef3c7; color: #92400e; }
        
        /* Radio card styles */
        .posting-option-card {
            transition: all 0.2s ease;
            border: 2px solid #e5e7eb;
        }
        .posting-option-card:hover {
            border-color: #3b82f6;
        }
        .posting-option-card.selected {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        .posting-option-card input[type="radio"]:checked + div {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Simple Header -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('announcements.index') }}" class="flex items-center">
                        <i class="fas fa-arrow-left text-gray-600 mr-2"></i>
                        <span class="text-gray-700">Back to Announcements</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-4">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Announcement</h1>
                <p class="mt-2 text-gray-600">Update announcement details</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-red-900">Please fix the following errors:</h4>
                            <ul class="mt-2 text-red-700 text-sm">
                                @foreach($errors->all() as $error)
                                    <li class="flex items-center mt-1">
                                        <i class="fas fa-circle text-xs mr-2"></i>{{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Edit Form -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-6">
                    <!-- Info Banner -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i> 
                            <span><strong>Created:</strong> {{ $announcement->created_at->format('F d, Y H:i') }}
                            @if($announcement->user)
                                by {{ $announcement->user->name }}
                            @endif</span>
                        </div>
                        @if($hasOfficialColumn ?? false)
                        <div class="mt-2 text-blue-800">
                            <span><strong>Current Type:</strong> 
                                @if($announcement->is_official)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">
                                        <i class="fas fa-check-circle mr-1"></i> Official Announcement
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800 ml-2">
                                        <i class="fas fa-bullhorn mr-1"></i> Unofficial Announcement
                                    </span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>

                    <form action="{{ route('announcements.update', $announcement) }}" method="POST" id="announcementForm">
                        @csrf
                        @method('PUT')

                        <!-- Posting Destination (Only show if column exists) -->
                        @if($hasOfficialColumn ?? false)
                        <div class="mb-8 p-6 bg-gray-50 rounded-xl">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Where should this announcement appear?</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Official Announcement Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="is_official" 
                                           value="1" 
                                           {{ old('is_official', $announcement->is_official ? '1' : '0') == '1' ? 'checked' : '' }}
                                           class="hidden"
                                           onchange="updateFormAction(this)">
                                    <div class="posting-option-card p-5 rounded-xl border-2 {{ old('is_official', $announcement->is_official ? '1' : '0') == '1' ? 'selected border-green-500 bg-green-50' : 'border-gray-200' }}">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="font-semibold text-gray-900">Official Announcement</h4>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Verified announcements from university administration. 
                                                    Will appear on the main bulletin board.
                                                </p>
                                                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-globe mr-1"></i> Main Bulletin Board
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Unofficial Announcement Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="is_official" 
                                           value="0" 
                                           {{ old('is_official', $announcement->is_official ? '1' : '0') == '0' ? 'checked' : '' }}
                                           class="hidden"
                                           onchange="updateFormAction(this)">
                                    <div class="posting-option-card p-5 rounded-xl border-2 {{ old('is_official', $announcement->is_official ? '1' : '0') == '0' ? 'selected border-amber-500 bg-amber-50' : 'border-gray-200' }}">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                                                    <i class="fas fa-bullhorn text-amber-600 text-xl"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="font-semibold text-gray-900">Unofficial Announcement</h4>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Informal updates, student notices, or department news. 
                                                    Will appear on the unofficial announcements page.
                                                </p>
                                                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    <i class="fas fa-users mr-1"></i> Unofficial Page
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Hidden field to store the posting type for form submission -->
                            <input type="hidden" name="posting_type" id="posting_type" value="{{ old('is_official', $announcement->is_official ? '1' : '0') == '1' ? 'official' : 'unofficial' }}">
                        </div>
                        @endif

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Announcement Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $announcement->title) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                                   placeholder="Enter announcement title"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Make it clear and descriptive</p>
                        </div>

                        <!-- Category, Priority, and Department -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select id="category" 
                                        name="category" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror"
                                        required>
                                    <option value="">Select Category</option>
                                    <option value="urgent" {{ old('category', $announcement->category) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="academic" {{ old('category', $announcement->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="events" {{ old('category', $announcement->category) == 'events' ? 'selected' : '' }}>Events</option>
                                    <option value="general" {{ old('category', $announcement->category) == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="important" {{ old('category', $announcement->category) == 'important' ? 'selected' : '' }}>Important</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Priority
                                </label>
                                <select id="priority" 
                                        name="priority" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror">
                                    <option value="normal" {{ old('priority', $announcement->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="important" {{ old('priority', $announcement->priority) == 'important' ? 'selected' : '' }}>Important</option>
                                    <option value="urgent" {{ old('priority', $announcement->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                    Department/Office
                                </label>
                                <input type="text" 
                                       id="department" 
                                       name="department" 
                                       value="{{ old('department', $announcement->department) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('department') border-red-500 @enderror"
                                       placeholder="e.g., IT Department, Academic Affairs Office">
                                @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status', $announcement->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $announcement->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $announcement->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea id="content" 
                                      name="content" 
                                      rows="10"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                                      placeholder="Enter announcement details..."
                                      required>{{ old('content', $announcement->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">You can use basic HTML formatting if needed</p>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <!-- Publish Date -->
                            <div>
                                <label for="publish_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Publish Date
                                </label>
                                <input type="datetime-local" 
                                       id="publish_date" 
                                       name="publish_date" 
                                       value="{{ old('publish_date', $announcement->publish_date ? $announcement->publish_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('publish_date') border-red-500 @enderror">
                                @error('publish_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Leave empty to publish immediately</p>
                            </div>

                            <!-- Expiry Date -->
                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Expiry Date
                                </label>
                                <input type="datetime-local" 
                                       id="expiry_date" 
                                       name="expiry_date" 
                                       value="{{ old('expiry_date', $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('expiry_date') border-red-500 @enderror">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Optional - when this announcement should expire</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                            <div class="flex gap-4">
                                <button type="button" 
                                        onclick="confirmDelete()"
                                        class="inline-flex items-center px-5 py-3 border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete
                                </button>
                                <a href="{{ route('announcements.show', $announcement) }}" 
                                   class="inline-flex items-center px-5 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>
                                    Preview
                                </a>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{ route('announcements.index') }}" 
                                   class="inline-flex items-center px-5 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" 
                                        id="submitButton"
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow">
                                    <i class="fas fa-save mr-2"></i>
                                    <span id="submitButtonText">
                                        Update Announcement
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form id="delete-form" action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Tips -->
            @if($hasOfficialColumn ?? false)
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="font-medium text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i> Announcement Type Guidelines
                </h3>
                <ul class="text-blue-700 text-sm space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span><strong>Official Announcements:</strong> University policies, official notices, verified information, academic calendar changes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span><strong>Unofficial Announcements:</strong> Student club activities, informal notices, department updates, social events</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-exclamation-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span><strong>Note:</strong> Changing the announcement type will move it to the appropriate page</span>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set min date for expiry date based on publish date
            const publishDateInput = document.getElementById('publish_date');
            const expiryDateInput = document.getElementById('expiry_date');
            
            if (publishDateInput && publishDateInput.value) {
                expiryDateInput.min = publishDateInput.value;
            }
            
            if (publishDateInput) {
                publishDateInput.addEventListener('change', function() {
                    if (this.value && expiryDateInput) {
                        expiryDateInput.min = this.value;
                    }
                });
            }
            
            // Add confirmation when changing status to published
            const statusSelect = document.getElementById('status');
            const form = document.querySelector('form[method="POST"]');
            
            if (form && statusSelect) {
                form.addEventListener('submit', function(e) {
                    const currentStatus = '{{ $announcement->status ?? 'draft' }}';
                    if (statusSelect.value === 'published' && currentStatus !== 'published') {
                        if (!confirm('Are you sure you want to publish this announcement? It will be visible to users.')) {
                            e.preventDefault();
                        }
                    }
                });
            }
            
            // Initialize posting option cards if they exist
            if (document.querySelector('input[name="is_official"]')) {
                updatePostingOptionCards();
            }
        });
        
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        function updateFormAction(radio) {
            const postingType = radio.value === '1' ? 'official' : 'unofficial';
            document.getElementById('posting_type').value = postingType;
            
            // Update button text
            const submitButtonText = document.getElementById('submitButtonText');
            
            if (radio.value === '1') {
                submitButtonText.textContent = 'Update & Move to Official Board';
            } else {
                submitButtonText.textContent = 'Update & Move to Unofficial Page';
            }
            
            // Update card styling
            updatePostingOptionCards();
        }
        
        function updatePostingOptionCards() {
            // Get all radio buttons
            const officialRadio = document.querySelector('input[name="is_official"][value="1"]');
            const unofficialRadio = document.querySelector('input[name="is_official"][value="0"]');
            
            if (!officialRadio || !unofficialRadio) return;
            
            // Get card containers
            const officialCard = officialRadio.closest('label').querySelector('.posting-option-card');
            const unofficialCard = unofficialRadio.closest('label').querySelector('.posting-option-card');
            
            // Reset all cards
            officialCard.classList.remove('selected', 'border-green-500', 'bg-green-50', 'border-amber-500', 'bg-amber-50');
            unofficialCard.classList.remove('selected', 'border-green-500', 'bg-green-50', 'border-amber-500', 'bg-amber-50');
            
            // Apply appropriate styling based on selection
            if (officialRadio.checked) {
                officialCard.classList.add('selected', 'border-green-500', 'bg-green-50');
                unofficialCard.classList.add('border-gray-200');
            } else {
                unofficialCard.classList.add('selected', 'border-amber-500', 'bg-amber-50');
                officialCard.classList.add('border-gray-200');
            }
        }
    </script>
</body>
</html>