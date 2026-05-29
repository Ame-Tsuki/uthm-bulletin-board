<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UTHM Digital Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .border-uthm-blue { border-color: #0056a6; }
        .focus\:ring-uthm-blue:focus { --tw-ring-color: #0056a6; }

        /* ── Animated background (same as login) ── */
        body {
            background-color: #0056a6;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        /* Subtle grid */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            opacity: 0.18;
            animation: drift 8s ease-in-out infinite alternate;
        }
        .orb-1 {
            width: 420px; height: 420px;
            background: #60b0ff;
            top: -120px; left: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 300px; height: 300px;
            background: #ffffff;
            bottom: -80px; right: -80px;
            animation-delay: 2s;
        }
        .orb-3 {
            width: 220px; height: 220px;
            background: #0033cc;
            top: 45%; left: 60%;
            animation-delay: 4s;
        }
        .orb-4 {
            width: 160px; height: 160px;
            background: #a0d0ff;
            top: 20%; right: 20%;
            animation-delay: 3s;
        }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(24px, 20px) scale(1.1); }
        }

        /* Page layout */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Card - improved spacing and shadow */
        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2rem 2rem 2rem 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 24px 60px rgba(0, 0, 50, 0.3);
            transition: transform 0.2s ease;
        }

        /* Trust badges - same as login */
        .trust-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f0f2f5;
        }
        .trust-badge {
            font-size: 11px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .trust-badge svg {
            width: 13px; height: 13px;
            stroke: #0056a6;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Smooth transitions for form elements */
        input, select {
            transition: all 0.2s ease;
        }
        input:focus, select:focus {
            transform: translateY(-1px);
        }

        /* Custom select arrow */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem;
            padding-right: 2.5rem;
        }

        /* Field container animation */
        .field-group {
            transition: all 0.2s ease;
        }
        
        /* Password strength indicator (optional visual) */
        .strength-meter {
            height: 3px;
            border-radius: 3px;
            background: #e5e7eb;
            margin-top: 6px;
            overflow: hidden;
        }
        .strength-meter-fill {
            width: 0%;
            height: 100%;
            transition: width 0.3s ease, background 0.3s ease;
        }
    </style>
</head>
<body>

    <!-- ── Animated background (same as login) ── -->
    <div class="bg-scene">
        <div class="bg-grid"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
    </div>

    <!-- ── Page content ── -->
    <div class="page-wrapper">
        <div class="card">
            <!-- Logo circle matching login -->
            <div class="w-14 h-14 rounded-full bg-uthm-blue flex items-center justify-center mx-auto mb-4 shadow-md">
                <span class="text-white text-xl font-semibold">U</span>
            </div>

            <!-- Header -->
            <h2 class="text-center text-2xl font-semibold text-gray-900">Create Account</h2>
            <p class="mt-1 text-center text-sm text-gray-500 mb-5">Join the UTHM Digital Bulletin Board</p>

            <!-- Registration Form -->
            <form class="space-y-4" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Row for UTHM ID + Name (inline on larger screens) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- UTHM ID -->
                    <div class="field-group">
                        <label for="uthm_id" class="block text-sm font-medium text-gray-700">
                            UTHM ID <span class="text-red-500">*</span>
                        </label>
                        <input id="uthm_id" name="uthm_id" type="text" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="AI230102">
                        @error('uthm_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div class="field-group">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Muhammad Amir Arif">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="field-group">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        UTHM Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="ai230102@student.uthm.edu.my">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role + Faculty row (responsive) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Role -->
                    <div class="field-group">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            I am a <span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Faculty (Conditional) -->
                    <div id="faculty-field" class="field-group" style="display: none;">
                        <label for="faculty" class="block text-sm font-medium text-gray-700">
                            Faculty <span class="text-red-500">*</span>
                        </label>
                        <select id="faculty" name="faculty" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Faculty</option>
                            <option value="FSKTM">Fakulti Sains Komputer dan Teknologi Maklumat</option>
                            <option value="FKMP">Fakulti Kejuruteraan Mekanikal dan Pembuatan</option>
                            <option value="FKAAB">Fakulti Kejuruteraan Arkitek dan Alam Sekitar Bangunan</option>
                            <option value="FPTP">Fakulti Pengurusan Teknologi dan Perniagaan</option>
                            <option value="FKEE">Fakulti Kejuruteraan Elektrik dan Elektronik</option>
                            <option value="FAST">Fakulti Sains Gunaan dan Teknologi</option>
                            <option value="FTK">Fakulti Teknologi Kejuruteraan</option>
                            <option value="PPD">Pusat Pengajian Diploma</option>
                        </select>
                        @error('faculty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password with strength meter (enhanced UX) -->
                <div class="field-group">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative mt-1">
                        <input id="password" name="password" type="password" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-10"
                               placeholder="Create a strong password (min. 8 characters)">
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                         -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div class="strength-meter">
                        <div id="strength-fill" class="strength-meter-fill"></div>
                    </div>
                    <p id="strength-text" class="text-xs text-gray-500 mt-1">Password strength: </p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="field-group">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Re-enter your password">
                </div>

                <!-- Terms & Conditions (styled like login's remember me) -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input id="terms" name="terms" type="checkbox" required
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        I agree to the <a href="#" class="uthm-blue hover:underline font-medium">Terms & Conditions</a>
                    </label>
                </div>

                <!-- Submit Button (same style as login) -->
                <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Create Account
                </button>

                <!-- Login Link (smooth) -->
                <p class="text-center text-sm text-gray-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium uthm-blue hover:underline">
                        Sign in instead
                    </a>
                </p>
            </form>

            <!-- Trust bar (consistent with login) -->
            <div class="trust-bar">
                <span class="trust-badge">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Secure registration
                </span>
                <span class="text-gray-300 text-xs">·</span>
                <span class="trust-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    UTHM Portal
                </span>
                <span class="text-gray-300 text-xs">·</span>
                <span class="trust-badge">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    Data protected
                </span>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript: Password Toggle + Faculty Conditional + Password Strength Meter -->
    <script>
        // Toggle password visibility (same as login but using register's password field)
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';

            icon.innerHTML = isHidden
                ? `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                            a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
                            l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                            m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0
                            01-4.132 5.411m0 0L21 21" />`
                : `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                   <path stroke-linecap="round" stroke-linejoin="round"
                         d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                            -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }

        // Faculty field conditional based on role selection
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const facultyField = document.getElementById('faculty-field');
            const facultySelect = document.getElementById('faculty');

            function updateFacultyField() {
                if (roleSelect.value === 'student') {
                    facultyField.style.display = 'block';
                    facultySelect.required = true;
                    // Add subtle animation
                    facultyField.style.opacity = '0';
                    facultyField.style.transform = 'translateY(-5px)';
                    setTimeout(() => {
                        facultyField.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                        facultyField.style.opacity = '1';
                        facultyField.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    facultyField.style.display = 'none';
                    facultySelect.required = false;
                    facultySelect.value = '';
                }
            }

            if (roleSelect) {
                roleSelect.addEventListener('change', updateFacultyField);
                updateFacultyField(); // initial check
            }

            // Password strength meter
            const passwordInput = document.getElementById('password');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');

            function calculateStrength(password) {
                let score = 0;
                if (!password) return { score: 0, label: 'Enter a password', color: '#e5e7eb', width: '0%' };
                
                // length check
                if (password.length >= 8) score += 1;
                if (password.length >= 12) score += 1;
                
                // complexity
                if (/[a-z]/.test(password)) score += 1;
                if (/[A-Z]/.test(password)) score += 1;
                if (/[0-9]/.test(password)) score += 1;
                if (/[^a-zA-Z0-9]/.test(password)) score += 1;
                
                // max score: 6 (2 for length, 4 for complexity)
                let percentage = Math.min((score / 6) * 100, 100);
                let label = '';
                let color = '';
                
                if (score <= 2) {
                    label = 'Weak';
                    color = '#ef4444';
                } else if (score <= 4) {
                    label = 'Fair';
                    color = '#f59e0b';
                } else {
                    label = 'Strong';
                    color = '#10b981';
                }
                
                if (password.length === 0) {
                    label = 'Enter a password';
                    color = '#e5e7eb';
                    percentage = 0;
                } else if (password.length > 0 && score < 2) {
                    label = 'Very weak';
                    color = '#dc2626';
                }
                
                return { width: `${percentage}%`, color, label };
            }
            
            function updateStrengthMeter() {
                const password = passwordInput.value;
                const { width, color, label } = calculateStrength(password);
                if (strengthFill) {
                    strengthFill.style.width = width;
                    strengthFill.style.backgroundColor = color;
                }
                if (strengthText) {
                    strengthText.innerHTML = `Password strength: <span style="color: ${color}; font-weight: 500;">${label}</span>`;
                }
            }
            
            if (passwordInput) {
                passwordInput.addEventListener('input', updateStrengthMeter);
                updateStrengthMeter();
            }
            
            // Optional: live confirm password validation hint
            const confirmInput = document.getElementById('password_confirmation');
            if (confirmInput && passwordInput) {
                function validateMatch() {
                    const pass = passwordInput.value;
                    const confirm = confirmInput.value;
                    if (confirm.length > 0 && pass !== confirm) {
                        confirmInput.classList.add('border-red-500', 'focus:border-red-500');
                        confirmInput.style.backgroundColor = '#fef2f2';
                    } else {
                        confirmInput.classList.remove('border-red-500', 'focus:border-red-500');
                        confirmInput.style.backgroundColor = '';
                    }
                }
                passwordInput.addEventListener('input', validateMatch);
                confirmInput.addEventListener('input', validateMatch);
            }
        });
    </script>
</body>
</html>