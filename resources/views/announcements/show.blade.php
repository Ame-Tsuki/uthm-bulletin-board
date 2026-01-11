<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title ?? 'Announcement Details' }} - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .badge-urgent {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .badge-important {
            background-color: #fef3c7;
            color: #d97706;
        }
        .badge-academic {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .badge-events {
            background-color: #f3e8ff;
            color: #7c3aed;
        }
        .badge-general {
            background-color: #f0f9ff;
            color: #0369a1;
        }
        .prose {
            color: #374151;
            line-height: 1.75;
        }
        .prose p {
            margin-top: 1em;
            margin-bottom: 1em;
        }
        .prose ul {
            margin-top: 1em;
            margin-bottom: 1em;
            padding-left: 1.625em;
        }
        .prose li {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation (Same as announcement.blade.php for consistency) -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('announcements.index') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-3"></i>
                        <span class="font-medium">Back to Announcements</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                        <span class="font-medium">{{ $user->name ?? 'Guest' }}</span>
                        @if($user->role ?? false)
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($user->role) }}
                            </span>
                        @endif
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Announcement Container -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Announcement Header -->
                <div class="p-8 border-b border-gray-200">
                    <div class="flex flex-col space-y-4">
                        <!-- Badges -->
                        <div class="flex flex-wrap gap-2">
                            @if(isset($announcement->priority) && $announcement->priority === 'urgent')
                                <span class="px-4 py-2 rounded-full text-sm font-medium badge-urgent">
                                    <i class="fas fa-exclamation-circle mr-2"></i> Urgent
                                </span>
                            @elseif(isset($announcement->priority) && $announcement->priority === 'important')
                                <span class="px-4 py-2 rounded-full text-sm font-medium badge-important">
                                    <i class="fas fa-star mr-2"></i> Important
                                </span>
                            @endif
                            
                            @php
                                $category = $announcement->category ?? 'general';
                            @endphp
                            
                            @if(!empty($category))
                                <span class="px-4 py-2 rounded-full text-sm font-medium badge-{{ $category }}">
                                    {{ ucfirst($category) }}
                                </span>
                            @endif
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                            {{ $announcement->title }}
                        </h1>

                        <!-- Meta Information -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                                <span>{{ $announcement->author->name ?? 'Unknown author' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-calendar mr-2 text-gray-400"></i>
                                <span>{{ optional($announcement->created_at)->format('F j, Y') ?? 'Date unavailable' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-clock mr-2 text-gray-400"></i>
                                <span>{{ optional($announcement->created_at)->format('g:i A') ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcement Content -->
                <div class="p-8">
                    <!-- Summary Box -->
                    <div class="mb-8 p-6 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Summary
                        </h3>
                        <p class="text-blue-800">
                            {{ \Illuminate\Support\Str::limit($announcement->content, 200) }}
                        </p>
                    </div>

                    <!-- Main Content -->
                    <div class="prose max-w-none">
                        {!! nl2br(e($announcement->content ?? '')) !!}
                    </div>

                    <!-- Attachments Section -->
                    @if(isset($announcement->attachments) && count($announcement->attachments) > 0)
                        <div class="mt-10 pt-8 border-t border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">
                                <i class="fas fa-paperclip mr-2"></i>Attachments
                            </h3>
                            <div class="space-y-3">
                                @foreach($announcement->attachments as $attachment)
                                    <a href="#" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                        <i class="fas fa-file-pdf text-red-500 text-xl mr-4"></i>
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $attachment->name ?? 'Maintenance_Schedule.pdf' }}</p>
                                            <p class="text-sm text-gray-500 mt-1">{{ $attachment->size ?? '1.2 MB' }}</p>
                                        </div>
                                        <i class="fas fa-download text-gray-400"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Related Info -->
                    <div class="mt-10 pt-8 border-t border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Additional Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700 font-medium mb-1">Last Updated</p>
                                <p class="text-blue-900">
                                    {{ optional($announcement->updated_at)->format('F j, Y \\a\\t g:i A') ?? 'Date unavailable' }}
                                </p>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-700 font-medium mb-1">Status</p>
                                <p class="text-green-900">
                                    <span class="inline-flex items-center">
                                        <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span>
                                        Active
                                    </span>
                                </p>
                            </div>
                            <div class="p-4 bg-purple-50 rounded-lg">
                                <p class="text-sm text-purple-700 font-medium mb-1">Visibility</p>
                                <p class="purple-900">All Users</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-8 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-eye mr-2"></i>
                                Views: {{ $announcement->views ?? 0 }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('announcements.index') }}" 
                               class="inline-flex items-center px-5 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-list mr-2"></i>
                                Back to All Announcements
                            </a>
                            
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                                <a href="{{ route('announcements.edit', $announcement) }}" 
                                   class="inline-flex items-center px-5 py-3 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit
                                </a>
                                <button onclick="window.print()" 
                                        class="inline-flex items-center px-5 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                    <i class="fas fa-print mr-2"></i>
                                    Print
                                </button>
                                <button onclick="shareAnnouncement()" 
                                        class="inline-flex items-center px-5 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-share-alt mr-2"></i>
                                    Share
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}. All rights reserved.</p>
                <p class="mt-1">For issues or inquiries, contact: <a href="mailto:support@uthm.edu.my" class="text-blue-600 hover:text-blue-800">support@uthm.edu.my</a></p>
            </div>
        </div>
    </footer>

    <script>
        function shareAnnouncement() {
            const title = document.querySelector('h1').textContent;
            const url = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: 'Check out this announcement from UTHM',
                    url: url,
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(url);
                alert('Link copied to clipboard!');
            }
        }

        // Add active state to current page in nav
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight current announcement
            const currentUrl = window.location.pathname;
            console.log('Current URL:', currentUrl);
            
            // Optional: Add animation to content load
            const content = document.querySelector('.prose');
            if (content) {
                content.style.opacity = '0';
                content.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    content.style.opacity = '1';
                }, 100);
            }
        });
    </script>
</body>
</html>