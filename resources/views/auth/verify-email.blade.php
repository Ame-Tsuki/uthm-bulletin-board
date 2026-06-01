<x-guest-layout>
    <x-slot name="title">Verify Email - UTHM Digital Bulletin Board</x-slot>

    <!-- Header -->
    <h2 class="text-center text-2xl font-semibold text-gray-900">Verify Your Email</h2>
    <p class="mt-1 text-center text-sm text-gray-500 mb-6">One last step to get started</p>

    <!-- Info Message -->
    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                <i class="fas fa-envelope-open-text uthm-blue"></i>
            </div>
            <p class="text-sm text-blue-800">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <p class="text-sm text-green-800 font-medium">
                    A new verification link has been sent to your email address.
                </p>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="space-y-4">
        <!-- Resend Verification Email -->
        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-paper-plane mr-2"></i> Resend Verification Email
            </button>
        </form>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-white text-gray-400">or</span>
            </div>
        </div>

        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-sign-out-alt mr-2"></i> Log Out
            </button>
        </form>
    </div>
</x-guest-layout>