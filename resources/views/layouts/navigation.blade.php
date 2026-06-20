<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @can('manage_system')
                        <x-nav-link href="#">Dashboard Sistem</x-nav-link>
                        <x-nav-link href="#">User Management</x-nav-link>
                        <x-nav-link href="#">Role Management</x-nav-link>
                        <x-nav-link href="#">Permission Management</x-nav-link>
                        <x-nav-link href="#">System Settings</x-nav-link>
                    @endcan

                    @can('view_residents')
                        <x-nav-link :href="route('warga.index')" :active="request()->routeIs('warga.*')">
                            {{ auth()->user()->role->role_name === 'KETUA_RT' ? 'Warga RT' : 'Data Warga' }}
                        </x-nav-link>
                        <x-nav-link :href="route('kk.index')" :active="request()->routeIs('kk.*')">
                            {{ auth()->user()->role->role_name === 'KETUA_RT' ? 'KK RT' : 'Data KK' }}
                        </x-nav-link>
                    @endcan

                    @can('view_letters')
                        <x-nav-link :href="route('letters.index')" :active="request()->routeIs('letters.*')">
                            {{ auth()->user()->role->role_name === 'SEKRETARIS_RW' ? 'Administrasi Surat' : (auth()->user()->role->role_name === 'KETUA_RT' ? 'Surat RT' : 'Persuratan') }}
                        </x-nav-link>
                    @endcan

                    @can('view_complaints')
                        <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                            {{ auth()->user()->role->role_name === 'KETUA_RT' ? 'Laporan RT' : 'Laporan & Aspirasi' }}
                        </x-nav-link>
                    @endcan

                    @can('manage_information')
                        <x-nav-link href="#">Pengumuman</x-nav-link>
                        <x-nav-link href="#">Agenda</x-nav-link>
                    @endcan

                    @can('approve_rw_letters')
                        <x-nav-link href="#">Review Publikasi</x-nav-link>
                    @endcan

                    @can('view_finances')
                        @if(auth()->user()->role->role_name === 'BENDAHARA_RW')
                        <x-nav-link href="#">Keuangan</x-nav-link>
                        <x-nav-link href="#">Iuran</x-nav-link>
                        <x-nav-link href="#">Laporan Keuangan</x-nav-link>
                        @endif
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('manage_system')
                <x-responsive-nav-link href="#">Dashboard Sistem</x-responsive-nav-link>
                <x-responsive-nav-link href="#">User Management</x-responsive-nav-link>
                <x-responsive-nav-link href="#">Role Management</x-responsive-nav-link>
                <x-responsive-nav-link href="#">Permission Management</x-responsive-nav-link>
                <x-responsive-nav-link href="#">System Settings</x-responsive-nav-link>
            @endcan

            @can('view_residents')
                <x-responsive-nav-link :href="route('warga.index')" :active="request()->routeIs('warga.*')">
                    {{ auth()->user()->role->role_name === 'KETUA_RT' ? 'Warga RT' : 'Data Warga' }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kk.index')" :active="request()->routeIs('kk.*')">
                    {{ auth()->user()->role->role_name === 'KETUA_RT' ? 'KK RT' : 'Data KK' }}
                </x-responsive-nav-link>
            @endcan

            @can('view_letters')
                <x-responsive-nav-link :href="route('letters.index')" :active="request()->routeIs('letters.*')">
                    {{ auth()->user()->role->role_name === 'SEKRETARIS_RW' ? 'Administrasi Surat' : (auth()->user()->role->role_name === 'KETUA_RT' ? 'Surat RT' : 'Persuratan') }}
                </x-responsive-nav-link>
            @endcan

            @can('view_complaints')
                <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                    {{ auth()->user()->role->role_name === 'KETUA_RT' ? 'Laporan RT' : 'Laporan & Aspirasi' }}
                </x-responsive-nav-link>
            @endcan

            @can('manage_information')
                <x-responsive-nav-link href="#">Pengumuman</x-responsive-nav-link>
                <x-responsive-nav-link href="#">Agenda</x-responsive-nav-link>
            @endcan

            @can('approve_rw_letters')
                <x-responsive-nav-link href="#">Review Publikasi</x-responsive-nav-link>
            @endcan

            @can('view_finances')
                @if(auth()->user()->role->role_name === 'BENDAHARA_RW')
                <x-responsive-nav-link href="#">Keuangan</x-responsive-nav-link>
                <x-responsive-nav-link href="#">Iuran</x-responsive-nav-link>
                <x-responsive-nav-link href="#">Laporan Keuangan</x-responsive-nav-link>
                @endif
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-red-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
