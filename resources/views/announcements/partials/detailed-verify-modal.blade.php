<!-- Detailed Verification Modal -->
<div id="detailedVerifyModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 overflow-y-auto h-full w-full z-50 hidden transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-0 border w-full max-w-5xl shadow-2xl rounded-xl bg-white overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="detailedVerifyModalContainer">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-blue-800 px-6 py-4 flex justify-between items-center text-white">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shield-alt text-2xl text-indigo-200"></i>
                <div>
                    <h3 class="text-lg font-bold">Review & Verify Announcement</h3>
                    <p class="text-xs text-indigo-100/80">Moderate content safety, verify details, and customize post options</p>
                </div>
            </div>
            <button onclick="closeDetailedVerifyModal()" class="text-indigo-200 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/10">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Loading Spinner overlay -->
        <div id="verifyModalSpinner" class="absolute inset-0 bg-white/85 flex flex-col items-center justify-center z-10 transition-opacity">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-3"></div>
            <p class="text-gray-500 font-semibold text-sm">Fetching announcement metadata...</p>
        </div>

        <div class="flex flex-col md:flex-row min-h-[480px]">
            
            <!-- Left Side: Content Preview & Safety -->
            <div class="md:w-3/5 p-6 border-r border-gray-100 bg-gray-50/50 max-h-[640px] overflow-y-auto">
                <div class="space-y-6">
                    
                    <!-- Title -->
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Title</span>
                        <h4 id="verifyPreviewTitle" class="text-xl font-bold text-gray-900 leading-tight"></h4>
                    </div>

                    <!-- Author & Info Box -->
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold" id="verifyPreviewAuthorInitial">
                                U
                            </div>
                            <div>
                                <h5 id="verifyPreviewAuthor" class="font-bold text-gray-900 text-sm"></h5>
                                <p class="text-xs text-gray-500" id="verifyPreviewAuthorRole"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400" id="verifyPreviewDate"></p>
                            <p class="text-xs font-semibold text-indigo-600 mt-0.5" id="verifyPreviewAuthorId"></p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Body Content</span>
                        <div id="verifyPreviewContent" class="bg-white p-4 rounded-xl border border-gray-100 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap max-h-48 overflow-y-auto shadow-inner">
                        </div>
                    </div>

                    <!-- Image Preview -->
                    <div id="verifyImageContainer" class="hidden">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Attached Cover Image</span>
                        <div class="rounded-xl border border-gray-100 overflow-hidden bg-black flex items-center justify-center max-h-40">
                            <img id="verifyPreviewImage" src="" alt="Cover Image" class="max-h-40 object-contain">
                        </div>
                    </div>

                    <!-- Content Moderation Check Card -->
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-robot text-purple-600"></i>
                                <span class="font-bold text-sm text-gray-800">Content Moderation Audit</span>
                            </div>
                            <span id="verifySafetyBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold"></span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Toxicity Risk Score</span>
                                    <span id="verifyToxicityText" class="font-semibold">0%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div id="verifyToxicityBar" class="h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 leading-normal" id="verifyModerationDetails">
                                Clean text check bypassed or no safety issues flagged.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Verification Options -->
            <div class="md:w-2/5 p-6 flex flex-col justify-between bg-white max-h-[640px] overflow-y-auto">
                <div>
                    <!-- Decision Tab Selector -->
                    <div class="mb-6">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Select Decision</span>
                        <div class="flex p-1 bg-gray-100 rounded-lg">
                            <button type="button" onclick="setDecision('approve')" id="btnTabApprove" class="flex-1 py-2 px-3 text-sm font-semibold rounded-md transition-all flex items-center justify-center space-x-2 bg-white text-emerald-700 shadow-sm">
                                <i class="fas fa-check"></i>
                                <span>Approve & Publish</span>
                            </button>
                            <button type="button" onclick="setDecision('reject')" id="btnTabReject" class="flex-1 py-2 px-3 text-sm font-semibold rounded-md transition-all flex items-center justify-center space-x-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-times"></i>
                                <span>Reject Request</span>
                            </button>
                        </div>
                    </div>

                    <!-- Input form area -->
                    <form id="verifyDecisionForm" class="space-y-4">
                        <input type="hidden" id="verifyAnnouncementId">
                        <input type="hidden" id="verifyDecisionAction" value="approve">

                        <!-- Approval Options (Show/Hide) -->
                        <div id="approvalOptionsBlock" class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Category</label>
                                    <select id="verifyCategory" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                        <option value="general">General</option>
                                        <option value="academic">Academic</option>
                                        <option value="events">Events</option>
                                        <option value="club">Club</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Priority</label>
                                    <select id="verifyPriority" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                        <option value="normal">Normal</option>
                                        <option value="important">Important</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Badges switch grid -->
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 grid grid-cols-2 gap-4">
                                <label class="flex items-center cursor-pointer select-none">
                                    <div class="relative">
                                        <input type="checkbox" id="verifyIsOfficial" class="sr-only">
                                        <div class="block bg-gray-300 w-9 h-5 rounded-full toggle-bg transition-colors"></div>
                                        <div class="dot absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform"></div>
                                    </div>
                                    <span class="ml-2 text-xs font-semibold text-gray-700">Official Notice</span>
                                </label>
                                
                                <label class="flex items-center cursor-pointer select-none">
                                    <div class="relative">
                                        <input type="checkbox" id="verifyIsFeatured" class="sr-only">
                                        <div class="block bg-gray-300 w-9 h-5 rounded-full toggle-bg transition-colors"></div>
                                        <div class="dot absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform"></div>
                                    </div>
                                    <span class="ml-2 text-xs font-semibold text-gray-700">Featured Post</span>
                                </label>
                            </div>

                            <!-- Approval Notes / Feedback to author -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Approval Feedback (Optional)</label>
                                <textarea id="verifyApprovalNotes" rows="3" placeholder="Add optional comments or feedback for the author..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" maxlength="500"></textarea>
                                <p class="text-[10px] text-gray-400 mt-1">This feedback will be visible to the author in their notifications.</p>
                            </div>
                        </div>

                        <!-- Rejection Options (Show/Hide) -->
                        <div id="rejectionOptionsBlock" class="space-y-4 hidden">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Rejection Category *</label>
                                <select id="verifyRejectionCategory" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                                    <option value="Incomplete details">Incomplete details</option>
                                    <option value="Inappropriate content">Inappropriate content</option>
                                    <option value="Policy violation">Policy violation</option>
                                    <option value="Duplicate post">Duplicate post</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Detailed Rejection Reason *</label>
                                <textarea id="verifyRejectionReason" rows="4" placeholder="Explain why this announcement is being rejected so the author can correct it..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" minlength="5" maxlength="500"></textarea>
                                <p class="text-[10px] text-gray-400 mt-1">Provide clear feedback. The author receives this description in full.</p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center space-x-3 pt-6 border-t border-gray-100 mt-6 shrink-0">
                    <button type="button" onclick="closeDetailedVerifyModal()" class="flex-1 py-2 px-4 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="submitVerification()" id="btnSubmitVerification" class="flex-1 py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Submit Approval
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS toggle animations for toggle switches */
input:checked ~ .toggle-bg {
    background-color: #4f46e5;
}
input:checked ~ .dot {
    transform: translateX(100%);
}
#approvalOptionsBlock input:checked ~ .toggle-bg {
    background-color: #10b981;
}
#rejectionOptionsBlock input:checked ~ .toggle-bg {
    background-color: #ef4444;
}
</style>

