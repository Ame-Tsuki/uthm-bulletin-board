<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement - UTHM Bulletin Board</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Create New Announcement</h1>
                <p class="mt-2 text-gray-600">Share updates with students and staff - choose where to post</p>
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

            <!-- Create Form -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('announcements.store') }}" method="POST" id="announcementForm">
                        @csrf

                        <!-- Posting Destination -->
                        <div class="mb-8 p-6 bg-gray-50 rounded-xl">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Where would you like to post?</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Official Announcement Option -->
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="is_official" 
                                           value="1" 
                                           {{ old('is_official', isset($defaultIsOfficial) ? $defaultIsOfficial : '1') == '1' ? 'checked' : '' }}
                                           class="hidden"
                                           onchange="updateFormAction(this)">
                                    <div class="posting-option-card p-5 rounded-xl border-2 {{ old('is_official', isset($defaultIsOfficial) ? $defaultIsOfficial : '1') == '1' ? 'selected border-green-500 bg-green-50' : 'border-gray-200' }}">
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
<!-- Announcement Type -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Announcement Type
    </label>
    <div class="flex items-center space-x-4">
        <div class="flex items-center">
            <input type="radio" id="official_type" name="is_official" value="1" 
                   {{ (old('is_official', $isOfficial ?? true)) ? 'checked' : '' }} 
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                   {{ ($userRole !== 'admin' && $userRole !== 'staff') ? 'disabled' : '' }}>
            <label for="official_type" class="ml-2 block text-sm text-gray-900 flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                Official Announcement
                <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Verified by Admin</span>
            </label>
        </div>
        <div class="flex items-center">
            <input type="radio" id="unofficial_type" name="is_official" value="0"
                   {{ !(old('is_official', $isOfficial ?? true)) ? 'checked' : '' }}
                   class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
            <label for="unofficial_type" class="ml-2 block text-sm text-gray-900 flex items-center">
                <i class="fas fa-users text-amber-500 mr-1"></i>
                Unofficial Announcement
                <span class="ml-2 px-2 py-1 text-xs bg-amber-100 text-amber-800 rounded">Community Post</span>
            </label>
        </div>
    </div>
    <p class="mt-2 text-sm text-gray-500">
        @if($userRole === 'admin' || $userRole === 'staff')
            You can create both official and unofficial announcements. Official announcements appear on the official page.
        @else
            As a {{ $userRole }}, you can only create unofficial community announcements.
        @endif
    </p>
    @error('is_official')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Announcement Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter announcement title"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Make it clear and descriptive</p>
                        </div>

                        <!-- Category and Priority -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select id="category" 
                                        name="category" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                    <option value="">Select Category</option>
                                    <option value="urgent" {{ old('category') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="events" {{ old('category') == 'events' ? 'selected' : '' }}>Events</option>
                                    <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="important" {{ old('category') == 'important' ? 'selected' : '' }}>Important</option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Priority
                                </label>
                                <select id="priority" 
                                        name="priority" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="important" {{ old('priority') == 'important' ? 'selected' : '' }}>Important</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="mb-6">
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                Department/Office
                            </label>
                            <input type="text" 
                                   id="department" 
                                   name="department" 
                                   value="{{ old('department') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., IT Department, Academic Affairs Office">
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea id="content" 
                                      name="content" 
                                      rows="10"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Enter announcement details..."
                                      required>{{ old('content') }}</textarea>
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
                                       value="{{ old('publish_date') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                                       value="{{ old('expiry_date') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Optional - when this announcement should expire</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                            <div>
                                <a href="{{ route('announcements.index') }}" 
                                   class="inline-flex items-center px-5 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Cancel
                                </a>
                            </div>
                            <div class="flex gap-4">
                                <button type="button" 
                                        onclick="resetForm()"
                                        class="inline-flex items-center px-5 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-redo mr-2"></i>
                                    Reset
                                </button>
                                <button type="submit" 
                                        id="submitButton"
                                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    <span id="submitButtonText">
                                        {{ old('is_official', isset($defaultIsOfficial) ? $defaultIsOfficial : '1') == '1' ? 'Publish to Official Board' : 'Post to Unofficial Page' }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="font-medium text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i> Tips for Effective Announcements
                </h3>
                <ul class="text-blue-700 text-sm space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span><strong>Official Announcements:</strong> University policies, official notices, verified information</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span><strong>Unofficial Announcements:</strong> Student club activities, informal notices, department updates</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span>Use clear and concise titles that summarize the announcement</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span>Include all relevant details: dates, times, locations, contacts</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span>Set expiry dates for time-sensitive announcements</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set min date for publish date to today
            const today = new Date();
            const todayString = today.toISOString().slice(0, 16);
            const publishDateInput = document.getElementById('publish_date');
            const expiryDateInput = document.getElementById('expiry_date');
            
            publishDateInput.min = todayString;
            
            // Set min date for expiry date based on publish date
            publishDateInput.addEventListener('change', function() {
                if (this.value) {
                    expiryDateInput.min = this.value;
                }
            });
            
            // Set expiry date min if publish date already has value
            if (publishDateInput.value) {
                expiryDateInput.min = publishDateInput.value;
            }
            
            // Initialize posting option cards based on current selection
            updatePostingOptionCards();
            
            // Initialize form action based on default selection
            const defaultRadio = document.querySelector('input[name="is_official"]:checked');
            if (defaultRadio) {
                updateFormAction(defaultRadio);
            }
        });
        
        function updateFormAction(radio) {
            const postingType = radio.value === '1' ? 'official' : 'unofficial';
            document.getElementById('posting_type').value = postingType;
            
            // Update button text
            const submitButtonText = document.getElementById('submitButtonText');
            const submitButton = document.getElementById('submitButton');
            
            if (radio.value === '1') {
                submitButtonText.textContent = 'Publish to Official Board';
                submitButton.className = 'inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow';
            } else {
                submitButtonText.textContent = 'Post to Unofficial Page';
                submitButton.className = 'inline-flex items-center px-6 py-3 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 transition-colors shadow';
            }
            
            // Update card styling
            updatePostingOptionCards();
        }
        
        function updatePostingOptionCards() {
            // Get all radio buttons
            const officialRadio = document.querySelector('input[name="is_official"][value="1"]');
            const unofficialRadio = document.querySelector('input[name="is_official"][value="0"]');
            
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
        
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                document.querySelector('form').reset();
                
                // Reset radio buttons to default (Official)
                const officialRadio = document.querySelector('input[name="is_official"][value="1"]');
                officialRadio.checked = true;
                
                // Update UI
                updateFormAction(officialRadio);
            }
        }
    </script>
</body>
</html>