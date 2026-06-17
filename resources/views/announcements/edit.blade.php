<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - UTHM Bulletin Board</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
    <style>
        .form-input-focus:focus {
            outline: none;
            border-color: #0056a6;
            box-shadow: 0 0 0 3px rgba(0, 86, 166, 0.15);
        }
        
        .moderation-warning {
            border-color: #dc2626 !important;
            background-color: #fef2f2 !important;
        }
        
        .moderation-safe {
            border-color: #10b981 !important;
            background-color: #f0fdf4 !important;
        }
        
        .moderation-message {
            animation: slideDown 0.25s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Interactive selection card transitions */
        .select-dot {
            transition: all 0.2s ease;
        }
        
        .badge-admin { background-color: #dc2626; color: white; }
        .badge-staff { background-color: #2563eb; color: white; }
        .badge-student { background-color: #059669; color: white; }
        .badge-guest { background-color: #6b7280; color: white; }
    </style>
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center min-w-0">
                        <a href="{{ route('announcements.index') }}" class="mr-4 p-2 text-gray-500 hover:text-uthm-blue hover:bg-uthm-blue-light rounded-lg transition-all" aria-label="Go back">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900 truncate">Edit Announcement</h1>
                        <span class="mx-2 text-gray-300 hidden sm:inline">/</span>
                        <span class="text-gray-500 text-sm hidden sm:inline truncate">Modify announcement settings</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        @include('layouts.partials.notification-bell')
                        
                        <div class="relative">
                            <button id="user-menu-button" type="button" class="flex items-center space-x-2 p-1.5 pr-3 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                                    <span class="font-bold text-uthm-blue text-sm">{{ strtoupper(substr(Auth::user()->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ Auth::user()->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id ?? 'UTHM Member' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:inline"></i>
                            </button>
                            
                            <div id="user-menu" class="portal-dropdown absolute right-0 mt-2 w-52 py-2 hidden z-50">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>
                                <a href="{{ route('announcements.my-announcements') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-alt mr-2"></i> My Announcements
                                </a>
                                <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        @include('layouts.partials.portal-content-open')

        <!-- Container Grid -->
        <div class="max-w-7xl mx-auto py-2">
            
            <!-- Success Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                        <span class="text-green-800 text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Moderation Global Error Display -->
            @error('moderation')
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-red-600 mt-1 mr-3 text-xl"></i>
                        <div>
                            <strong class="text-red-800 font-semibold block mb-1">⚠️ Content Blocked</strong>
                            <p class="text-red-700 text-sm">{{ $message }}</p>
                            <p class="text-xs text-red-600 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Please remove any inappropriate language before submitting.
                            </p>
                        </div>
                    </div>
                </div>
            @enderror

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3 text-xl"></i>
                        <div>
                            <strong class="text-red-800 font-semibold block mb-1">Validation Errors</strong>
                            <ul class="text-red-750 text-sm list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Editor Card Form (Left 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="portal-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="border-b border-gray-100 pb-4 mb-6">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Edit Announcement</h2>
                                    <p class="text-gray-500 text-sm mt-1">Update fields to modify your published announcement or draft.</p>
                                </div>
                                <div class="text-xs text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-150 font-medium">
                                    Created: {{ $announcement->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" id="announcement-form" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <!-- Announcement Type Selection Cards -->
                            @if($hasOfficialColumn ?? false)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Announcement Type <span class="text-red-500">*</span>
                                </label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Official Option -->
                                    <div id="official-card" onclick="selectType('official')" class="relative border-2 border-gray-200 rounded-xl p-5 cursor-pointer hover:border-uthm-blue transition-all duration-200 flex flex-col justify-between">
                                        <input type="radio" 
                                               id="type_official" 
                                               name="announcement_type_radio" 
                                               value="official"
                                               class="sr-only"
                                               {{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') == 'official' ? 'checked' : '' }}
                                               onchange="updateFormAction(this)">
                                        <div class="flex items-start mb-3">
                                            <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0 shadow-inner">
                                                <i class="fas fa-shield-alt text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="font-bold text-gray-900 text-sm">Official Announcement</h4>
                                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">For university policy changes, official notices, and administration procedures.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 right-4 w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center select-dot">
                                            <div class="w-2 h-2 rounded-full bg-white hidden selection-inner"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Unofficial Option -->
                                    <div id="unofficial-card" onclick="selectType('unofficial')" class="relative border-2 border-gray-200 rounded-xl p-5 cursor-pointer hover:border-uthm-blue transition-all duration-200 flex flex-col justify-between">
                                        <input type="radio" 
                                               id="type_unofficial" 
                                               name="announcement_type_radio" 
                                               value="unofficial"
                                               class="sr-only"
                                               {{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') == 'unofficial' ? 'checked' : '' }}
                                               onchange="updateFormAction(this)">
                                        <div class="flex items-start mb-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 text-uthm-blue flex items-center justify-center shrink-0 shadow-inner">
                                                <i class="fas fa-user-friends text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="font-bold text-gray-900 text-sm">Unofficial Announcement</h4>
                                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">For student club events, social initiatives, and department notices.</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 right-4 w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center select-dot">
                                            <div class="w-2 h-2 rounded-full bg-white hidden selection-inner"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden field to store actual type selection -->
                                <input type="hidden" name="announcement_type" id="announcement_type_hidden" value="{{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') }}">

                                <!-- Official Info Banner -->
                                <div id="official-info" class="mt-4 p-4 rounded-xl border bg-blue-50/50 border-blue-100 text-blue-800 transition-all duration-300">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2.5"></i>
                                        <div class="text-xs">
                                            @if(in_array(Auth::user()->role ?? 'guest', ['admin', 'staff']))
                                                <span class="font-semibold text-blue-900">Pre-Verified:</span> As a staff/admin, your announcement is official and will stay published.
                                            @else
                                                <span class="font-semibold text-blue-900">Verification Queued:</span> Changing this to Official will send it to the queue for staff approval.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Unofficial Info Banner -->
                                <div id="unofficial-info" class="mt-4 p-4 rounded-xl border bg-amber-50/50 border-amber-100 text-amber-800 transition-all duration-300">
                                    <div class="flex items-start">
                                        <i class="fas fa-bolt text-amber-500 mt-0.5 mr-2.5"></i>
                                        <div class="text-xs">
                                            <span class="font-semibold text-amber-900">Instant Publishing:</span> Unofficial notices do not require administrative verification.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Title Input -->
                            <div>
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Announcement Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $announcement->title) }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm"
                                       placeholder="Enter title"
                                       required>
                                <div id="title-moderation-message" class="mt-2 text-xs hidden moderation-message"></div>
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category and Priority Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <select id="category" name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm bg-white">
                                        <option value="" disabled>Select Category</option>
                                        <option value="academic" {{ old('category', $announcement->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                                        <option value="events" {{ old('category', $announcement->category) == 'events' ? 'selected' : '' }}>Events</option>
                                        <option value="general" {{ old('category', $announcement->category) == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="club" {{ old('category', $announcement->category) == 'club' ? 'selected' : '' }}>Club</option>
                                    </select>
                                    @error('category')
                                        <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Priority Level
                                    </label>
                                    <select id="priority" name="priority" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm bg-white">
                                        <option value="normal" {{ old('priority', $announcement->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="important" {{ old('priority', $announcement->priority) == 'important' ? 'selected' : '' }}>Important</option>
                                        <option value="urgent" {{ old('priority', $announcement->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Department -->
                            <div>
                                <label for="department" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Department/Office
                                </label>
                                <input type="text" 
                                       id="department" 
                                       name="department" 
                                       value="{{ old('department', $announcement->department) }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm"
                                       placeholder="e.g. IT Department, Registrar's Office">
                                @error('department')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Status
                                </label>
                                <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm bg-white">
                                    <option value="draft" {{ old('status', $announcement->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ in_array(old('status', $announcement->status), ['published', 'expired']) ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $announcement->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                    <option value="pending_verification" {{ old('status', $announcement->status) == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cover Image Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Cover Image <span class="text-gray-450 text-xs font-normal">(Optional)</span>
                                </label>
                                
                                <input type="file" 
                                       id="image" 
                                       name="image"
                                       class="hidden"
                                       accept=".jpg,.jpeg,.png,.gif,.webp">
                                
                                <!-- Preview container for newly selected image -->
                                <div id="image-preview-container" class="hidden relative group rounded-xl overflow-hidden shadow-sm border border-gray-100 max-w-xl mb-4">
                                    <img id="image-preview" src="" alt="Image Preview" class="w-full h-48 object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" 
                                                onclick="removeImage()" 
                                                class="bg-red-600 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold shadow-lg transition-transform hover:scale-105">
                                            <i class="fas fa-trash mr-1.5"></i>Remove Selected
                                        </button>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/60 px-4 py-2 text-white text-xs truncate" id="image-filename"></div>
                                </div>

                                <!-- Display Current Image if exists -->
                                @if(isset($announcement->image) && !empty($announcement->image))
                                    <div id="current-image-container" class="relative group rounded-xl overflow-hidden shadow-sm border border-gray-100 max-w-xl mb-4">
                                        <img src="{{ asset('storage/' . $announcement->image) }}" alt="Current Image" class="w-full h-48 object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" 
                                                    onclick="removeCurrentImage()" 
                                                    class="bg-red-650 hover:bg-red-700 text-white rounded-full px-4 py-2 text-xs font-semibold shadow-lg transition-transform hover:scale-105">
                                                <i class="fas fa-trash mr-1.5"></i>Delete Current
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Drag zone placeholder -->
                                <div id="image-placeholder-container" 
                                     onclick="document.getElementById('image').click()" 
                                     class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center cursor-pointer hover:border-uthm-blue hover:bg-uthm-blue-light/10 transition-all duration-200 group max-w-xl @if(isset($announcement->image) && !empty($announcement->image)) hidden @endif">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-3 group-hover:bg-uthm-blue-light group-hover:text-uthm-blue transition-colors">
                                        <i class="fas fa-cloud-upload-alt text-xl"></i>
                                    </div>
                                    <h5 class="text-gray-700 font-bold text-sm">Replace Cover Image</h5>
                                    <p class="text-gray-400 text-xs mt-1">Drag and drop image here, or click to browse files</p>
                                    <p class="text-gray-400 text-[10px] mt-2">JPG, PNG, GIF or WEBP up to 2MB</p>
                                </div>
                                <div id="image-error-msg" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                                    <i class="fas fa-exclamation-triangle mr-1.5 text-red-500"></i>
                                    <span id="image-error-text"></span>
                                </div>
                                @error('image')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Content Textarea -->
                            <div>
                                <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Content <span class="text-red-500">*</span>
                                </label>
                                <textarea id="content" 
                                          name="content" 
                                          rows="8"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm resize-y min-h-[150px]"
                                          placeholder="Enter content details..."
                                          required>{{ old('content', $announcement->content) }}</textarea>
                                <div id="content-moderation-message" class="mt-2 text-xs hidden moderation-message"></div>
                                <div class="flex justify-between items-center mt-2 text-xs text-gray-400">
                                    <span><i class="far fa-keyboard mr-1.5"></i>Draft clear, error-free instructions.</span>
                                    <span id="char-counter">0 characters</span>
                                </div>
                                @error('content')
                                    <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Publish Date -->
                                <div>
                                    <label for="publish_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Publish Date
                                    </label>
                                    <input type="datetime-local" 
                                           id="publish_date" 
                                           name="publish_date" 
                                           value="{{ old('publish_date', $announcement->publish_date ? $announcement->publish_date->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm">
                                    <p class="text-gray-400 text-[11px] mt-1.5">Leave empty to publish immediately.</p>
                                    @error('publish_date')
                                        <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Expiry Date -->
                                <div>
                                    <label for="expiry_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Expiry Date
                                    </label>
                                    <input type="date" 
                                           id="expiry_date" 
                                           name="expiry_date" 
                                           value="{{ old('expiry_date', $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d') : '') }}"
                                           min="{{ now()->format('Y-m-d') }}"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-uthm-blue/15 focus:border-uthm-blue transition-all shadow-sm">
                                    <p class="text-gray-400 text-[11px] mt-1.5">Optional expiration target date.</p>
                                    @error('expiry_date')
                                        <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                    @if($announcement->status === 'expired')
                                        <div class="mt-2 text-xs text-amber-700 bg-amber-50/50 border border-amber-100 p-2.5 rounded-xl flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2 text-amber-500"></i> This announcement has expired. Choose a future expiry date and publish to restore it.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-100">
                                <div class="flex gap-3">
                                    <button type="button" 
                                            onclick="confirmDelete()"
                                            class="inline-flex items-center px-4 py-2.5 border border-red-200 text-red-650 font-medium rounded-xl text-sm hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                    <a href="{{ route('announcements.show', $announcement) }}" 
                                       class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-gray-700 font-medium rounded-xl text-sm hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>Preview
                                    </a>
                                </div>
                                <div class="flex gap-3">
                                    <a href="{{ route('announcements.index') }}" 
                                       class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                        Cancel
                                    </a>
                                    <button type="submit" 
                                            id="submitButton"
                                            class="px-6 py-2.5 bg-uthm-blue hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-all shadow-sm hover:shadow flex items-center">
                                        <i class="fas fa-save mr-1.5" id="publish-icon"></i>
                                        <i class="fas fa-spinner fa-spin mr-1.5 hidden" id="publish-spinner"></i>
                                        <span id="submitButtonText">Update Announcement</span>
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

                <!-- Helper & Guide Side panel (Right 1/3) -->
                <div class="space-y-6">
                    
                    <!-- Writing Advice -->
                    <div class="portal-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center space-x-2.5 mb-4 border-b border-gray-50 pb-3">
                            <div class="text-uthm-blue"><i class="fas fa-lightbulb text-lg"></i></div>
                            <h3 class="font-bold text-gray-900 text-sm">Writing Guidelines</h3>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-gray-600">
                            <p>Follow these best practices to ensure your announcement is professional, clear, and highly engaging:</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 shrink-0"><i class="fas fa-check-circle"></i></span>
                                    <span><strong>Start Strong:</strong> Summarize the core update or request in the first paragraph. Users scan listings quickly.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 shrink-0"><i class="fas fa-check-circle"></i></span>
                                    <span><strong>Choose Categories Wisely:</strong> Align categories with contents. Academic procedures go to <em>Academic</em>, club meetings to <em>Club</em>.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 shrink-0"><i class="fas fa-check-circle"></i></span>
                                    <span><strong>Call to Action:</strong> If steps are required (such as registrations or submissions), detail links, dates, and contact emails clearly.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Safety checks dynamic badge -->
                    <div class="portal-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center space-x-2.5 mb-4 border-b border-gray-50 pb-3">
                            <div class="text-green-600"><i class="fas fa-shield-alt text-lg"></i></div>
                            <h3 class="font-bold text-gray-900 text-sm">AI Content Safety</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Checker Engine:</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 font-semibold text-[10px]"><i class="fas fa-circle text-[7px] mr-1.5 animate-pulse"></i>Active</span>
                            </div>
                            <p class="text-gray-550 text-[11px] leading-relaxed">
                                To maintain a respectful campus environment, a real-time safety filter validates your text for spam or inappropriate wording before submission.
                            </p>
                            <div class="pt-2.5 border-t border-gray-50 flex items-center justify-between text-xs">
                                <span class="text-gray-400">Security Status:</span>
                                <span id="moderation-summary-badge" class="font-semibold text-gray-500">Idle</span>
                            </div>
                        </div>
                    </div>

                </div>
                
            </div>
        </div>

        @include('layouts.partials.portal-content-close')
    </div>

    @include('layouts.partials.portal-scripts')

    <!-- Moderation & Action Scripts -->
    <script>
        let moderationTimeouts = {};
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const submitButton = document.getElementById('submitButton');
        const form = document.getElementById('announcement-form');

        document.addEventListener('DOMContentLoaded', function() {
            // Set min date bounds
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
            
            // Confirmation alert on publish status switch
            const statusSelect = document.getElementById('status');
            if (form && statusSelect) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    if (!titleInput.value.trim()) {
                        titleInput.classList.add('border-red-500');
                        isValid = false;
                    }
                    
                    if (!contentInput.value.trim()) {
                        contentInput.classList.add('border-red-500');
                        isValid = false;
                    }
                    
                    const typeRadio = document.querySelector('input[name="announcement_type_radio"]:checked');
                    if (typeRadio && !typeRadio.value) {
                        alert('Please select an announcement type.');
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                    } else {
                        const currentStatus = '{{ $announcement->status ?? 'draft' }}';
                        if (statusSelect.value === 'published' && currentStatus !== 'published') {
                            if (!confirm('Are you sure you want to publish this announcement? It will immediately become visible to other users.')) {
                                e.preventDefault();
                                return;
                            }
                        }
                        
                        // Show publish loading spinner
                        const spinner = document.getElementById('publish-spinner');
                        const icon = document.getElementById('publish-icon');
                        if (spinner && icon) {
                            spinner.classList.remove('hidden');
                            icon.classList.add('hidden');
                        }
                        disableSubmitButton();
                    }
                });
            }
            
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
                contentInput.dispatchEvent(new Event('input'));
            }
            
            // Image upload triggers
            const imageInput = document.getElementById('image');
            const imagePlaceholder = document.getElementById('image-placeholder-container');
            
            if (imageInput) {
                imageInput.addEventListener('change', function() { handleImageUpload(this.files[0]); });
                
                if (imagePlaceholder) {
                    imagePlaceholder.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        this.classList.add('opacity-75', 'border-uthm-blue');
                    });
                    
                    imagePlaceholder.addEventListener('dragleave', function(e) {
                        e.preventDefault();
                        this.classList.remove('opacity-75', 'border-uthm-blue');
                    });
                    
                    imagePlaceholder.addEventListener('drop', function(e) {
                        e.preventDefault();
                        this.classList.remove('opacity-75', 'border-uthm-blue');
                        const files = e.dataTransfer.files;
                        if (files.length > 0) {
                            imageInput.files = files;
                            handleImageUpload(files[0]);
                        }
                    });
                }
            }

            // Initialize selector cards if column option is present
            if (document.querySelector('input[name="announcement_type_radio"]')) {
                initializeTypeSelection();
            }
            
            // Setup real-time safety scans
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
                if (contentInput.value.length > 5) {
                    setTimeout(() => checkContent(contentInput.value, 'content'), 500);
                }
            }
        }
        
        async function checkContent(text, field) {
            if (!text || text.length < 5) {
                removeModerationWarning(field);
                updateSecurityBadge();
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
                    disableSubmitButton();
                } else {
                    removeModerationWarning(field);
                    enableSubmitButton();
                }
                
                updateFieldStyle(field, result.flagged);
                updateSecurityBadge();
                
            } catch (error) {
                console.error('Moderation check failed:', error);
                hideCheckingIndicator(field);
                updateSecurityBadge();
                enableSubmitButton();
            }
        }
        
        function showCheckingIndicator(field) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv) {
                messageDiv.className = 'mt-2 text-xs text-blue-650 flex items-center moderation-message';
                messageDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Scanning field...';
                messageDiv.classList.remove('hidden');
            }
            
            const badge = document.getElementById('moderation-summary-badge');
            if (badge) {
                badge.className = "font-semibold text-blue-600";
                badge.innerHTML = "<i class='fas fa-spinner fa-spin mr-1.5'></i>Scanning...";
            }
        }
        
        function hideCheckingIndicator(field) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv && messageDiv.innerHTML.includes('Scanning')) {
                messageDiv.classList.add('hidden');
            }
        }
        
        function showModerationWarning(field, violations) {
            const messageDiv = document.getElementById(`${field}-moderation-message`);
            if (messageDiv) {
                const violationTypes = violations.map(v => v.classifier).join(', ');
                messageDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-750 moderation-message';
                messageDiv.innerHTML = `
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mt-0.5 mr-2 text-red-500"></i>
                        <div>
                            <strong class="font-semibold text-red-800">Flagged content:</strong>
                            <span class="ml-1">Please avoid ${violationTypes} keywords.</span>
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
        
        function updateSecurityBadge() {
            const titleWarning = document.getElementById('title-moderation-message');
            const contentWarning = document.getElementById('content-moderation-message');
            const badge = document.getElementById('moderation-summary-badge');
            
            const titleFlagged = titleWarning && !titleWarning.classList.contains('hidden') && titleWarning.innerHTML.includes('Flagged');
            const contentFlagged = contentWarning && !contentWarning.classList.contains('hidden') && contentWarning.innerHTML.includes('Flagged');
            
            const titleScanning = titleWarning && !titleWarning.classList.contains('hidden') && titleWarning.innerHTML.includes('Scanning');
            const contentScanning = contentWarning && !contentWarning.classList.contains('hidden') && contentWarning.innerHTML.includes('Scanning');
            
            if (badge) {
                if (titleFlagged || contentFlagged) {
                    badge.className = "font-semibold text-red-600";
                    badge.innerHTML = "<i class='fas fa-exclamation-triangle mr-1'></i>Flagged";
                } else if (titleScanning || contentScanning) {
                    badge.className = "font-semibold text-blue-600";
                    badge.innerHTML = "<i class='fas fa-spinner fa-spin mr-1'></i>Scanning...";
                } else if (document.getElementById('title').value.length > 5 || document.getElementById('content').value.length > 5) {
                    badge.className = "font-semibold text-green-600";
                    badge.innerHTML = "<i class='fas fa-check-circle mr-1'></i>Safe";
                } else {
                    badge.className = "font-semibold text-gray-500";
                    badge.innerHTML = "Idle";
                }
            }
        }

        function disableSubmitButton() {
            const btn = document.getElementById('submitButton');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        function enableSubmitButton() {
            const titleWarning = document.getElementById('title-moderation-message');
            const contentWarning = document.getElementById('content-moderation-message');
            
            const titleBlocked = titleWarning && !titleWarning.classList.contains('hidden') && titleWarning.innerHTML.includes('Flagged');
            const contentBlocked = contentWarning && !contentWarning.classList.contains('hidden') && contentWarning.innerHTML.includes('Flagged');
            
            const btn = document.getElementById('submitButton');
            if (!titleBlocked && !contentBlocked && btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
            if (submitButtonText) {
                if (announcementType === 'official') {
                    submitButtonText.textContent = 'Update & Move to Official Board';
                } else {
                    submitButtonText.textContent = 'Update & Move to Unofficial Page';
                }
            }
            
            updatePostingOptionCards(announcementType);
        }

        function updatePostingOptionCards(type) {
            const officialCard = document.getElementById('official-card');
            const unofficialCard = document.getElementById('unofficial-card');
            if (!officialCard || !unofficialCard) return;
            
            if (type === 'official') {
                officialCard.className = "relative border-2 border-green-500 bg-green-50/15 rounded-xl p-5 cursor-pointer transition-all duration-200 flex flex-col justify-between";
                unofficialCard.className = "relative border-2 border-gray-200 rounded-xl p-5 cursor-pointer hover:border-uthm-blue transition-all duration-200 flex flex-col justify-between";
                
                officialCard.querySelector('.select-dot').className = "absolute top-4 right-4 w-4 h-4 rounded-full border-2 border-green-500 flex items-center justify-center select-dot bg-green-500";
                officialCard.querySelector('.selection-inner').classList.remove('hidden');
                
                unofficialCard.querySelector('.select-dot').className = "absolute top-4 right-4 w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center select-dot bg-transparent";
                unofficialCard.querySelector('.selection-inner').classList.add('hidden');
            } else {
                unofficialCard.className = "relative border-2 border-uthm-blue bg-blue-50/10 rounded-xl p-5 cursor-pointer transition-all duration-200 flex flex-col justify-between";
                officialCard.className = "relative border-2 border-gray-200 rounded-xl p-5 cursor-pointer hover:border-uthm-blue transition-all duration-200 flex flex-col justify-between";
                
                unofficialCard.querySelector('.select-dot').className = "absolute top-4 right-4 w-4 h-4 rounded-full border-2 border-uthm-blue flex items-center justify-center select-dot bg-uthm-blue";
                unofficialCard.querySelector('.selection-inner').classList.remove('hidden');
                
                officialCard.querySelector('.select-dot').className = "absolute top-4 right-4 w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center select-dot bg-transparent";
                officialCard.querySelector('.selection-inner').classList.add('hidden');
            }

            document.getElementById('official-info').style.display = type === 'official' ? 'block' : 'none';
            document.getElementById('unofficial-info').style.display = type === 'unofficial' ? 'block' : 'none';
        }

        function initializeTypeSelection() {
            const initialType = "{{ old('announcement_type', $announcement->is_official ? 'official' : 'unofficial') }}";
            const radio = document.querySelector(`input[name="announcement_type_radio"][value="${initialType}"]`);
            if (radio) radio.checked = true;
            updatePostingOptionCards(initialType);
            
            const submitButtonText = document.getElementById('submitButtonText');
            if (submitButtonText) {
                if (initialType === 'official') {
                    submitButtonText.textContent = 'Update & Move to Official Board';
                } else {
                    submitButtonText.textContent = 'Update & Move to Unofficial Page';
                }
            }
        }
        
        window.selectType = function(type) {
            const radio = document.querySelector(`input[name="announcement_type_radio"][value="${type}"]`);
            if (radio) {
                radio.checked = true;
                updateFormAction(radio);
            }
        };

        function showImageError(msg) {
            const errDiv = document.getElementById('image-error-msg');
            const errText = document.getElementById('image-error-text');
            if (errDiv && errText) {
                errText.textContent = msg;
                errDiv.classList.remove('hidden');
            }
            disableSubmitButton();
        }

        function clearImageError() {
            const errDiv = document.getElementById('image-error-msg');
            if (errDiv) errDiv.classList.add('hidden');
            enableSubmitButton();
        }

        // Image upload handlers
        function handleImageUpload(file) {
            if (!file) return;

            // 2 MB — safe limit for shared Nginx hosting (client_max_body_size default)
            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                document.getElementById('image').value = '';
                showImageError(`Image is too large (${(file.size / 1024 / 1024).toFixed(1)} MB). Please choose an image smaller than 2 MB.`);
                return;
            }

            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                document.getElementById('image').value = '';
                showImageError('Invalid image format. Please use JPG, PNG, GIF, or WEBP.');
                return;
            }

            clearImageError();

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-filename').innerHTML = `<i class='fas fa-image mr-1.5'></i>${file.name}`;
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
            const formObj = document.getElementById('announcement-form');
            
            let removeImageInput = document.getElementById('remove_image_input');
            if (!removeImageInput) {
                removeImageInput = document.createElement('input');
                removeImageInput.type = 'hidden';
                removeImageInput.id = 'remove_image_input';
                removeImageInput.name = 'remove_image';
                removeImageInput.value = '1';
                formObj.appendChild(removeImageInput);
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