<script>
let verifyAnnouncementId = null;

function openDetailedVerifyModal(id) {
    verifyAnnouncementId = id;
    document.getElementById('verifyAnnouncementId').value = id;
    
    // Show modal container and overlay
    const modal = document.getElementById('detailedVerifyModal');
    const modalContainer = document.getElementById('detailedVerifyModalContainer');
    const spinner = document.getElementById('verifyModalSpinner');
    
    modal.classList.remove('hidden');
    // Force reflow
    modal.offsetHeight;
    modal.classList.remove('opacity-0');
    modalContainer.classList.remove('scale-95', 'opacity-0');
    spinner.style.opacity = '1';
    spinner.classList.remove('hidden');
    
    // Fetch details
    fetch(`/announcements/${id}/details`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch details');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const item = data.data;
                
                // Populate content preview
                document.getElementById('verifyPreviewTitle').textContent = item.title;
                document.getElementById('verifyPreviewContent').textContent = item.content;
                document.getElementById('verifyPreviewAuthor').textContent = item.author.name;
                document.getElementById('verifyPreviewAuthorRole').textContent = item.author.role.charAt(0).toUpperCase() + item.author.role.slice(1);
                document.getElementById('verifyPreviewAuthorInitial').textContent = item.author.name.charAt(0).toUpperCase();
                document.getElementById('verifyPreviewAuthorId').textContent = `ID: ${item.author.uthm_id}`;
                document.getElementById('verifyPreviewDate').textContent = new Date(item.created_at).toLocaleDateString(undefined, {
                    month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });
                
                // Image handling
                const imageContainer = document.getElementById('verifyImageContainer');
                if (item.image_url) {
                    document.getElementById('verifyPreviewImage').src = item.image_url;
                    imageContainer.classList.remove('hidden');
                } else {
                    imageContainer.classList.add('hidden');
                }
                
                // Content Moderation check
                const safetyBadge = document.getElementById('verifySafetyBadge');
                const toxicityText = document.getElementById('verifyToxicityText');
                const toxicityBar = document.getElementById('verifyToxicityBar');
                const moderationDetails = document.getElementById('verifyModerationDetails');
                
                const toxScore = item.moderation.toxicity_score !== null ? parseFloat(item.moderation.toxicity_score) : 0;
                const toxPct = Math.round(toxScore * 100);
                toxicityText.textContent = `${toxPct}%`;
                toxicityBar.style.width = `${toxPct}%`;
                
                // Style safety meter by toxicity
                if (toxPct > 50) {
                    toxicityBar.className = 'h-2.5 rounded-full bg-red-500 transition-all duration-500';
                } else if (toxPct > 20) {
                    toxicityBar.className = 'h-2.5 rounded-full bg-amber-500 transition-all duration-500';
                } else {
                    toxicityBar.className = 'h-2.5 rounded-full bg-green-500 transition-all duration-500';
                }
                
                if (item.moderation.flagged) {
                    safetyBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800';
                    safetyBadge.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Flagged';
                    moderationDetails.innerHTML = `<span class="text-red-600 font-semibold">Toxicity flagged.</span> Reason: ${item.moderation.reason || 'Flagged by content filtering api'}`;
                } else {
                    safetyBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800';
                    safetyBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Safe';
                    moderationDetails.innerHTML = 'AI analysis indicates the announcement content matches UTHM community guidelines.';
                }
                
                // Form setup
                document.getElementById('verifyCategory').value = item.category;
                document.getElementById('verifyPriority').value = item.priority;
                document.getElementById('verifyIsOfficial').checked = item.is_official;
                document.getElementById('verifyIsFeatured').checked = item.is_featured;
                document.getElementById('verifyApprovalNotes').value = '';
                document.getElementById('verifyRejectionReason').value = '';
                document.getElementById('verifyRejectionCategory').value = 'Incomplete details';
                
                // Default back to approve tab
                setDecision('approve');
            } else {
                showToastNotification('Failed to load announcement details', 'error');
                closeDetailedVerifyModal();
            }
        })
        .catch(err => {
            console.error(err);
            showToastNotification('Error fetching announcement metadata', 'error');
            closeDetailedVerifyModal();
        })
        .finally(() => {
            spinner.classList.add('hidden');
        });
}

