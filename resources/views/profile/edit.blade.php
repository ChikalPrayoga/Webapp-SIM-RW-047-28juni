<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-dark leading-tight">
            {{ __('Informasi Profil') }}
        </h2>
    </x-slot>

    @php
        $roleTitle = match(auth()->user()->role->role_name) {
            'SUPER_ADMIN' => 'Super Admin',
            'KETUA_RW' => 'Ketua RW',
            'SEKRETARIS_RW' => 'Sekretaris RW',
            'KETUA_RT' => 'Ketua RT',
            'BENDAHARA_RW' => 'Bendahara RW',
            default => auth()->user()->role->role_name
        };
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Profile Card -->
            <div class="bg-white border border-slate-100 shadow-[0_15px_40px_-15px_rgba(0,77,64,0.12)] sm:rounded-2xl overflow-hidden">
                
                <!-- Decorative Card Header Banner -->
                <div class="h-32 sm:h-40 bg-gradient-to-r from-brand-primary to-[#00695C] relative">
                    <!-- Subtle pattern overlay -->
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                </div>

                <!-- Profile Avatar and Title Section -->
                <div class="px-6 pb-6 text-center border-b border-slate-100">
                    <!-- Avatar container -->
                    <div class="relative -mt-16 sm:-mt-20 mb-4 inline-block">
                        <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-white bg-gradient-to-tr from-brand-secondary/40 via-white to-[#E0F2F1] flex items-center justify-center shadow-md overflow-hidden">
                            <!-- Neutral Flat Minimalist Avatar -->
                            <svg class="w-20 h-20 text-brand-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Display Name and Username -->
                    <h3 class="text-2xl font-bold text-brand-dark tracking-tight">{{ auth()->user()->full_name }}</h3>
                    <p class="text-sm text-slate-500 font-medium mt-0.5">@<span>{{ auth()->user()->username }}</span></p>

                    <!-- Badges -->
                    <div class="flex items-center justify-center gap-2.5 mt-4">
                        <!-- Role Badge -->
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-primary border border-brand-primary/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            {{ $roleTitle }}
                        </span>

                        <!-- Status Badge -->
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#E8F5E9] text-[#2E7D32] border border-[#C8E6C9]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2E7D32] animate-pulse"></span>
                            Aktif
                        </span>
                    </div>
                </div>

                <!-- Detailed Account Info Section -->
                <div class="p-8 bg-slate-50/50">
                    <h4 class="text-sm font-bold text-brand-dark uppercase tracking-wider mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Rincian Informasi Akun
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Lengkap</span>
                            <div class="text-sm font-semibold text-brand-dark mt-1">{{ auth()->user()->full_name }}</div>
                        </div>

                        <!-- Username -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Username</span>
                            <div class="text-sm font-semibold text-brand-dark mt-1">{{ auth()->user()->username }}</div>
                        </div>

                        <!-- Email Address -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alamat Email</span>
                            <div class="text-sm font-semibold text-brand-dark mt-1">{{ auth()->user()->email }}</div>
                        </div>

                        <!-- Phone Number -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nomor Telepon</span>
                            <div class="text-sm font-semibold text-brand-dark mt-1">{{ auth()->user()->phone_number ?? '-' }}</div>
                        </div>

                        <!-- Role / Position -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jabatan Kerja</span>
                            <div class="text-sm font-semibold text-brand-dark mt-1">{{ $roleTitle }}</div>
                        </div>

                        <!-- Last Login -->
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Terakhir Login</span>
                            <div class="text-sm font-semibold text-brand-dark mt-1">
                                {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- Read-Only Policy Warning Banner -->
                    <div class="mt-8 p-4 rounded-xl bg-amber-50 border border-amber-100 flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h5 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Pemberitahuan Kebijakan Keamanan</h5>
                            <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                Pengelolaan data akun (nama, email, password, dan status keaktifan) sepenuhnya dikelola terpusat oleh **Super Admin**. Halaman ini bersifat read-only untuk menjaga integritas data kependudukan dan organisasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

