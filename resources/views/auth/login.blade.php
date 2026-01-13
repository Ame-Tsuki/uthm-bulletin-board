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
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-2xl">
            <!-- Header -->
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Welcome Back
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sign in to your UTHM account
                </p>
            </div>

            <!-- Login Form -->
            <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Login Field -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700">
                        UTHM ID or Email *
                    </label>
                    <input id="login" name="login" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm"
                           placeholder="AI230102 or email@uthm.edu.my">
                    @error('login')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password *
                    </label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm pr-10"
                               placeholder="Enter your password">
                        <button type="button" onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            👁️
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password-->
                <div class="flex items-center justify-between">
    <div class="flex items-center">
        <input id="remember" name="remember" type="checkbox" 
               class="h-4 w-4 text-uthm-blue focus:ring-uthm-blue border-gray-300 rounded">
        <label for="remember" class="ml-2 block text-sm text-gray-900">
            Remember me
        </label>
    </div>
    <div class="text-sm">
        <a href="{{ route('password.request') }}" class="font-medium text-uthm-blue hover:underline">
            Forgot your password?
        </a>
    </div>
</div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uthm-blue">
                        Sign In
                    </button>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-medium text-uthm-blue hover:underline">
                            Register here
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>