function closeDetailedVerifyModal() {
    const modal = document.getElementById('detailedVerifyModal');
    const modalContainer = document.getElementById('detailedVerifyModalContainer');
    
    modalContainer.classList.add('scale-95', 'opacity-0');
    modal.classList.add('opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        verifyAnnouncementId = null;
    }, 200);
}

function setDecision(action) {
    document.getElementById('verifyDecisionAction').value = action;
    
    const tabApprove = document.getElementById('btnTabApprove');
    const tabReject = document.getElementById('btnTabReject');
    const approvalBlock = document.getElementById('approvalOptionsBlock');
    const rejectionBlock = document.getElementById('rejectionOptionsBlock');
    const submitBtn = document.getElementById('btnSubmitVerification');
    
    if (action === 'approve') {
        tabApprove.className = 'flex-1 py-2 px-3 text-sm font-semibold rounded-md transition-all flex items-center justify-center space-x-2 bg-white text-emerald-700 shadow-sm';
        tabReject.className = 'flex-1 py-2 px-3 text-sm font-semibold rounded-md transition-all flex items-center justify-center space-x-2 text-gray-600 hover:text-gray-900';
        approvalBlock.classList.remove('hidden');
        rejectionBlock.classList.add('hidden');
        submitBtn.textContent = 'Submit Approval';
        submitBtn.className = 'flex-1 py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm';
    } else {
        tabApprove.className = 'flex-1 py-2 px-3 text-sm font-semibold rounded-md transition-all flex items-center justify-center space-x-2 text-gray-600 hover:text-gray-900';
        tabReject.className = 'flex-1 py-2 px-3 text-sm font-semibold rounded-md transition-all flex items-center justify-center space-x-2 bg-white text-red-700 shadow-sm';
        approvalBlock.classList.add('hidden');
        rejectionBlock.classList.remove('hidden');
        submitBtn.textContent = 'Submit Rejection';
        submitBtn.className = 'flex-1 py-2 px-4 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm';
    }
}

