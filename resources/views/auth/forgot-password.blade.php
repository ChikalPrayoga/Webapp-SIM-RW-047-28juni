<x-guest-layout>
    <div class="mb-6 text-sm text-slate-600 leading-relaxed">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block w-full px-4 py-3" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan alamat email Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="pt-2 flex justify-between items-center">
            <a class="text-sm font-semibold text-brand-primary hover:text-[#003B31] transition-colors duration-150 focus:outline-none focus:underline" href="{{ route('login') }}">
                {{ __('Kembali ke Login') }}
            </a>
            
            <x-primary-button class="py-3 text-xs tracking-widest font-bold shadow-md shadow-brand-primary/10">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

