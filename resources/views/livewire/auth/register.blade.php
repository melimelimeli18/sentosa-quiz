<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Class selection moved to post-registration modal -->
            <!-- Password -->
            <div x-data="{ password: '' }">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Password')"
                    viewable
                    x-model="password"
                />

                <div class="mt-3 text-sm space-y-2">
                    <p class="font-medium text-zinc-700 dark:text-zinc-300">{{ __('Password requirements:') }}</p>
                    <ul class="space-y-1">
                        <li class="flex items-center gap-2" :class="password.length >= 12 ? 'text-green-600 dark:text-green-400' : 'text-zinc-500 dark:text-zinc-400'">
                            <svg x-show="password.length >= 12" class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <svg x-show="password.length < 12" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /></svg>
                            <span>{{ __('Minimum 12 characters') }}</span>
                        </li>
                        <li class="flex items-center gap-2" :class="(/[A-Z]/.test(password) && /[a-z]/.test(password)) ? 'text-green-600 dark:text-green-400' : 'text-zinc-500 dark:text-zinc-400'">
                            <svg x-show="/[A-Z]/.test(password) && /[a-z]/.test(password)" class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <svg x-show="!/[A-Z]/.test(password) || !/[a-z]/.test(password)" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /></svg>
                            <span>{{ __('At least one uppercase and lowercase letter') }}</span>
                        </li>
                        <li class="flex items-center gap-2" :class="/[^A-Za-z0-9]/.test(password) ? 'text-green-600 dark:text-green-400' : 'text-zinc-500 dark:text-zinc-400'">
                            <svg x-show="/[^A-Za-z0-9]/.test(password)" class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <svg x-show="!/[^A-Za-z0-9]/.test(password)" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /></svg>
                            <span>{{ __('At least one symbol') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button" wire:loading.attr="disabled" style="display:inline-flex;align-items:center;gap:6px"><!-- ponytail: loading spinner for button -->
    <svg wire:loading class="animate-spin" style="width:14px;height:14px;color:#fff;display:inline-block" fill="none" viewBox="0 0 24 24">
        <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span>Create account</span>
</flux:button>
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
