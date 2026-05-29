<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UTHM Digital Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }

        /* ── Animated background ── */
        body {
            background-color: #0056a6;
            overflow: hidden;
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
            padding: 3rem 1rem;
        }

        /* Card */
        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 60px rgba(0, 0, 50, 0.3);
        }

        /* Trust badges */
        .trust-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
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
    </style>
</head>
<body>

    <!-- ── Animated background ── -->
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

            <!-- Logo circle -->
            <div class="w-14 h-14 rounded-full bg-uthm-blue flex items-center justify-center mx-auto mb-4">
                <span class="text-white text-xl font-semibold">U</span>
            </div>

            <!-- Header -->
            <h2 class="text-center text-2xl font-semibold text-gray-900">Welcome Back</h2>
            <p class="mt-1 text-center text-sm text-gray-500 mb-6">Sign in to your UTHM account</p>

            <!-- Login Form -->
            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- UTHM ID / Email -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700">
                        UTHM ID or Email <span class="text-red-500">*</span>
                    </label>
                    <input id="login" name="login" type="text" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="AI230102 or email@uthm.edu.my">
                    @error('login')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-10"
                               placeholder="Enter your password">
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
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-medium uthm-blue hover:underline">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Sign In
                </button>

                <!-- Register link -->
                <p class="text-center text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium uthm-blue hover:underline">
                        Register here
                    </a>
                </p>
            </form>

            <!-- Trust bar -->
            <div class="trust-bar">
                <span class="trust-badge">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Secure login
                </span>
                <span class="text-gray-300 text-xs">·</span>
                <span class="trust-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    UTHM Portal
                </span>
            </div>

        </div>
    </div>

    <!-- Password toggle script -->
    <script>
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
    </script>
</body>
</html>