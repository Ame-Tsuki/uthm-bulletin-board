<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom colors */
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .bg-uthm-blue-light { background-color: #e6f0fa; }
        
        /* Form styles */
        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #0056a6;
            ring: 2px;
            ring-color: rgba(0, 86, 166, 0.2);
        }
        
        .form-error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        /* Radio button styles */
        .radio-option {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .radio-option:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }
        
        .radio-option.selected {
            border-color: #0056a6;
            background-color: #e6f0fa;
        }
        
        .radio-input {
            margin-right: 1rem;
            transform: scale(1.2);
        }
        
        .radio-content {
            flex: 1;
        }
        
        .radio-title {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        
        .radio-description {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        /* Badge styles */
        .badge-admin {
            background-color: #dc2626;
            color: white;
        }
        .badge-staff {
            background-color: #2563eb;
            color: white;
        }
        .badge-student {
            background-color: #059669;
            color: white;
        }
        .badge-guest {
            background-color: #6b7280;
            color: white;
        }
        
        /* Verification info styles */
        .verification-info {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
        
        .verification-official {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
        }
        
        .verification-unofficial {
            background-color: #fefce8;
            border: 1px solid #fef08a;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Simple Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('announcements.index') }}" class="flex items-center mr-8">
                        <i class="fas fa-arrow-left text-gray-600 mr-2"></i>
                        <span class="text-gray-700">Back to Announcements</span>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Create New Announcement</h1>
                </div>
                
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-4">
                        Welcome, {{ $user?->name ?? 'User' }}
                    </span>
                    <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center">
                        <span class="font-bold uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-uthm-blue-light border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="bg-uthm-blue text-white p-3 rounded-lg mr-4">
                            <i class="fas fa-bullhorn text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Create a New Announcement</h2>
                            <p class="mt-2 text-gray-600">
                                Share information, updates, or events with the UTHM community.
                            </p>
                            
                            <!-- User role info -->
                            @if($user)
                                <div class="mt-3 flex items-center">
                                    <span class="text-sm text-gray-500">Your role:</span>
                                    <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <span class="ml-4 text-sm text-gray-500">
                                        @if(in_array($user->role, ['admin', 'staff']))
                                            You can create official announcements without verification.
                                        @else
                                            Official announcements require admin/staff verification.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white rounded-xl shadow p-6">
                <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" id="announcement-form">
                    @csrf
                    
                    <!-- Announcement Type Selection -->
                    <div class="mb-8">
                        <label class="form-label mb-4">
                            Announcement Type <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- Official Option -->
                        <div class="radio-option" id="official-option" onclick="selectType('official')">
                            <input type="radio" 
                                   id="type_official" 
                                   name="announcement_type" 
                                   value="official"
                                   class="radio-input"
                                   {{ old('announcement_type', 'unofficial') == 'official' ? 'checked' : '' }}
                                   required>
                            <div class="radio-content">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="radio-title flex items-center">
                                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                            Official Announcement
                                        </div>
                                        <div class="radio-description">
                                            For important university announcements, policy changes, or official communications
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-shield-alt mr-1"></i> Verified
                                    </span>
                                </div>
                                
                                <!-- Verification Requirement Info -->
                                <div id="official-info" class="verification-info verification-official mt-3" 
                                     style="{{ old('announcement_type', 'unofficial') == 'official' ? 'display: block;' : 'display: none;' }}">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 mb-1">
                                                @if(in_array($user?->role, ['admin', 'staff']))
                                                    ✅ As {{ $user->role }}, your official announcement will be published immediately.
                                                @else
                                                    ⏳ Official announcements require verification from admin/staff.
                                                    <br>
                                                    <span class="text-sm text-gray-600">
                                                        Your announcement will be reviewed before being published to all users.
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Unofficial Option -->
                        <div class="radio-option" id="unofficial-option" onclick="selectType('unofficial')">
                            <input type="radio" 
                                   id="type_unofficial" 
                                   name="announcement_type" 
                                   value="unofficial"
                                   class="radio-input"
                                   {{ old('announcement_type', 'unofficial') == 'unofficial' ? 'checked' : 'selected' }}
                                   required>
                            <div class="radio-content">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="radio-title flex items-center">
                                            <i class="fas fa-users text-blue-600 mr-2"></i>
                                            Unofficial Announcement
                                        </div>
                                        <div class="radio-description">
                                            For club activities, personal notices, informal updates, or community events
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-user-friends mr-1"></i> Community
                                    </span>
                                </div>
                                
                                <!-- Immediate Posting Info -->
                                <div id="unofficial-info" class="verification-info verification-unofficial mt-3"
                                     style="{{ old('announcement_type', 'unofficial') == 'unofficial' ? 'display: block;' : 'display: none;' }}">
                                    <div class="flex items-start">
                                        <i class="fas fa-bolt text-yellow-500 mr-2 mt-0.5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 mb-1">
                                                ⚡ Unofficial announcements are published immediately.
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Your announcement will be visible to all users right after publishing.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @error('announcement_type')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title Field -->
                    <div class="mb-6">
                        <label for="title" class="form-label">
                            Announcement Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="form-input"
                               placeholder="Enter a clear and descriptive title"
                               required>
                        @error('title')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content Field -->
                    <div class="mb-6">
                        <label for="content" class="form-label">
                            Announcement Content <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="8"
                                  class="form-input"
                                  placeholder="Provide detailed information about your announcement..."
                                  required>{{ old('content') }}</textarea>
                        <div class="flex justify-between mt-2">
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-lightbulb mr-1"></i>
                                Be clear and concise. Include all necessary details.
                            </p>
                            <p id="char-counter" class="text-sm text-gray-500">0 characters</p>
                        </div>
                        @error('content')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category Field -->
                    <div class="mb-6">
                        <label for="category" class="form-label">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" class="form-input" required>
                            <option value="" disabled selected>Select a category</option>
                            <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="events" {{ old('category') == 'events' ? 'selected' : '' }}>Events</option>
                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="important" {{ old('category') == 'important' ? 'selected' : '' }}>Important</option>
                            <option value="urgent" {{ old('category') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('category')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority Field -->
                    <div class="mb-6">
                        <label for="priority" class="form-label">
                            Priority Level
                        </label>
                        <select id="priority" name="priority" class="form-input">
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }} selected>Normal</option>
                            <option value="important" {{ old('priority') == 'important' ? 'selected' : '' }}>Important</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Field (Optional) -->
                    <div class="mb-6">
                        <label for="image" class="form-label">
                            Featured Image (Optional)
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
                             class="mb-4 rounded-lg overflow-hidden shadow-lg cursor-pointer transition-transform hover:scale-105" 
                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="w-full h-64 md:h-80 flex flex-col items-center justify-center text-center px-6 py-12">
                                <div class="bg-white/20 rounded-full p-6 mb-4 backdrop-blur-sm">
                                    <i class="fas fa-image text-white text-5xl"></i>
                                </div>
                                <h3 class="text-white text-xl font-bold mb-2">Add Featured Image</h3>
                                <p class="text-white/90 text-sm mb-4">Upload an image that represents your announcement</p>
                                <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                    <p class="text-white text-xs font-medium">Click to upload or drag & drop</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Support Text -->
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Supported formats: JPG, JPEG, PNG, GIF, WEBP (Max: 5MB)
                        </p>
                        
                        @error('image')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Important Information
                        </h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• All announcements will be visible to the UTHM community</li>
                            <li>• Official announcements may require verification before publishing</li>
                            <li>• Unofficial announcements are published immediately</li>
                            <li>• Be respectful and follow community guidelines</li>
                            <li>• Double-check information before publishing</li>
                        </ul>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('announcements.index') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        
                        <!-- Save as Draft Button -->
                        <button type="submit" 
                                name="status" 
                                value="draft"
                                class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>
                        
                        <!-- Publish Button -->
                        <button type="submit" 
                                name="status" 
                                value="published"
                                class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors"
                                id="publish-button">
                            <i class="fas fa-paper-plane mr-2"></i>
                            <span id="publish-text">Publish Announcement</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Type Comparison Table -->
            <div class="mt-8 bg-white rounded-xl shadow p-6">
                <h3 class="font-bold text-gray-900 text-lg mb-4">
                    <i class="fas fa-balance-scale text-gray-600 mr-2"></i>
                    Official vs Unofficial Announcements
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Feature
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Official Announcement
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Unofficial Announcement
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Purpose
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    University policy, official notices, important updates
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    Club activities, personal notices, informal updates
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Verification
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    Requires admin/staff verification (except for admin/staff users)
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    Published immediately, no verification required
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Visibility Badge
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> Official
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                        <i class="fas fa-users mr-1"></i> Unofficial
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Best For
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    Admin, staff, or verified official communications
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    Students, clubs, community members, informal updates
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}</p>
                <p class="mt-1">Choose announcement type based on your needs. Official announcements may require verification.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('announcement-form');
            const titleInput = document.getElementById('title');
            const contentInput = document.getElementById('content');
            const publishButton = document.getElementById('publish-button');
            const publishText = document.getElementById('publish-text');
            
            // Initialize type selection
            initializeTypeSelection();
            
            // Character counter for content
            if (contentInput) {
                contentInput.addEventListener('input', function() {
                    const charCount = this.value.length;
                    const counter = document.getElementById('char-counter');
                    counter.textContent = `${charCount} characters`;
                    
                    if (charCount > 5000) {
                        counter.classList.add('text-red-500');
                    } else {
                        counter.classList.remove('text-red-500');
                    }
                });
                
                // Initialize counter
                contentInput.dispatchEvent(new Event('input'));
            }
            
            // Basic form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear previous error styles
                document.querySelectorAll('.form-input').forEach(input => {
                    input.classList.remove('border-red-500');
                });
                
                // Validate title
                if (!titleInput.value.trim()) {
                    titleInput.classList.add('border-red-500');
                    isValid = false;
                }
                
                // Validate content
                if (!contentInput.value.trim()) {
                    contentInput.classList.add('border-red-500');
                    isValid = false;
                }
                
                // Validate announcement type
                const announcementType = document.querySelector('input[name="announcement_type"]:checked');
                if (!announcementType) {
                    alert('Please select an announcement type (Official or Unofficial).');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields marked with *.');
                } else {
                    // Show confirmation for official announcements from non-admin/staff
                    const isOfficial = announcementType.value === 'official';
                    const userRole = "{{ $user?->role ?? 'guest' }}";
                    const isAdminOrStaff = ['admin', 'staff'].includes(userRole);
                    
                    if (isOfficial && !isAdminOrStaff) {
                        if (!confirm('Official announcements require admin/staff verification.\n\nYour announcement will be submitted for review and published after verification.\n\nDo you want to continue?')) {
                            e.preventDefault();
                        }
                    }
                }
            });
            
            // Image upload handler
            const imageInput = document.getElementById('image');
            const imagePlaceholder = document.getElementById('image-placeholder-container');
            
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    handleImageUpload(this.files[0]);
                });
                
                // Drag and drop support
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
            
            function handleImageUpload(file) {
                if (!file) return;
                
                // File size validation
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (file.size > maxSize) {
                    alert('Image size exceeds 5MB limit. Please choose a smaller image.');
                    document.getElementById('image').value = '';
                    document.getElementById('image-preview-container').classList.add('hidden');
                    document.getElementById('image-placeholder-container').classList.remove('hidden');
                    return;
                }
                
                // File type validation
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Invalid image format. Please use JPG, PNG, GIF, or WEBP.');
                    document.getElementById('image').value = '';
                    document.getElementById('image-preview-container').classList.add('hidden');
                    document.getElementById('image-placeholder-container').classList.remove('hidden');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('image-filename').textContent = file.name;
                    document.getElementById('image-preview-container').classList.remove('hidden');
                    document.getElementById('image-placeholder-container').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
            
            window.removeImage = function() {
                document.getElementById('image').value = '';
                document.getElementById('image-preview-container').classList.add('hidden');
                document.getElementById('image-placeholder-container').classList.remove('hidden');
                document.getElementById('image-preview').src = '';
            };
            
            function initializeTypeSelection() {
                // Set initial selection
                const initialType = "{{ old('announcement_type', 'unofficial') }}";
                selectType(initialType);
                
                // Update publish button text based on type
                updatePublishButtonText(initialType);
            }
            
            window.selectType = function(type) {
                // Update radio button
                document.getElementById(`type_${type}`).checked = true;
                
                // Update visual selection
                document.getElementById('official-option').classList.remove('selected');
                document.getElementById('unofficial-option').classList.remove('selected');
                document.getElementById(`${type}-option`).classList.add('selected');
                
                // Show/hide info sections
                document.getElementById('official-info').style.display = type === 'official' ? 'block' : 'none';
                document.getElementById('unofficial-info').style.display = type === 'unofficial' ? 'block' : 'none';
                
                // Update publish button text
                updatePublishButtonText(type);
            }
            
            function updatePublishButtonText(type) {
                const userRole = "{{ $user?->role ?? 'guest' }}";
                const isAdminOrStaff = ['admin', 'staff'].includes(userRole);
                
                if (type === 'official') {
                    if (isAdminOrStaff) {
                        publishText.textContent = 'Publish Official Announcement';
                        publishButton.title = 'Publish immediately as official announcement';
                    } else {
                        publishText.textContent = 'Submit for Verification';
                        publishButton.title = 'Submit for admin/staff verification';
                    }
                } else {
                    publishText.textContent = 'Publish Announcement';
                    publishButton.title = 'Publish immediately as unofficial announcement';
                }
            }
            
            // Add click handlers for radio options
            document.querySelectorAll('.radio-input').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        selectType(this.value);
                    }
                });
            });
        });
    </script>
</body>
</html>