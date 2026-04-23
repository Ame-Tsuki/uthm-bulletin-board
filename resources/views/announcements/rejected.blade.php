@extends('layouts.app')

@section('title', 'Rejected Announcements')
@section('page_title', 'Rejected Announcements')
@section('page_subtitle', 'View and manage rejected announcements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Rejected Announcements</h1>
                <p class="text-gray-600 mt-2">Review announcements that have been rejected and the reasons for rejection.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('announcements.verification-queue') }}" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-clock mr-2"></i>
                    Pending Queue
                </a>
                <a href="{{ route('announcements.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Announcements
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Rejected</p>
                    <p class="text-3xl font-bold text-red-600">{{ $totalRejected }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rejected This Month</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $rejectedThisMonth }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-calendar-alt text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rejection Rate</p>
                    @php
                        $total = \App\Models\Announcement::count();
                        $rejectionRate = $total > 0 ? round(($totalRejected / $total) * 100, 1) : 0;
                    @endphp
                    <p class="text-3xl font-bold text-purple-600">{{ $rejectionRate }}%</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-900">Rejected by Category</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $rejectedByCategory['urgent'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Urgent</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ $rejectedByCategory['academic'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Academic</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">{{ $rejectedByCategory['events'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Events</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600">{{ $rejectedByCategory['general'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">General</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">{{ $rejectedByCategory['important'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Important</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6">
            <form method="GET" action="{{ route('announcements.rejected') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" placeholder="Title, content, or reason..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="">All Categories</option>
                        <option value="urgent" {{ request('category') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="academic" {{ request('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="events" {{ request('category') == 'events' ? 'selected' : '' }}>Events</option>
                        <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="important" {{ request('category') == 'important' ? 'selected' : '' }}>Important</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                    <input type="text" name="author" placeholder="Author name..." 
                           value="{{ request('author') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <div class="flex items-end">
                    <div class="flex gap-2 w-full">
                        <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('announcements.rejected') }}" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 text-center">
                            <i class="fas fa-sync-alt mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rejected Announcements List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Rejected Announcements</h2>
                <span class="text-sm text-gray-600">Total: {{ $announcements->total() }}</span>
            </div>
        </div>
        
        @if($announcements->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($announcements as $announcement)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-2 mb-3">
                                    <!-- Category Badge -->
                                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                                        @if($announcement->category == 'urgent') bg-red-100 text-red-800
                                        @elseif($announcement->category == 'academic') bg-blue-100 text-blue-800
                                        @elseif($announcement->category == 'events') bg-purple-100 text-purple-800
                                        @elseif($announcement->category == 'important') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($announcement->category) }}
                                    </span>
                                    
                                    <!-- Rejected Badge -->
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $announcement->title }}</h3>
                                
                                <p class="text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit($announcement->content, 200) }}
                                </p>
                                
                                <!-- Rejection Reason -->
                                <div class="mb-4 p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-semibold text-red-700">Rejection Reason:</p>
                                            <p class="text-sm text-red-600">{{ $announcement->rejection_reason ?? 'No reason provided' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Author Info -->
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-xs font-bold">{{ strtoupper(substr($announcement->author->name ?? 'A', 0, 1)) }}</span>
                                    </div>
                                    <span>{{ $announcement->author->name ?? 'Unknown' }}</span>
                                    @if($announcement->author)
                                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full badge-{{ $announcement->author->role }}">
                                            {{ ucfirst($announcement->author->role) }}
                                        </span>
                                    @endif
                                    <span class="ml-4">
                                        <i class="far fa-calendar-alt mr-1"></i> Submitted: {{ $announcement->created_at->format('M d, Y') }}
                                    </span>
                                    @if($announcement->rejected_at)
                                        <span class="ml-4">
                                            <i class="fas fa-ban mr-1 text-red-500"></i> Rejected: {{ \Carbon\Carbon::parse($announcement->rejected_at)->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('announcements.show', $announcement) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-eye mr-2"></i> View
                                </a>
                                
                                @if($announcement->author_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'staff']))
                                    <a href="{{ route('announcements.edit', $announcement) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                        <i class="fas fa-edit mr-2"></i> Edit & Resubmit
                                    </a>
                                @endif
                                
                                @if(in_array(auth()->user()->role, ['admin', 'staff']))
                                    <button onclick="resubmitForApproval({{ $announcement->id }})" 
                                            class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700">
                                        <i class="fas fa-paper-plane mr-2"></i> Resubmit for Approval
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $announcements->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-check-circle text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No Rejected Announcements</h3>
                <p class="text-gray-600">All announcements have been approved or are pending verification.</p>
                <div class="mt-6">
                    <a href="{{ route('announcements.verification-queue') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-clock mr-2"></i>
                        Go to Verification Queue
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Resubmit Modal -->
<div id="resubmitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Resubmit Announcement</h3>
            <button onclick="closeResubmitModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <p class="text-gray-600 mb-4">Are you sure you want to resubmit this announcement for approval?</p>
        <p class="text-sm text-gray-500 mb-6">The announcement will be sent back to the verification queue for review.</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeResubmitModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="confirmResubmit()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Yes, Resubmit
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let resubmitAnnouncementId = null;
    
    function resubmitForApproval(id) {
        resubmitAnnouncementId = id;
        document.getElementById('resubmitModal').classList.remove('hidden');
    }
    
    function closeResubmitModal() {
        document.getElementById('resubmitModal').classList.add('hidden');
        resubmitAnnouncementId = null;
    }
    
    function confirmResubmit() {
        if (!resubmitAnnouncementId) return;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(`/announcements/${resubmitAnnouncementId}/resubmit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Announcement resubmitted for approval!', 'success');
                closeResubmitModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Error resubmitting announcement', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error resubmitting announcement', 'error');
        });
    }
    
    function showToast(message, type = 'success') {
        // Check if toast container exists, if not create it
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'fixed bottom-4 right-4 z-50 hidden';
            document.body.appendChild(toast);
        }
        
        toast.innerHTML = `
            <div class="${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
</script>
@endpush

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .badge-admin { background-color: #dc2626; color: white; }
    .badge-staff { background-color: #2563eb; color: white; }
    .badge-student { background-color: #059669; color: white; }
    .badge-guest { background-color: #6b7280; color: white; }
</style>
@endpush
@endsection