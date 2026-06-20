<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfigurasi Sistem Dasar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-md shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Mode Tinjauan (Read Only)</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Halaman ini menampilkan konfigurasi dasar yang tertanam di sistem. Form ini dikunci sementara (MVP) hingga modul dinamis dikembangkan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                        <input type="text" disabled value="{{ $settings['rw_name'] }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat Resmi</label>
                        <input type="text" disabled value="{{ $settings['rw_address'] }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500 sm:text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Telepon Kontak</label>
                            <input type="text" disabled value="{{ $settings['phone'] }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Resmi</label>
                            <input type="email" disabled value="{{ $settings['email'] }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Zona Waktu Sistem</label>
                            <input type="text" disabled value="{{ $settings['timezone'] }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Pemeliharaan (Maintenance)</label>
                            <input type="text" disabled value="{{ $settings['maintenance_mode'] ? 'AKTIF' : 'NONAKTIF' }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500 sm:text-sm font-bold {{ $settings['maintenance_mode'] ? 'text-red-600' : 'text-green-600' }}">
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <label class="block text-sm font-medium text-gray-700">Logo Instansi</label>
                        <div class="mt-4 flex items-center justify-center h-32 w-32 rounded border border-gray-300 bg-gray-50">
                            <span class="text-sm text-gray-400">Logo Default</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
