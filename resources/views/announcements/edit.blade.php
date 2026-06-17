<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - UTHM Bulletin Board</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* Moderation styles */
        .form-input.moderation-warning {
            border-color: #dc2626;
            background-color: #fef2f2;
        }

        .form-input.moderation-safe {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .moderation-message {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Checking indicator */
        .checking-indicator {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
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

            <!-- Moderation Global Error Display -->
            @error('moderation')
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-red-600 mt-0.5 mr-3 text-lg"></i>
                        <div>
                            <strong class="text-red-800 font-semibold block mb-1">⚠️ Content Blocked</strong>
                            <p class="text-red-700">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            @enderror

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

                    <form action="{{ route('announcements.update', $announcement) }}" method="POST" id="announcementForm" enctype="multipart/form-data">
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
                                           name="announcement_type"
                                           value="official"
                                           {{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') == 'official' ? 'checked' : '' }}
                                           class="hidden"
                                           onchange="updateFormAction(this)">
                                    <div class="posting-option-card p-5 rounded-xl border-2 {{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') == 'official' ? 'selected border-green-500 bg-green-50' : 'border-gray-200' }}">
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
                                           name="announcement_type"
                                           value="unofficial"
                                           {{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') == 'unofficial' ? 'checked' : '' }}
                                           class="hidden"
                                           onchange="updateFormAction(this)">
                                    <div class="posting-option-card p-5 rounded-xl border-2 {{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') == 'unofficial' ? 'selected border-amber-500 bg-amber-50' : 'border-gray-200' }}">
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
                            <input type="hidden" name="announcement_type" id="announcement_type_hidden" value="{{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') }}">
                        </div>
                        @endif

                        <!-- Title Field with Moderation -->
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
                            <div id="title-moderation-message" class="mt-2 text-sm hidden moderation-message"></div>
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
                                    <option value="academic" {{ old('category', $announcement->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="events" {{ old('category', $announcement->category) == 'events' ? 'selected' : '' }}>Events</option>
                                    <option value="general" {{ old('category', $announcement->category) == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="club" {{ old('category', $announcement->category) == 'club' ? 'selected' : '' }}>Club</option>
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
                                <option value="published" {{ in_array(old('status', $announcement->status), ['published', 'expired']) ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $announcement->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                <option value="pending_verification" {{ old('status', $announcement->status) == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Field (Optional) -->
                        <div class="mb-6">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Cover Image (Optional)
                            </label>
                            
                            <!-- Hidden File Input -->
                            <input type="file" 
                                   id="image" 
                                   name="image"
                                   class="hidden"
                                   accept=".jpg,.jpeg,.png,.gif,.webp">
                            
                            <!-- Image Preview -->
                            <div id="image-preview-container" class="hidden mb-4">
                                <div class="relative rounded-lg overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <img id="image-preview" src="" alt="Image preview" class="w-full h-auto max-h-96 object-cover">
                                    <div class="absolute top-3 right-3 flex gap-2">
                                        <button type="button" 
                                                onclick="removeImage()" 
                                                class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition-colors shadow-lg">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                        <p class="text-white text-sm font-medium">
                                            <i class="fas fa-image mr-2"></i>
                                            <span id="image-filename">Image selected</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Placeholder (when no image selected) -->
                            <div id="image-placeholder-container" 
                                 onclick="document.getElementById('image').click()" 
                                 class="mb-4 rounded-lg overflow-hidden shadow-lg cursor-pointer transition-transform hover:scale-105 @if(isset($announcement->image) && !empty($announcement->image)) hidden @endif" 
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="w-full h-64 md:h-80 flex flex-col items-center justify-center text-center px-6 py-12">
                                    <div class="bg-white/20 rounded-full p-6 mb-4 backdrop-blur-sm">
                                        <i class="fas fa-image text-white text-5xl"></i>
                                    </div>
                                    <h3 class="text-white text-xl font-bold mb-2">Add Cover Image</h3>
                                    <p class="text-white/90 text-sm mb-4">Upload an image that represents your announcement</p>
                                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                        <p class="text-white text-xs font-medium">Click to upload or drag & drop</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Image Display (if exists on load) -->
                            @if(isset($announcement->image) && !empty($announcement->image))
                                <div id="current-image-container" class="mb-4">
                                    <div class="relative rounded-lg overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <img src="{{ asset('storage/' . $announcement->image) }}" alt="Current image" class="w-full h-auto max-h-96 object-cover">
                                        <div class="absolute top-3 right-3 flex gap-2">
                                            <button type="button" 
                                                    onclick="removeCurrentImage()" 
                                                    class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition-colors shadow-lg">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Support Text -->
                            <p class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Supported formats: JPG, JPEG, PNG, GIF, WEBP (Max: 5MB)
                            </p>
                            
                            @error('image')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content Field with Moderation -->
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
                            <div id="content-moderation-message" class="mt-2 text-sm hidden moderation-message"></div>
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
                                <input type="date" 
                                       id="expiry_date" 
                                       name="expiry_date" 
                                       value="{{ old('expiry_date', $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d') : '') }}"
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('expiry_date') border-red-500 @enderror">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Optional — visible through this day; hidden from the main board the next day</p>
                                @if($announcement->status === 'expired')
                                    <p class="mt-2 text-sm text-amber-700">This announcement has expired. Set a future expiry date and publish again to restore it on the main board.</p>
                                @endif
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
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt text-blue-500 mt-1 mr-2 text-xs"></i>
                        <span><strong>Content Moderation:</strong> All announcements are checked for inappropriate content automatically</span>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- JavaScript with Moderation -->
    <script>
        // Moderation variables
        let moderationTimeouts = {};
        let titleInput = document.getElementById('title');
        let contentInput = document.getElementById('content');
        let submitButton = document.getElementById('submitButton');
        
        document.addEventListener('DOMContentLoaded', function() {
            // Set min date for expiry date based on publish date
            const publishDateInput = document.getElementById('publish_date');
            const expiryDateInput = document.getElementById('expiry_date');
            
            if (publishDateInput && publishDateInput.value && expiryDateInput) {
                expiryDateInput.min = publishDateInput.value.split('T')[0] || publishDateInput.value;
            }
            
            if (publishDateInput) {
                publishDateInput.addEventListener('change', function() {
                    if (this.value && expiryDateInput) {
                        expiryDateInput.min = this.value.split('T')[0] || this.value;
                    }
                });
            }
            
            // Add confirmation when changing status to published
            const statusSelect = document.getElementById('status');
            const form = document.getElementById('announcementForm');
            
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
            if (document.querySelector('input[name="announcement_type"]')) {
                updatePostingOptionCards();
            }
            
            // Setup real-time moderation
            setupModeration();
        });
        
        function setupModeration() {
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    clearTimeout(moderationTimeouts.title);
                    moderationTimeouts.title = setTimeout(() => {
                        checkContent(this.value, 'title');
                    }, 800);
                });
                // Initial check
                if (titleInput.value.length > 5) {
                    setTimeout(() => checkContent(titleInput.value, 'title'), 500);
                }
            }
            
            if (contentInput) {
                contentInput.addEventListener('input', function() {
                    clearTimeout(moderationTimeouts.content);
                    moderationTimeouts.content = setTimeout(() => {
                        checkContent(this.value, 'content');
                    }, 800);
                });
                // Initial check
                if (contentInput.value.length > 5) {
                    setTimeout(() => checkContent(contentInput.value, 'content'), 500);
                }
            }
        }
        
        async function checkContent(text, field) {
            if (!text || text.length < 5) {
                removeModerationWarning(field);
                enableSubmitButton();
                return;
            }
            
            showCheckingIndicator(field);
            
            try {
                const response = await fetch(`/api/moderate/test?text=${encodeURIComponent(text)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                hideCheckingIndicator(field);
                
                if (result.flagged) {
                    showModerationWarning(field, result.violations);
                    updateFieldStyle(field, true);
                    disableSubmitButton();
                } else {
                    removeModerationWarning(field);
                    updateFieldStyle(field, false);
                    enableSubmitButton();
                }
                
            } catch (error) {
                console.error('Moderation check failed:', error);
                hideCheckingIndicator(field);
                enableSubmitButton();
            }
        }
        
        function showCheckingIndicator(field) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv) {
                messageDiv.className = 'mt-2 text-sm text-blue-600 flex items-center moderation-message checking-indicator';
                messageDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking content...';
                messageDiv.classList.remove('hidden');
            }
        }
        
        function hideCheckingIndicator(field) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv && messageDiv.innerHTML.includes('Checking')) {
                messageDiv.classList.add('hidden');
            }
        }
        
        function showModerationWarning(field, violations) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv) {
                const violationTypes = violations.map(v => v.classifier).join(', ');
                messageDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 moderation-message';
                messageDiv.innerHTML = `
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                        <div>
                            <strong class="font-semibold">Inappropriate ${field} detected:</strong>
                            <span class="ml-1">Please avoid ${violationTypes} language.</span>
                        </div>
                    </div>
                `;
                messageDiv.classList.remove('hidden');
            }
        }
        
        function removeModerationWarning(field) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv) {
                messageDiv.classList.add('hidden');
            }
        }
        
        function updateFieldStyle(field, isFlagged) {
            const input = document.getElementById(field);
            if (input) {
                if (isFlagged) {
                    input.classList.add('moderation-warning');
                    input.classList.remove('moderation-safe');
                } else if (input.value.length > 5) {
                    input.classList.add('moderation-safe');
                    input.classList.remove('moderation-warning');
                } else {
                    input.classList.remove('moderation-warning', 'moderation-safe');
                }
            }
        }
        
        function disableSubmitButton() {
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.title = 'Please fix inappropriate content before saving';
            }
        }
        
        function enableSubmitButton() {
            const titleWarning = document.getElementById('title-moderation-message');
            const contentWarning = document.getElementById('content-moderation-message');
            
            const titleBlocked = titleWarning && !titleWarning.classList.contains('hidden') && titleWarning.innerHTML.includes('Inappropriate');
            const contentBlocked = contentWarning && !contentWarning.classList.contains('hidden') && contentWarning.innerHTML.includes('Inappropriate');
            
            if (!titleBlocked && !contentBlocked && submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.title = '';
            }
        }
        
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        function updateFormAction(radio) {
            const announcementType = radio.value;
            
            const hiddenField = document.getElementById('announcement_type_hidden');
            if (hiddenField) {
                hiddenField.value = announcementType;
            }
            
            const submitButtonText = document.getElementById('submitButtonText');
            
            if (announcementType === 'official') {
                submitButtonText.textContent = 'Update & Move to Official Board';
            } else {
                submitButtonText.textContent = 'Update & Move to Unofficial Page';
            }
            
            updatePostingOptionCards();
        }
        
        function updatePostingOptionCards() {
            const officialRadio = document.querySelector('input[name="announcement_type"][value="official"]');
            const unofficialRadio = document.querySelector('input[name="announcement_type"][value="unofficial"]');
            
            if (!officialRadio || !unofficialRadio) return;
            
            const officialCard = officialRadio.closest('label').querySelector('.posting-option-card');
            const unofficialCard = unofficialRadio.closest('label').querySelector('.posting-option-card');
            
            officialCard.classList.remove('selected', 'border-green-500', 'bg-green-50', 'border-amber-500', 'bg-amber-50');
            unofficialCard.classList.remove('selected', 'border-green-500', 'bg-green-50', 'border-amber-500', 'bg-amber-50');
            
            if (officialRadio.checked) {
                officialCard.classList.add('selected', 'border-green-500', 'bg-green-50');
                unofficialCard.classList.add('border-gray-200');
            } else {
                unofficialCard.classList.add('selected', 'border-amber-500', 'bg-amber-50');
                officialCard.classList.add('border-gray-200');
            }
        }
        
        // Image upload handler
        const imageInput = document.getElementById('image');
        const imagePlaceholder = document.getElementById('image-placeholder-container');
        
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                handleImageUpload(this.files[0]);
            });
            
            if (imagePlaceholder) {
                imagePlaceholder.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.add('opacity-75');
                });
                
                imagePlaceholder.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('opacity-75');
                });
                
                imagePlaceholder.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('opacity-75');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        imageInput.files = files;
                        handleImageUpload(files[0]);
                    }
                });
            }
        }
        
        function handleImageUpload(file) {
            if (!file) return;
            
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Image size exceeds 5MB limit. Please choose a smaller image.');
                document.getElementById('image').value = '';
                document.getElementById('image-preview-container').classList.add('hidden');
                document.getElementById('image-placeholder-container').classList.remove('hidden');
                return;
            }
            
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Invalid image format. Please use JPG, PNG, GIF, or WEBP.');
                document.getElementById('image').value = '';
                document.getElementById('image-preview-container').classList.add('hidden');
                document.getElementById('image-placeholder-container').classList.remove('hidden');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-filename').textContent = file.name;
                document.getElementById('image-preview-container').classList.remove('hidden');
                document.getElementById('image-placeholder-container').classList.add('hidden');
                
                const currentImageContainer = document.getElementById('current-image-container');
                if (currentImageContainer) {
                    currentImageContainer.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
        
        window.removeImage = function() {
            document.getElementById('image').value = '';
            document.getElementById('image-preview-container').classList.add('hidden');
            document.getElementById('image-placeholder-container').classList.remove('hidden');
            document.getElementById('image-preview').src = '';
            
            const currentImageContainer = document.getElementById('current-image-container');
            if (currentImageContainer) {
                currentImageContainer.classList.remove('hidden');
            }
        };
        
        window.removeCurrentImage = function() {
            const form = document.getElementById('announcementForm');
            
            let removeImageInput = document.getElementById('remove_image_input');
            if (!removeImageInput) {
                removeImageInput = document.createElement('input');
                removeImageInput.type = 'hidden';
                removeImageInput.id = 'remove_image_input';
                removeImageInput.name = 'remove_image';
                removeImageInput.value = '1';
                form.appendChild(removeImageInput);
            }
            
            const currentImageContainer = document.getElementById('current-image-container');
            if (currentImageContainer) {
                currentImageContainer.classList.add('hidden');
            }
            document.getElementById('image-placeholder-container').classList.remove('hidden');
            document.getElementById('image-preview-container').classList.add('hidden');
            document.getElementById('image').value = '';
        };
    </script>
</body>
</html>