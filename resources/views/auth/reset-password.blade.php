<x-guest-layout>
    <x-slot name="title">Reset Password - UTHM Digital Bulletin Board</x-slot>

    <!-- Header -->
    <h2 class="text-center text-2xl font-semibold text-gray-900">Reset Password</h2>
    <p class="mt-1 text-center text-sm text-gray-500 mb-6">Create a new secure password for your account</p>

    <!-- Info Message -->
    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                <i class="fas fa-shield-alt uthm-blue"></i>
            </div>
            <p class="text-sm text-blue-800">
                Please enter your new password below. Make sure it's strong and unique.
            </p>
        </div>
    </div>

    <!-- Reset Password Form -->
    <form class="space-y-4" action="{{ route('password.update') }}" method="POST">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email Address
            </label>
            <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </div>
                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required readonly
                       class="input-field mt-1 block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg shadow-sm bg-gray-100 text-gray-600 sm:text-sm cursor-not-allowed">
            </div>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
                New Password <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </div>
                <input id="password" name="password" type="password" required
                       class="input-field block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Enter new password">
                <button type="button" onclick="togglePassword('password', 'eye-icon-1')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg id="eye-icon-1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
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

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                Confirm Password <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-check-circle text-gray-400 text-sm"></i>
                </div>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="input-field block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Confirm new password">
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg id="eye-icon-2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                 -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-save mr-2"></i> Reset Password
        </button>

        <!-- Back to login -->
        <p class="text-center text-sm text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}" class="font-medium uthm-blue hover:underline">
                Back to login
            </a>
        </p>
    </form>
</x-guest-layout>