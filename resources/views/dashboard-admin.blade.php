<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Dashboard - Super Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Selamat datang, {{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Anda login sebagai <span class="font-semibold px-2 py-1 bg-red-50 text-red-700 rounded">{{ auth()->user()->role->role_name }}</span>
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </div>

            <!-- Admin Alert -->
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-indigo-800">
                            Mode Administrator Sistem Aktif
                        </h3>
                        <div class="mt-2 text-sm text-indigo-700">
                            <p>Anda berada di area manajemen sistem. Pastikan untuk berhati-hati saat melakukan perubahan konfigurasi peran (Role) atau izin (Permission).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistic Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pengguna Aktif</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['total_users']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Akun terdaftar dalam sistem</p>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Role Terkonfigurasi</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['total_roles']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Struktur RBAC</p>
                </div>
            </div>

            <!-- Empty State Lists -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800">Log Aktivitas Sistem</h3>
                </div>
                <div class="p-6 text-center text-gray-500">
                    Sistem beroperasi dengan normal. Fitur audit log akan tersedia pada update mendatang.
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
