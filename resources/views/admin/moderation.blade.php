@extends('layouts.admin')

@section('title', 'Content Moderation - UTHM Bulletin Board System')
@section('page_title', 'Content Moderation')
@section('page_subtitle', 'Review and moderate reported announcements')

@push('styles')
<style>
    .badge {
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
    }
    
    .report-card {
        transition: all 0.3s ease;
    }
    
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .status-resolved {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-dismissed {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .priority-high {
        border-left-color: #dc2626;
    }
    
    .priority-medium {
        border-left-color: #f59e0b;
    }
    
    .priority-low {
        border-left-color: #10b981;
    }
</style>
@endpush

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Pending Reports</p>
                <p id="pendingReportsStat" class="text-3xl font-bold text-yellow-600">0</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Banned / Resolved</p>
                <p id="resolvedReportsStat" class="text-3xl font-bold text-green-600">0</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Dismissed Reports</p>
                <p id="dismissedReportsStat" class="text-3xl font-bold text-red-600">0</p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Reports</p>
                <p id="totalReportsStat" class="text-3xl font-bold text-purple-600">0</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-flag text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
            <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">All Reports</option>
                <option value="pending">Pending</option>
                <option value="resolved">Resolved</option>
                <option value="dismissed">Dismissed</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
            <select id="priorityFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">All Priorities</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" id="searchInput" placeholder="Search by title or reporter..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end">
            <button id="searchBtn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
        </div>
    </div>
</div>

<!-- Reports List -->
<div id="reportsContainer" class="space-y-4">
    <div class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
        <p class="text-gray-500">Loading reports...</p>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Review Report</h3>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="reportDetails" class="space-y-4">
            <!-- Dynamic content will be loaded here -->
        </div>
        
        <div class="flex justify-end gap-3 pt-4 border-t mt-4">
            <button onclick="closeReviewModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button id="dismissBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Dismiss Report</button>
            <button id="banBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ban Post</button>
        </div>
    </div>
</div>

<!-- Action Modal (Delete/Block) -->
<div id="actionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="actionModalTitle" class="text-lg font-bold text-gray-900">Confirm Action</h3>
            <button onclick="closeActionModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <p id="actionModalMessage" class="text-gray-600 mb-4">Are you sure you want to perform this action?</p>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
            <textarea id="actionReason" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter reason for this action..."></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeActionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button id="confirmActionBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Confirm</button>
        </div>
    </div>
</div>

<script>
    let currentReportId = null;
    let currentAction = null;

    // Load reports on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadReports();
        loadStatistics();
        document.getElementById('searchBtn')?.addEventListener('click', function() {
            loadReports();
            loadStatistics();
        });
        document.getElementById('statusFilter')?.addEventListener('change', function() {
            loadReports();
            loadStatistics();
        });
        document.getElementById('priorityFilter')?.addEventListener('change', function() {
            loadReports();
            loadStatistics();
        });
    });

    function loadStatistics() {
        fetch('/api/admin/reports/statistics')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('pendingReportsStat').textContent = data.data.pending || 0;
                    document.getElementById('resolvedReportsStat').textContent = data.data.resolved || 0;
                    document.getElementById('dismissedReportsStat').textContent = data.data.dismissed || 0;
                    document.getElementById('totalReportsStat').textContent = data.data.total || 0;
                }
            })
            .catch(error => console.error('Error loading statistics:', error));
    }

    function loadReports() {
        const status = document.getElementById('statusFilter')?.value || 'all';
        const priority = document.getElementById('priorityFilter')?.value || 'all';
        const search = document.getElementById('searchInput')?.value || '';

        fetch(`/api/admin/reports?status=${status}&priority=${priority}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('reportsContainer');
                if (!container) return;
                
                container.innerHTML = '';

                if (data.success && data.data && data.data.length > 0) {
                    data.data.forEach(report => {
                        const priorityClass = report.priority === 'high' ? 'priority-high' : (report.priority === 'medium' ? 'priority-medium' : 'priority-low');
                        const statusClass = report.status === 'pending' ? 'status-pending' : (report.status === 'resolved' ? 'status-resolved' : 'status-dismissed');
                        const statusText = report.status === 'pending' ? 'Pending' : (report.status === 'resolved' ? 'Resolved' : 'Dismissed');
                        
                        const card = `
                            <div class="report-card bg-white rounded-lg shadow border-l-4 ${priorityClass} overflow-hidden">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="text-lg font-bold text-gray-900">${escapeHtml(report.announcement_title)}</h3>
                                                <span class="badge ${statusClass}">${statusText}</span>
                                                <span class="badge bg-gray-100 text-gray-800">Priority: ${report.priority}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">
                                                <i class="fas fa-user mr-2"></i>Reported by: ${escapeHtml(report.reporter_name)}
                                            </p>
                                            <p class="text-sm text-gray-600 mb-4">
                                                <i class="fas fa-calendar-alt mr-2"></i>${new Date(report.created_at).toLocaleString()}
                                            </p>
                                            <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                                <p class="text-sm font-medium text-gray-700 mb-1">Report Reason:</p>
                                                <p class="text-sm text-gray-600">${escapeHtml(report.reason)}</p>
                                            </div>
                                            ${report.status === 'pending' ? `
                                                <div class="flex gap-2">
                                                    <button onclick="viewReport(${report.id})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                                                        <i class="fas fa-eye mr-1"></i> Review
                                                    </button>
                                                    <button onclick="takeAction(${report.id}, 'dismiss')" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm">
                                                        <i class="fas fa-times mr-1"></i> Dismiss
                                                    </button>
                                                    <button onclick="takeAction(${report.id}, 'ban')" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">
                                                        <i class="fas fa-ban mr-1"></i> Ban Post
                                                    </button>
                                                </div>
                                            ` : `
                                                <div class="flex gap-2">
                                                    <button onclick="viewReport(${report.id})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                                                        <i class="fas fa-eye mr-1"></i> View Details
                                                    </button>
                                                    ${report.resolution_note ? `
                                                        <span class="text-sm text-gray-500 self-center">
                                                            <i class="fas fa-info-circle mr-1"></i>${escapeHtml(report.resolution_note)}
                                                        </span>
                                                    ` : ''}
                                                </div>
                                            `}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.innerHTML += card;
                    });
                } else {
                    container.innerHTML = `
                        <div class="text-center py-12 bg-white rounded-lg shadow">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No reports found</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading reports:', error);
                document.getElementById('reportsContainer').innerHTML = '<div class="text-center py-12 bg-white rounded-lg shadow"><p class="text-red-500">Error loading reports. Please try again.</p></div>';
            });
    }

    function viewReport(reportId) {
        fetch(`/api/admin/reports/${reportId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const report = data.data;
                    const detailsDiv = document.getElementById('reportDetails');
                    detailsDiv.innerHTML = `
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold text-gray-900 mb-2">Announcement Details</h4>
                                <p class="text-sm text-gray-700 mb-2"><strong>Title:</strong> ${escapeHtml(report.announcement_title)}</p>
                                <p class="text-sm text-gray-700 mb-2"><strong>Content:</strong> ${escapeHtml(report.announcement_content?.substring(0, 500))}${report.announcement_content?.length > 500 ? '...' : ''}</p>
                                <p class="text-sm text-gray-700 mb-2"><strong>Author:</strong> ${escapeHtml(report.announcement_author)}</p>
                                ${report.announcement_is_banned ? '<p class="text-sm text-red-600 font-medium"><i class="fas fa-ban mr-1"></i>This post is already banned</p>' : ''}
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h4 class="font-bold text-gray-900 mb-2">Report Details</h4>
                                <p class="text-sm text-gray-700 mb-2"><strong>Reported by:</strong> ${escapeHtml(report.reporter_name)}</p>
                                <p class="text-sm text-gray-700 mb-2"><strong>Reported on:</strong> ${new Date(report.created_at).toLocaleString()}</p>
                                <p class="text-sm text-gray-700 mb-2"><strong>Reason:</strong> ${escapeHtml(report.reason)}</p>
                                <p class="text-sm text-gray-700"><strong>Priority:</strong> ${report.priority}</p>
                            </div>
                            
                            ${report.resolution_note ? `
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-bold text-gray-900 mb-2">Resolution Note</h4>
                                    <p class="text-sm text-gray-700">${escapeHtml(report.resolution_note)}</p>
                                    <p class="text-xs text-gray-500 mt-2">Resolved by: ${escapeHtml(report.resolved_by_name)} on ${new Date(report.resolved_at).toLocaleString()}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    document.getElementById('reviewModal').classList.remove('hidden');
                    
                    // Set up action buttons
                    const dismissBtn = document.getElementById('dismissBtn');
                    const banBtn = document.getElementById('banBtn');
                    
                    if (report.status === 'pending') {
                        dismissBtn.onclick = () => {
                            closeReviewModal();
                            takeAction(reportId, 'dismiss');
                        };
                        banBtn.onclick = () => {
                            closeReviewModal();
                            takeAction(reportId, 'ban');
                        };
                        dismissBtn.classList.remove('hidden');
                        banBtn.classList.remove('hidden');
                    } else {
                        dismissBtn.classList.add('hidden');
                        banBtn.classList.add('hidden');
                    }
                }
            })
            .catch(error => console.error('Error loading report details:', error));
    }

    function takeAction(reportId, action) {
        currentReportId = reportId;
        currentAction = action;
        
        const modal = document.getElementById('actionModal');
        const title = document.getElementById('actionModalTitle');
        const message = document.getElementById('actionModalMessage');
        const confirmBtn = document.getElementById('confirmActionBtn');
        
        if (action === 'dismiss') {
            title.textContent = 'Dismiss Report';
            message.textContent = 'Are you sure you want to dismiss this report? The announcement will remain unchanged.';
            confirmBtn.className = 'px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700';
            confirmBtn.textContent = 'Dismiss Report';
        } else if (action === 'ban') {
            title.textContent = 'Ban Announcement';
            message.textContent = 'This will hide the announcement from all users. All pending reports for this post will be marked resolved.';
            confirmBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
            confirmBtn.textContent = 'Ban Post';
        }
        
        modal.classList.remove('hidden');
    }

    function confirmAction() {
        const reason = document.getElementById('actionReason').value;
        const url = `/api/admin/reports/${currentReportId}/${currentAction}`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeActionModal();
                closeReviewModal();
                loadReports();
                loadStatistics();
            } else {
                showNotification(data.message || 'Error performing action', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error performing action', 'error');
        });
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
        document.getElementById('actionReason').value = '';
        currentReportId = null;
        currentAction = null;
    }

    document.getElementById('confirmActionBtn')?.addEventListener('click', confirmAction);

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 transition-opacity duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${escapeHtml(message)}</span>
            </div>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const reviewModal = document.getElementById('reviewModal');
        const actionModal = document.getElementById('actionModal');
        if (event.target === reviewModal) closeReviewModal();
        if (event.target === actionModal) closeActionModal();
    });
</script>
@endsection