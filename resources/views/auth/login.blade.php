<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block w-full px-4 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan alamat email Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5" />
            <x-text-input id="password" class="block w-full px-4 py-3"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-primary shadow-sm focus:ring focus:ring-brand-primary focus:ring-opacity-20 cursor-pointer" name="remember">
                <span class="ms-2 text-sm text-slate-600 font-medium hover:text-slate-800 transition duration-150">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-brand-primary hover:text-[#003B31] transition-colors duration-150 focus:outline-none focus:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Action Button -->
        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 text-sm tracking-widest font-bold shadow-lg shadow-brand-primary/10 hover:shadow-brand-primary/25">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

