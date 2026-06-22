<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    @include('layouts.partials.notification-bell')
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
            @if(($announcement->status === 'banned' || $announcement->is_banned) && $announcement->author_id === auth()->id())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-semibold"><i class="fas fa-ban mr-2"></i>This post has been banned by an administrator</p>
                    @if($announcement->ban_reason)
                        <p class="text-sm text-red-700 mt-2"><strong>Reason:</strong> {{ $announcement->ban_reason }}</p>
                    @endif
                    @if($announcement->banned_at)
                        <p class="text-xs text-red-600 mt-1">Banned on {{ $announcement->banned_at->format('F j, Y \\a\\t g:i A') }}</p>
                    @endif
                </div>
            @endif
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

                <!-- Image Section -->
                <div class="bg-gray-50 p-8 border-b border-gray-200">
                    <div class="w-full max-w-4xl mx-auto bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center shadow-lg">
                        @if(isset($announcement->image) && $announcement->image)
                            <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-auto object-contain">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-400 w-full py-32 bg-gradient-to-br from-gray-100 to-gray-200">
                                <i class="fas fa-image text-8xl mb-6 opacity-40"></i>
                                <p class="text-2xl font-bold mb-2 opacity-50">No image available</p>
                                <p class="text-base opacity-40">Image placeholder</p>
                            </div>
                        @endif
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
                            <div class="p-4 {{ ($announcement->status === 'banned' || $announcement->is_banned) ? 'bg-red-50' : ($announcement->status === 'expired' ? 'bg-gray-50' : 'bg-green-50') }} rounded-lg">
                                <p class="text-sm {{ ($announcement->status === 'banned' || $announcement->is_banned) ? 'text-red-700' : ($announcement->status === 'expired' ? 'text-gray-700' : 'text-green-700') }} font-medium mb-1">Status</p>
                                <p class="{{ ($announcement->status === 'banned' || $announcement->is_banned) ? 'text-red-900' : ($announcement->status === 'expired' ? 'text-gray-900' : 'text-green-900') }}">
                                    @if($announcement->status === 'banned' || $announcement->is_banned)
                                        <span class="inline-flex items-center">
                                            <span class="h-2 w-2 bg-red-500 rounded-full mr-2"></span>
                                            Banned
                                        </span>
                                    @elseif($announcement->status === 'expired')
                                        <span class="inline-flex items-center">
                                            <span class="h-2 w-2 bg-gray-500 rounded-full mr-2"></span>
                                            Expired
                                        </span>
                                    @else
                                        <span class="inline-flex items-center">
                                            <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span>
                                            {{ ucfirst(str_replace('_', ' ', $announcement->status ?? 'active')) }}
                                        </span>
                                    @endif
                                </p>
                                @if($announcement->expiry_date)
                                    <p class="text-xs text-gray-600 mt-2">Expires: {{ $announcement->expiry_date->format('M d, Y') }}</p>
                                @endif
                            </div>
                            <div class="p-4 bg-purple-50 rounded-lg">
                                <p class="text-sm text-purple-700 font-medium mb-1">Visibility</p>
                                <p class="text-purple-900">
                                    @if($announcement->status === 'expired')
                                        Hidden from main board (author only)
                                    @else
                                        All Users
                                    @endif
                                </p>
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
                            @include('announcements.partials.calendar-dropdown', [
                                'announcement' => $announcement,
                                'calendar' => $calendar,
                                'compact' => false,
                            ])

                            <a href="{{ route('announcements.index') }}" 
                               class="inline-flex items-center px-5 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-list mr-2"></i>
                                Back to All Announcements
                            </a>

                            @if($canReport ?? false)
                                @if($hasReported ?? false)
                                    <span class="inline-flex items-center px-5 py-3 bg-gray-100 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                                        <i class="fas fa-flag mr-2"></i>
                                        Reported
                                    </span>
                                @else
                                    <button type="button" onclick="openReportModal()"
                                            class="inline-flex items-center px-5 py-3 bg-red-50 text-red-700 font-medium rounded-lg hover:bg-red-100 transition-colors border border-red-200">
                                        <i class="fas fa-flag mr-2"></i>
                                        Report
                                    </button>
                                @endif
                            @endif
                            
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

            <!-- Q&A / FAQ Section -->
            <div id="questions-section" class="mt-8 bg-white rounded-xl shadow-lg overflow-hidden p-8">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-comments text-blue-500 mr-2"></i>Questions & Answers (FAQ)
                    </h2>
                    @php
                        $answeredQuestions = $announcement->questions->filter(fn($q) => $q->isAnswered());
                        $unansweredQuestions = $announcement->questions->filter(fn($q) => !$q->isAnswered());
                    @endphp
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                        {{ $answeredQuestions->count() }} {{ Str::plural('FAQ', $answeredQuestions->count()) }}
                    </span>
                </div>

                <!-- Session Status Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center shadow-sm">
                        <i class="fas fa-check-circle text-lg mr-2 text-green-500"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg shadow-sm">
                        <div class="flex items-center mb-2 font-semibold">
                            <i class="fas fa-exclamation-circle text-lg mr-2 text-red-500"></i>
                            <span>Please correct the errors below:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- ============================================ -->
                <!-- CREATOR / MODERATOR ANSWER PANEL -->
                <!-- ============================================ -->
                @php
                    $isCreatorOrStaff = ($announcement->author_id === auth()->id()) || in_array(auth()->user()->role, ['admin', 'staff']);
                    $myUnansweredQuestions = $unansweredQuestions->filter(fn($q) => $q->user_id === auth()->id());
                @endphp

                @if($isCreatorOrStaff)
                    @if($unansweredQuestions->count() > 0)
                        <div class="mb-8 p-6 bg-amber-50 border border-amber-200 rounded-xl">
                            <h3 class="text-lg font-bold text-amber-900 mb-4 flex items-center">
                                <i class="fas fa-question-circle mr-2 text-amber-500"></i>
                                Unanswered Questions ({{ $unansweredQuestions->count() }})
                            </h3>
                            <div class="space-y-6">
                                @foreach($unansweredQuestions as $question)
                                    <div class="bg-white p-5 rounded-lg border border-amber-100 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Unanswered Question
                                            </span>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">
                                                    {{ $question->created_at->diffForHumans() }}
                                                </span>
                                                <form action="{{ route('announcements.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm p-1" title="Delete Question">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="text-gray-800 font-medium mb-4">{{ $question->question_text }}</p>
                                        
                                        <!-- Answer Form -->
                                        <form action="{{ route('announcements.questions.answer', $question->id) }}" method="POST" class="space-y-3">
                                            @csrf
                                            <div>
                                                <textarea name="answer_text" rows="3" required
                                                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                                          placeholder="Write a public answer to this question..."></textarea>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                                                    Publish Answer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <!-- For regular users: Show their own pending unanswered questions -->
                    @if($myUnansweredQuestions->count() > 0)
                        <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-xl">
                            <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                                <i class="fas fa-clock mr-2 text-blue-500 animate-pulse"></i>
                                Your Questions Pending Answer
                            </h3>
                            <div class="space-y-4">
                                @foreach($myUnansweredQuestions as $question)
                                    <div class="bg-white p-4 rounded-lg border border-blue-100 shadow-sm relative group">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs text-blue-600 font-semibold">Pending Creator Answer</span>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-400">
                                                    {{ $question->created_at->diffForHumans() }}
                                                </span>
                                                <form action="{{ route('announcements.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your question?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm p-1" title="Delete Question">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="text-gray-800 font-medium">{{ $question->question_text }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <!-- ============================================ -->
                <!-- MAIN FAQ LIST (ANSWERED QUESTIONS) -->
                <!-- ============================================ -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-list-ol mr-2 text-blue-500"></i>Frequently Asked Questions
                    </h3>
                    
                    @if($answeredQuestions->count() > 0)
                        <div class="space-y-6">
                            @foreach($answeredQuestions as $question)
                                <div id="question-{{ $question->id }}" class="p-6 bg-gray-50 hover:bg-gray-100 transition-colors border border-gray-200 rounded-xl relative group shadow-sm">
                                    
                                    <!-- Delete Button for authorized users -->
                                    @if($question->user_id === auth()->id() || $announcement->author_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'staff']))
                                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <form action="{{ route('announcements.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Delete FAQ">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    <!-- Question Text -->
                                    <div class="flex items-start mb-4">
                                        <div class="bg-blue-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 shadow-sm">Q</div>
                                        <div class="flex-1">
                                            <p class="text-gray-900 font-semibold">{{ $question->question_text }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if($question->user_id === auth()->id())
                                                    <span class="font-medium text-blue-600">Anonymous (You)</span>
                                                @else
                                                    <span>Anonymous User</span>
                                                @endif
                                                • {{ $question->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Answer Text -->
                                    <div class="flex items-start pl-9 border-l-2 border-blue-200">
                                        <div class="bg-green-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 shadow-sm">A</div>
                                        <div class="flex-1">
                                            <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $question->answer_text }}</p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Answered by <span class="font-semibold text-gray-700">{{ $question->answerer->name ?? 'Author' }}</span> 
                                                @if($question->answered_by === $announcement->author_id)
                                                    <span class="text-xs px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-medium">Creator</span>
                                                @else
                                                    <span class="text-xs px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 font-medium">Staff/Admin</span>
                                                @endif
                                                • {{ $question->answered_at ? $question->answered_at->diffForHumans() : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500 font-medium">No questions have been answered yet.</p>
                            <p class="text-sm text-gray-400 mt-1">Have a query? Ask a question below!</p>
                        </div>
                    @endif
                </div>

                <!-- ============================================ -->
                <!-- ASK A QUESTION FORM -->
                <!-- ============================================ -->
                @if($announcement->author_id !== auth()->id())
                    <div class="border-t border-gray-200 pt-6 mt-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-paper-plane mr-2 text-blue-500"></i>Ask a Question
                        </h3>
                        <form action="{{ route('announcements.questions.store', $announcement->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <textarea name="question_text" rows="3" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                          placeholder="Type your question here... (e.g. Where is the venue? Is this open to all faculties?)"></textarea>
                            </div>
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-lock text-blue-500 mr-2"></i>
                                    <span>Your question will be posted anonymously to other users. Only your question and the creator's answer will be visible publicly.</span>
                                </div>
                                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                                    Submit Question
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Report Announcement</h3>
                <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Help us keep the bulletin board safe. Your report will be reviewed by an administrator.</p>
            <form id="reportForm" class="space-y-4">
                <div>
                    <label for="reportCategory" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="reportCategory" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">Select a reason</option>
                        <option value="spam">Spam or misleading</option>
                        <option value="inappropriate">Inappropriate content</option>
                        <option value="harassment">Harassment or hate speech</option>
                        <option value="misinformation">False or misleading information</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="reportReason" class="block text-sm font-medium text-gray-700 mb-1">Details</label>
                    <textarea id="reportReason" name="reason" rows="4" required minlength="10" maxlength="1000"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                              placeholder="Please describe why you are reporting this announcement (at least 10 characters)..."></textarea>
                </div>
                <p id="reportError" class="text-sm text-red-600 hidden"></p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="submitReportBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Submit Report</button>
                </div>
            </form>
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
        function openReportModal() {
            document.getElementById('reportModal').classList.remove('hidden');
            document.getElementById('reportError').classList.add('hidden');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.getElementById('reportForm').reset();
        }

        document.getElementById('reportForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitReportBtn');
            const errorEl = document.getElementById('reportError');
            errorEl.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Submitting...';

            fetch('{{ route('announcements.report', $announcement) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    category: document.getElementById('reportCategory').value,
                    reason: document.getElementById('reportReason').value,
                }),
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    closeReportModal();
                    alert(data.message);
                    location.reload();
                } else {
                    errorEl.textContent = data.message || 'Failed to submit report.';
                    errorEl.classList.remove('hidden');
                }
            })
            .catch(() => {
                errorEl.textContent = 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Submit Report';
            });
        });

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
    @include('announcements.partials.calendar-assets')
</body>
</html>