function submitVerification() {
    if (!verifyAnnouncementId) return;
    
    const action = document.getElementById('verifyDecisionAction').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        showToastNotification('Security token not found. Please refresh the page.', 'error');
        return;
    }
    
    // Disable submit button and show status
    const submitBtn = document.getElementById('btnSubmitVerification');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    let url = `/announcements/${verifyAnnouncementId}/approve`;
    let bodyData = {};
    
    if (action === 'approve') {
        url = `/announcements/${verifyAnnouncementId}/approve`;
        bodyData = {
            category: document.getElementById('verifyCategory').value,
            priority: document.getElementById('verifyPriority').value,
            is_official: document.getElementById('verifyIsOfficial').checked ? 1 : 0,
            is_featured: document.getElementById('verifyIsFeatured').checked ? 1 : 0,
            approval_notes: document.getElementById('verifyApprovalNotes').value.trim()
        };
    } else {
        url = `/announcements/${verifyAnnouncementId}/reject`;
        const reason = document.getElementById('verifyRejectionReason').value.trim();
        if (!reason || reason.length < 5) {
            showToastNotification('Please provide a rejection reason (minimum 5 characters).', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            return;
        }
        bodyData = {
            reason: reason,
            rejection_category: document.getElementById('verifyRejectionCategory').value
        };
    }
    
    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(bodyData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(json => {
                throw new Error(json.message || `HTTP ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToastNotification(data.message || 'Action completed successfully!', 'success');
            closeDetailedVerifyModal();
            // Reload the view or table list
            setTimeout(() => {
                if (typeof loadAnnouncements === 'function') {
                    loadAnnouncements(); // Admin panel list refresh
                } else {
                    location.reload(); // Staff dashboard refresh
                }
            }, 1000);
        } else {
            showToastNotification(data.message || 'Action failed', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToastNotification(err.message || 'An error occurred during submission', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function showToastNotification(message, type = 'success') {
    // If showNotification exists on the page (admin page), use it.
    if (typeof showNotification === 'function') {
        showNotification(message, type);
        return;
    }
    
    // Otherwise use page's showToast (staff dashboard, index pages)
    if (typeof showToast === 'function') {
        showToast(message, type);
        return;
    }
    
    // Default fallback toast creator
    let toast = document.getElementById('verifyToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'verifyToast';
        toast.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;transition:opacity 0.3s;';
        document.body.appendChild(toast);
    }
    
    const bgClass = type === 'error' ? 'bg-red-600' : 'bg-green-600';
    const iconClass = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
    
    toast.innerHTML = `
        <div class="${bgClass} text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <i class="fas ${iconClass}"></i>
            <span class="text-sm font-semibold">${message}</span>
        </div>
    `;
    
    toast.style.opacity = '1';
    setTimeout(() => {
        toast.style.opacity = '0';
    }, 3000);
}

// Close modal when clicking outside the container box
document.getElementById('detailedVerifyModal').addEventListener('click', function(e) {
    const modalContainer = document.getElementById('detailedVerifyModalContainer');
    if (!modalContainer.contains(e.target)) {
        closeDetailedVerifyModal();
    }
});
</script>
