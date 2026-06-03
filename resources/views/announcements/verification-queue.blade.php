@extends('layouts.admin')

@section('title', 'Verification Queue - UTHM Bulletin')
@section('page_title', 'Verification Queue')
@section('page_subtitle', 'Review pending announcements for publication')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold">Pending Announcements</h2>
            <p class="text-sm text-gray-500">Approve or reject announcements submitted for verification.</p>
        </div>
        <div>
            <input id="searchInput" type="text" placeholder="Search title or content..." class="px-3 py-2 border rounded-lg" />
            <button id="searchBtn" class="ml-2 px-4 py-2 bg-uthm-blue text-white rounded-lg">Search</button>
        </div>
    </div>
</div>

<div id="queueContainer" class="space-y-4">
    @forelse($announcements as $announcement)
    <div id="announcement-{{ $announcement->id }}" class="bg-white rounded-lg shadow p-6 flex justify-between items-start">
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
            <p class="text-xs text-gray-500">By {{ $announcement->author->name ?? 'Unknown' }} • {{ $announcement->created_at->diffForHumans() }}</p>
            <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ Str::limit($announcement->content, 300) }}</p>
        </div>
        <div class="ms-4 flex flex-col items-end gap-2">
            <button class="approveBtn inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg" data-id="{{ $announcement->id }}">
                <i class="fas fa-check mr-2"></i> Approve
            </button>
            <button class="rejectBtn inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg" data-id="{{ $announcement->id }}">
                <i class="fas fa-times mr-2"></i> Reject
            </button>
            <a href="{{ route('announcements.show', $announcement->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">View</a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-4"></i>
        <p class="text-sm">No pending announcements found.</p>
    </div>
    @endforelse

    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
</div>

<!-- Reject reason modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-start justify-center pt-24 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-2">Reject Announcement</h3>
        <p class="text-sm text-gray-600 mb-4">Provide a reason for rejecting this announcement.</p>
        <textarea id="rejectReason" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Reason..."></textarea>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="closeRejectModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
            <button id="confirmRejectBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg">Reject</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = document.querySelector('meta[name="csrf-token"]').content;

        document.querySelectorAll('.approveBtn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                if (!confirm('Approve this announcement?')) return;
                try {
                    const res = await fetch(`/announcements/${id}/approve`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        // remove item from list
                        document.getElementById('announcement-' + id)?.remove();
                        alert(data.message || 'Announcement approved');
                    } else {
                        alert(data.message || 'Failed to approve');
                    }
                } catch (e) {
                    alert('Error approving announcement');
                }
            });
        });

        let currentRejectId = null;
        document.querySelectorAll('.rejectBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentRejectId = this.dataset.id;
                openRejectModal();
            });
        });

        document.getElementById('confirmRejectBtn')?.addEventListener('click', async function() {
            const reason = document.getElementById('rejectReason').value.trim();
            if (!reason) { alert('Please provide a reason'); return; }
            if (!currentRejectId) return;
            try {
                const res = await fetch(`/announcements/${currentRejectId}/reject`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ reason })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    document.getElementById('announcement-' + currentRejectId)?.remove();
                    closeRejectModal();
                    alert(data.message || 'Announcement rejected');
                } else {
                    alert(data.message || 'Failed to reject');
                }
            } catch (e) {
                alert('Error rejecting announcement');
            }
        });

        window.openRejectModal = function() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        window.closeRejectModal = function() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectReason').value = '';
            currentRejectId = null;
        }

        // Search
        document.getElementById('searchBtn')?.addEventListener('click', function() {
            const q = document.getElementById('searchInput').value.trim();
            const url = new URL(window.location.href);
            if (q) url.searchParams.set('search', q); else url.searchParams.delete('search');
            window.location.href = url.toString();
        });
    });
</script>
@endpush
