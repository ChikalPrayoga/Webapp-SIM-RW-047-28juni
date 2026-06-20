<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Dashboard - Bendahara RW') }}
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
                            Anda login sebagai <span class="font-semibold px-2 py-1 bg-indigo-50 text-indigo-700 rounded">{{ auth()->user()->role->role_name }}</span>
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </div>

            <!-- Placeholder Alert -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Modul Keuangan Dalam Pengembangan
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Fitur pencatatan arus kas dan rekap iuran warga masih dalam tahap pengembangan dan akan dirilis pada fase pembaruan sistem berikutnya.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistic Cards Placeholder -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 opacity-50 pointer-events-none">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Saldo Kas</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">Rp 0</dd>
                    <p class="text-xs text-gray-500 mt-2">Bulan ini</p>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">Pemasukan Iuran</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">Rp 0</dd>
                    <p class="text-xs text-gray-500 mt-2">Bulan ini</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">Pengeluaran</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">Rp 0</dd>
                    <p class="text-xs text-gray-500 mt-2">Bulan ini</p>
                </div>
            </div>

            <!-- Empty State Lists -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg opacity-50 pointer-events-none">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800">Riwayat Transaksi Terbaru</h3>
                </div>
                <div class="p-6 text-center text-gray-500">
                    Belum ada data transaksi yang tercatat.
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
