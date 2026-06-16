<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Settings - UTHM Bulletin Board</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .settings-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: var(--primary, #0056a6);
        }
        .toggle-checkbox:checked + .toggle-label .toggle-dot {
            transform: translateX(100%);
        }
        .danger-zone {
            border: 2px solid #fee2e2;
            background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);
        }

        .modal-overlay {
            backdrop-filter: blur(4px);
        }
        .theme-option {
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }
        .theme-option:hover {
            transform: scale(1.05);
        }
        .theme-option.active {
            ring: 2px solid var(--primary, #0056a6);
            ring-offset: 2px;
        }

        @media (max-width: 640px) {
            .settings-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="portal-body bg-gray-50" id="appBody">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        @include('layouts.partials.portal-topbar', ['pageTitle' => 'Settings', 'breadcrumb' => 'Account Settings'])

        @include('layouts.partials.portal-content-open')
        
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900" id="pageTitle">Account Settings</h2>
                    <p class="text-sm text-gray-500 mt-1" id="pageSubtitle">Manage your account preferences and security settings</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        <i class="fas fa-check-circle mr-1.5"></i> Profile Active
                    </span>
                </div>
            </div>
        </div>

        <!-- Settings Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Settings -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Profile Section -->
                <div class="portal-card settings-card p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ Auth::user()->name ?? 'User' }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ Auth::user()->email ?? 'No email' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Member since {{ Auth::user()->created_at->format('M d, Y') }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="shrink-0 px-4 py-2 bg-uthm-blue text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-1.5"></i> Edit
                        </a>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="portal-card settings-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Change Password</h4>
                                <p class="text-sm text-gray-500">Update your password regularly for better security</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Secure</span>
                                    <span class="text-xs text-gray-400">Last changed: 30 days ago</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="openPasswordModal()" class="shrink-0 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-edit mr-1.5"></i> Update
                        </button>
                    </div>
                </div>

                <!-- Notifications Section -->
                <div class="portal-card settings-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 shrink-0">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Email Notifications</h4>
                                <p class="text-sm text-gray-500">Receive email updates about announcements and activities</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <span class="text-sm text-gray-500" id="notificationStatus">On</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer toggle-checkbox" checked onchange="toggleNotifications(this)">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-uthm-blue"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Theme Selection -->
                <div class="portal-card settings-card p-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">Theme</h4>
                            <p class="text-sm text-gray-500">Choose your preferred theme color</p>
                            <div class="flex flex-wrap gap-3 mt-3">
                                <button onclick="setTheme('light')" class="theme-option w-12 h-12 rounded-full bg-white border-2 border-gray-300 hover:border-uthm-blue flex items-center justify-center shadow-sm" data-theme="light" id="themeLight">
                                    <i class="fas fa-sun text-yellow-500 text-lg"></i>
                                </button>
                                <button onclick="setTheme('dark')" class="theme-option w-12 h-12 rounded-full bg-gray-900 border-2 border-gray-300 hover:border-uthm-blue flex items-center justify-center shadow-sm" data-theme="dark" id="themeDark">
                                    <i class="fas fa-moon text-white text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <!-- Quick Actions -->
                <div class="portal-card settings-card p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user text-uthm-blue w-5"></i>
                            <span class="text-sm text-gray-700">View Profile</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
                        </a>
                        <a href="{{ route('announcements.my-announcements') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-file-alt text-green-600 w-5"></i>
                            <span class="text-sm text-gray-700">My Announcements</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
                        </a>
                        <a href="{{ route('calendar') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-calendar-alt text-purple-600 w-5"></i>
                            <span class="text-sm text-gray-700">My Calendar</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-question-circle text-blue-600 w-5"></i>
                            <span class="text-sm text-gray-700">Help & Support</span>
                            <i class="fas fa-chevron-right ml-auto text-gray-400 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="portal-card danger-zone p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center text-red-600">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="font-bold text-red-700">Danger Zone</h4>
                    </div>
                    <p class="text-sm text-red-600 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                    <button onclick="openDeleteModal()" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-trash-alt"></i>
                        Delete Account
                    </button>
                </div>

                <!-- Account Info -->
                <div class="portal-card settings-card p-4">
                    <div class="text-xs text-gray-400 space-y-1">
                        <p><i class="fas fa-circle text-green-500 text-[6px] mr-1 align-middle"></i> Account active</p>
                        <p>Role: <span class="font-medium text-gray-600">{{ ucfirst(Auth::user()->role ?? 'User') }}</span></p>
                        <p>UTHM ID: <span class="font-medium text-gray-600">{{ Auth::user()->uthm_id ?? 'N/A' }}</span></p>
                        <p class="mt-2">Version 1.2.1</p>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.partials.portal-content-close')
    </div>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="fixed inset-0 z-50 hidden modal-overlay">
        <div class="fixed inset-0 bg-black/50" onclick="closePasswordModal()"></div>
        <div class="relative top-1/2 -translate-y-1/2 mx-auto p-6 w-full max-w-md bg-white rounded-xl shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Change Password</h3>
                <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="passwordForm" onsubmit="changePassword(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="currentPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="newPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" required minlength="8">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirmPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" required>
                    </div>
                    <div id="passwordError" class="text-red-500 text-sm hidden"></div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closePasswordModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden modal-overlay">
        <div class="fixed inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
        <div class="relative top-1/2 -translate-y-1/2 mx-auto p-6 w-full max-w-md bg-white rounded-xl shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-red-600">Delete Account</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <p class="text-gray-600">Are you sure you want to delete your account? This action cannot be undone.</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-700"><i class="fas fa-exclamation-circle mr-2"></i> All your data including announcements, comments, and groups will be permanently removed.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type your email to confirm</label>
                    <input type="email" id="confirmEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="your@email.com">
                </div>
                <div class="flex gap-3 pt-2">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete Account</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span id="toastMessage">Settings updated successfully!</span>
        </div>
    </div>

    <script>
        // Theme Management
        function setTheme(theme) {
            // Remove active class from all theme options
            document.querySelectorAll('.theme-option').forEach(el => {
                el.classList.remove('active');
                el.style.ring = 'none';
            });
            
            // Add active class to selected theme
            const selected = document.getElementById('theme' + theme.charAt(0).toUpperCase() + theme.slice(1));
            if (selected) {
                selected.classList.add('active');
                selected.style.ring = '2px solid var(--primary, #0056a6)';
                selected.style.ringOffset = '2px';
            }
            
            // Apply theme to document element
            document.documentElement.removeAttribute('data-theme');
            
            // Apply new theme
            if (theme !== 'light') {
                document.documentElement.setAttribute('data-theme', theme);
            }
            
            // Save preference
            localStorage.setItem('userTheme', theme);
            
            showToast('Theme updated to ' + theme.charAt(0).toUpperCase() + theme.slice(1));
        }

        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('userTheme') || 'light';
            
            // Apply theme
            if (savedTheme !== 'light') {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
            
            // Highlight selected theme
            const themeId = 'theme' + savedTheme.charAt(0).toUpperCase() + savedTheme.slice(1);
            const selected = document.getElementById(themeId);
            if (selected) {
                selected.classList.add('active');
                selected.style.ring = '2px solid var(--primary, #0056a6)';
                selected.style.ringOffset = '2px';
            }
        });

        // Toggle Notifications
        function toggleNotifications(checkbox) {
            const status = document.getElementById('notificationStatus');
            if (checkbox.checked) {
                status.textContent = 'On';
                status.className = 'text-sm text-green-600';
                showToast('Notifications enabled');
            } else {
                status.textContent = 'Off';
                status.className = 'text-sm text-red-600';
                showToast('Notifications disabled');
            }
        }

        // Password Modal
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('passwordError').classList.add('hidden');
        }

        function changePassword(event) {
            event.preventDefault();
            const current = document.getElementById('currentPassword').value;
            const newPw = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;
            const error = document.getElementById('passwordError');

            if (newPw.length < 8) {
                error.textContent = 'Password must be at least 8 characters';
                error.classList.remove('hidden');
                return;
            }

            if (newPw !== confirm) {
                error.textContent = 'Passwords do not match';
                error.classList.remove('hidden');
                return;
            }

            error.classList.add('hidden');
            showToast('Password updated successfully!');
            closePasswordModal();
            document.getElementById('passwordForm').reset();
        }

        // Delete Account Modal
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('confirmEmail').value = '';
        }

        function confirmDelete() {
            const email = document.getElementById('confirmEmail').value;
            const userEmail = '{{ Auth::user()->email }}';

            if (email !== userEmail) {
                showToast('Email does not match', 'error');
                return;
            }

            if (confirm('Are you absolutely sure you want to delete your account?')) {
                showToast('Account deletion request submitted');
                closeDeleteModal();
            }
        }

        // Toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastDiv = toast.querySelector('div');

            toastMessage.textContent = message;
            if (type === 'error') {
                toastDiv.className = 'bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2';
            } else {
                toastDiv.className = 'bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2';
            }

            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Auto-close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePasswordModal();
                closeDeleteModal();
            }
        });
    </script>

    @include('layouts.partials.portal-scripts')
</body>
</html>