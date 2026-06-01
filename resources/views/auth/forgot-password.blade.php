<x-guest-layout>
    <x-slot name="title">Forgot Password - UTHM Digital Bulletin Board</x-slot>

    <!-- Header -->
    <h2 class="text-center text-2xl font-semibold text-gray-900">Forgot Password?</h2>
    <p class="mt-1 text-center text-sm text-gray-500 mb-6">No worries, we'll send you reset instructions</p>

    <!-- Info Message -->
    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle uthm-blue"></i>
            </div>
            <p class="text-sm text-blue-800">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <p class="text-sm text-green-800 font-medium">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <!-- Forgot Password Form -->
    <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email Address <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </div>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="input-field mt-1 block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="your@email.com">
            </div>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-paper-plane mr-2"></i> Send Reset Link
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