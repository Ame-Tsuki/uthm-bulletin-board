<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'UTHM Digital Bulletin Board' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            {{ $slot }}

            <!-- Trust bar -->
            <div class="trust-bar">
                <span class="trust-badge">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Secure connection
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
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
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