<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5" />
            <x-text-input id="email" class="block w-full px-4 py-3" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5" />
            <x-text-input id="password" class="block w-full px-4 py-3" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-700 font-semibold text-xs uppercase tracking-wider mb-1.5" />
            <x-text-input id="password_confirmation" class="block w-full px-4 py-3"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2 flex justify-between items-center">
            <a class="text-sm font-semibold text-brand-primary hover:text-[#003B31] transition-colors duration-150 focus:outline-none focus:underline" href="{{ route('login') }}">
                {{ __('Kembali ke Login') }}
            </a>

            <x-primary-button class="py-3 text-xs tracking-widest font-bold shadow-md shadow-brand-primary/10">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

