<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input-label for="name" :value="__('Full Name')" />
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="mt-1 block w-full pl-10" 
                        :value="old('name', $user->name)" 
                        required 
                        autofocus 
                        autocomplete="name"
                        placeholder="Your full name"
                    />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="email" :value="__('Email Address')" />
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="mt-1 block w-full pl-10" 
                        :value="old('email', $user->email)" 
                        required 
                        autocomplete="username"
                        placeholder="your@email.com"
                    />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-yellow-800 font-medium">
                                    {{ __('Your email address is unverified.') }}
                                </p>
                                <button 
                                    form="send-verification" 
                                    class="mt-1 text-sm text-indigo-600 hover:text-indigo-500 font-medium underline transition-colors duration-150"
                                >
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                <p class="text-sm font-medium text-green-800">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">My Interests & Hobbies</h3>
            <p class="text-xs text-gray-500 mb-4">Select the interests you want to connect with. This helps you find relevant community groups.</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                @php
                    $availableInterests = ['Drawing', 'Swimming', 'Science', 'Music', 'Gaming', 'Reading', 'Coding', 'Cooking', 'Photography', 'Sports'];
                    $userInterests = $user->interests ?? [];
                @endphp
                @foreach($availableInterests as $interest)
                    <label class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl cursor-pointer transition duration-150 select-none group">
                        <input type="checkbox" name="interests[]" value="{{ $interest }}" 
                               {{ in_array($interest, $userInterests) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue w-4 h-4 mr-2.5">
                        <span class="text-sm text-gray-700 font-medium group-hover:text-gray-900">{{ $interest }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
            <x-primary-button>
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Save Changes') }}
                </span>
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-medium"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Profile updated!') }}
                </div>
            @endif
        </div>
    </form>
</section>