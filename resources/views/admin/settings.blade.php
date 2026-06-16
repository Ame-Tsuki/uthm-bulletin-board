@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'System Settings')
@section('page_subtitle', 'Configure system-wide settings and preferences')

@section('content')
<div class="grid grid-cols-1">
        <!-- Settings Tabs -->
        <div class="flex gap-4 mb-8 border-b border-gray-200">
            <button onclick="showTab('general')" class="tab-btn py-3 px-4 text-gray-700 font-medium border-b-2 border-transparent hover:border-blue-600 active" data-tab="general">
                General
            </button>
            <button onclick="showTab('email')" class="tab-btn py-3 px-4 text-gray-700 font-medium border-b-2 border-transparent hover:border-blue-600" data-tab="email">
                Email
            </button>
            <button onclick="showTab('notifications')" class="tab-btn py-3 px-4 text-gray-700 font-medium border-b-2 border-transparent hover:border-blue-600" data-tab="notifications">
                Notifications
            </button>
            <button onclick="showTab('security')" class="tab-btn py-3 px-4 text-gray-700 font-medium border-b-2 border-transparent hover:border-blue-600" data-tab="security">
                Security
            </button>
        </div>

        <!-- General Settings Tab -->
        <div id="general-tab" class="settings-tab">
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <h3 class="text-lg font-bold text-gray-900 mb-6">General Settings</h3>
                <form id="generalForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                        <input type="text" id="siteName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="UTHM Bulletin Board">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                        <textarea id="siteDescription" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">Official UTHM Bulletin Board for announcements and events</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <select id="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="UTC" selected>UTC</option>
                            <option value="Asia/Kuala_Lumpur">Asia/Kuala Lumpur (Malaysia)</option>
                            <option value="Asia/Singapore">Asia/Singapore</option>
                            <option value="Asia/Bangkok">Asia/Bangkok</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Site Theme</label>
                        <select id="siteTheme" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="maintenanceMode" class="w-4 h-4 text-blue-600">
                            <span class="ml-3 text-sm font-medium text-gray-700">Maintenance Mode</span>
                        </label>
                        <span class="text-sm text-gray-500">Site will be unavailable to users</span>
                    </div>
                    <button type="button" onclick="saveSettings('general')" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Email Settings Tab -->
        <div id="email-tab" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Email Configuration</h3>
                <form id="emailForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" id="smtpHost" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="smtp.mailtrap.io">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" id="smtpPort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="587" value="587">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                        <input type="text" id="smtpUsername" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                        <input type="password" id="smtpPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                        <input type="email" id="fromEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="noreply@uthm.edu.my">
                    </div>
                    <button type="button" onclick="saveSettings('email')" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Notifications Settings Tab -->
        <div id="notifications-tab" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Notification Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="text-sm font-medium text-gray-700">Send Email Notifications</label>
                        <input type="checkbox" id="emailNotifications" class="w-4 h-4 text-blue-600" checked>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="text-sm font-medium text-gray-700">Notify on New Announcements</label>
                        <input type="checkbox" id="notifyAnnouncements" class="w-4 h-4 text-blue-600" checked>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="text-sm font-medium text-gray-700">Notify on New Events</label>
                        <input type="checkbox" id="notifyEvents" class="w-4 h-4 text-blue-600" checked>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="text-sm font-medium text-gray-700">Notify on User Registration</label>
                        <input type="checkbox" id="notifyRegistration" class="w-4 h-4 text-blue-600">
                    </div>
                    <button type="button" onclick="saveSettings('notifications')" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 mt-6">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- Security Settings Tab -->
        <div id="security-tab" class="settings-tab hidden">
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Security Settings</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes)</label>
                        <input type="number" id="sessionTimeout" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
                        <input type="number" id="maxLoginAttempts" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="5">
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="text-sm font-medium text-gray-700">Require Email Verification</label>
                        <input type="checkbox" id="requireEmailVerification" class="w-4 h-4 text-blue-600" checked>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <label class="text-sm font-medium text-gray-700">Enable Two-Factor Authentication</label>
                        <input type="checkbox" id="enableTwoFactor" class="w-4 h-4 text-blue-600">
                    </div>
                    <button type="button" onclick="saveSettings('security')" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                </div>
            </div>
        </div>
</div>

@endsection

