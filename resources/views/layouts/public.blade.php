<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Portal Layanan Warga' }} — SIM RW 047</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-[#37474F] antialiased bg-white flex flex-col min-h-screen">

        {{-- Public Navbar --}}
        <nav x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('public.portal') }}" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-[#004D40] flex items-center justify-center shadow-md group-hover:bg-[#00382E] transition-colors">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="text-xl font-extrabold text-[#37474F] tracking-tight">SIM RW 047</span>
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex lg:items-center lg:space-x-8">
                        <a href="{{ route('public.portal') }}" class="text-sm font-semibold {{ request()->routeIs('public.portal') ? 'text-[#004D40]' : 'text-slate-600 hover:text-[#004D40]' }} transition-colors">Beranda</a>
                        
                        <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-[#004D40] transition-colors focus:outline-none">
                                Surat
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="dropdownOpen" x-transition style="display: none;" class="absolute z-10 -ml-4 mt-3 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 py-2">
                                <a href="{{ route('public.letters.create') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-[#004D40]">Ajukan Surat</a>
                                <a href="{{ route('public.letters.track') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-[#004D40]">Lacak Surat</a>
                            </div>
                        </div>

                        <div x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-[#004D40] transition-colors focus:outline-none">
                                Pengaduan
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="dropdownOpen" x-transition style="display: none;" class="absolute z-10 -ml-4 mt-3 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 py-2">
                                <a href="{{ route('public.complaints.create') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-[#004D40]">Buat Laporan</a>
                                <a href="{{ route('public.complaints.track') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-[#004D40]">Lacak Laporan</a>
                            </div>
                        </div>

                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-[#004D40] transition-colors">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-colors bg-[#004D40] border border-transparent rounded-xl hover:bg-[#00382E] shadow-sm">
                                Login Pengurus
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center lg:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-500 hover:text-[#004D40] focus:outline-none p-2">
                            <svg x-show="!mobileMenuOpen" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="mobileMenuOpen" style="display:none;" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition style="display: none;" class="lg:hidden border-t border-gray-200 bg-white shadow-lg absolute w-full">
                <div class="px-4 pt-2 pb-4 space-y-1">
                    <a href="{{ route('public.portal') }}" class="block px-3 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('public.portal') ? 'text-[#004D40] bg-slate-50' : 'text-slate-700 hover:text-[#004D40] hover:bg-slate-50' }}">Beranda</a>
                    
                    <div class="text-sm font-bold text-slate-400 px-3 pt-3 pb-1 uppercase tracking-wider">Layanan Surat</div>
                    <a href="{{ route('public.letters.create') }}" class="block px-3 py-2 rounded-xl text-base font-semibold text-slate-700 hover:text-[#004D40] hover:bg-slate-50">Ajukan Surat</a>
                    <a href="{{ route('public.letters.track') }}" class="block px-3 py-2 rounded-xl text-base font-semibold text-slate-700 hover:text-[#004D40] hover:bg-slate-50">Lacak Surat</a>
                    
                    <div class="text-sm font-bold text-slate-400 px-3 pt-3 pb-1 uppercase tracking-wider">Pengaduan</div>
                    <a href="{{ route('public.complaints.create') }}" class="block px-3 py-2 rounded-xl text-base font-semibold text-slate-700 hover:text-[#004D40] hover:bg-slate-50">Buat Laporan</a>
                    <a href="{{ route('public.complaints.track') }}" class="block px-3 py-2 rounded-xl text-base font-semibold text-slate-700 hover:text-[#004D40] hover:bg-slate-50">Lacak Laporan</a>
                    
                    <div class="border-t border-gray-100 my-2"></div>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-700 hover:text-[#004D40] hover:bg-slate-50">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-3 rounded-xl text-base font-semibold text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    @else
                        <div class="pt-2">
                            <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-base font-semibold text-white transition-colors bg-[#004D40] border border-transparent rounded-xl hover:bg-[#00382E] shadow-sm">
                                Login Pengurus
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main class="flex-grow w-full bg-[#FFFFFF]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-8 sm:py-10">
                {{ $slot }}
            </div>
        </main>

        {{-- Public Footer --}}
        <footer class="bg-[#37474F] text-[#FFFFFF] mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="flex flex-col lg:flex-row justify-between gap-12 lg:gap-16">
                    <div class="lg:w-2/5">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-[#004D40] flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold tracking-tight">SIM RW 047</span>
                        </div>
                        <p class="text-base text-[#A5D6A7] leading-relaxed mb-6 pr-4 lg:pr-10">
                            Portal Layanan Publik Warga RW 047. Sistem informasi terpadu untuk kemudahan administrasi pengajuan surat pengantar dan pelaporan aspirasi warga secara digital.
                        </p>
                    </div>
                    
                    <div class="lg:w-1/4">
                        <h4 class="text-lg font-bold mb-5 text-[#FFFFFF]">Layanan Warga</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ route('public.letters.create') }}" class="text-[#A5D6A7] hover:text-[#FFFFFF] font-medium transition-colors">Pengajuan Surat</a></li>
                            <li><a href="{{ route('public.complaints.create') }}" class="text-[#A5D6A7] hover:text-[#FFFFFF] font-medium transition-colors">Buat Laporan</a></li>
                            <li><a href="{{ route('public.letters.track') }}" class="text-[#A5D6A7] hover:text-[#FFFFFF] font-medium transition-colors">Lacak Status Surat</a></li>
                            <li><a href="{{ route('public.complaints.track') }}" class="text-[#A5D6A7] hover:text-[#FFFFFF] font-medium transition-colors">Lacak Status Laporan</a></li>
                        </ul>
                    </div>

                    <div class="lg:w-1/3">
                        <h4 class="text-lg font-bold mb-5 text-[#FFFFFF]">Kontak Sekretariat</h4>
                        <ul class="space-y-4 text-[#A5D6A7]">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-[#A5D6A7] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                <span class="text-sm font-medium leading-relaxed">Kantor Sekretariat RW 047<br>Jl. Cempaka Putih No. 47<br>Kecamatan Contoh, Kota Contoh</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-[#A5D6A7]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                <span class="text-sm font-medium">pengurus@simrw047.com</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-[#455A64] mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm font-medium text-[#A5D6A7]">
                        &copy; {{ date('Y') }} SIM RW 047. Hak Cipta Dilindungi.
                    </p>
                    <div class="flex items-center gap-4 text-sm font-medium text-[#A5D6A7]">
                        <span>Versi 1.0.0</span>
                    </div>
                </div>
            </div>
        </footer>

    </body>
</html>