@section('scripts')
<script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-b-2', 'border-blue-600');
            btn.classList.add('border-transparent');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active class to button
        event.target.classList.remove('border-transparent');
        event.target.classList.add('border-b-2', 'border-blue-600');
    }

    function saveSettings(section) {
        const settings = {};
        
        if (section === 'general') {
            settings.site_name = document.getElementById('siteName').value;
            settings.site_description = document.getElementById('siteDescription').value;
            settings.timezone = document.getElementById('timezone').value;
            settings.maintenance_mode = document.getElementById('maintenanceMode').checked;
            settings.site_theme = document.getElementById('siteTheme').value;
        } else if (section === 'email') {
            settings.smtp_host = document.getElementById('smtpHost').value;
            settings.smtp_port = document.getElementById('smtpPort').value;
            settings.smtp_username = document.getElementById('smtpUsername').value;
            settings.smtp_password = document.getElementById('smtpPassword').value;
            settings.from_email = document.getElementById('fromEmail').value;
        } else if (section === 'notifications') {
            settings.email_notifications = document.getElementById('emailNotifications').checked;
            settings.notify_announcements = document.getElementById('notifyAnnouncements').checked;
            settings.notify_events = document.getElementById('notifyEvents').checked;
            settings.notify_registration = document.getElementById('notifyRegistration').checked;
        } else if (section === 'security') {
            settings.session_timeout = document.getElementById('sessionTimeout').value;
            settings.max_login_attempts = document.getElementById('maxLoginAttempts').value;
            settings.require_email_verification = document.getElementById('requireEmailVerification').checked;
            settings.enable_two_factor = document.getElementById('enableTwoFactor').checked;
        }

        fetch('/admin/settings/update', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(settings)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Settings saved successfully');
            } else {
                alert('Error saving settings: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving settings');
        });
    }

    // Load saved settings on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/admin/settings/data')
            .then(response => response.json())
            .then(response => {
                if (response.success && response.data) {
                    const data = response.data;
                    if (data.site_name) document.getElementById('siteName').value = data.site_name;
                    if (data.site_description) document.getElementById('siteDescription').value = data.site_description;
                    if (data.timezone) document.getElementById('timezone').value = data.timezone;
                    if (data.maintenance_mode) {
                        document.getElementById('maintenanceMode').checked = data.maintenance_mode === 'true' || data.maintenance_mode === true || data.maintenance_mode === '1';
                    }
                    if (data.site_theme) document.getElementById('siteTheme').value = data.site_theme;
                    
                    // Email settings
                    if (data.smtp_host) document.getElementById('smtpHost').value = data.smtp_host;
                    if (data.smtp_port) document.getElementById('smtpPort').value = data.smtp_port;
                    if (data.smtp_username) document.getElementById('smtpUsername').value = data.smtp_username;
                    if (data.smtp_password) document.getElementById('smtpPassword').value = data.smtp_password;
                    if (data.from_email) document.getElementById('fromEmail').value = data.from_email;
                    
                    // Notifications settings
                    if (data.email_notifications) {
                        document.getElementById('emailNotifications').checked = data.email_notifications === 'true' || data.email_notifications === true || data.email_notifications === '1';
                    }
                    if (data.notify_announcements) {
                        document.getElementById('notifyAnnouncements').checked = data.notify_announcements === 'true' || data.notify_announcements === true || data.notify_announcements === '1';
                    }
                    if (data.notify_events) {
                        document.getElementById('notifyEvents').checked = data.notify_events === 'true' || data.notify_events === true || data.notify_events === '1';
                    }
                    if (data.notify_registration) {
                        document.getElementById('notifyRegistration').checked = data.notify_registration === 'true' || data.notify_registration === true || data.notify_registration === '1';
                    }
                    
                    // Security settings
                    if (data.session_timeout) document.getElementById('sessionTimeout').value = data.session_timeout;
                    if (data.max_login_attempts) document.getElementById('maxLoginAttempts').value = data.max_login_attempts;
                    if (data.require_email_verification) {
                        document.getElementById('requireEmailVerification').checked = data.require_email_verification === 'true' || data.require_email_verification === true || data.require_email_verification === '1';
                    }
                    if (data.enable_two_factor) {
                        document.getElementById('enableTwoFactor').checked = data.enable_two_factor === 'true' || data.enable_two_factor === true || data.enable_two_factor === '1';
                    }
                }
            })
            .catch(error => console.error('Error fetching settings:', error));
    });
</script>

<style>
    .settings-tab.hidden {
        display: none;
    }
</style>
@endsection
