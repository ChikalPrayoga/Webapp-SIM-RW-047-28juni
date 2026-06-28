<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Data Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form method="GET" action="{{ route('warga.index') }}" class="mb-4 flex flex-col md:flex-row gap-4">
                        <input type="text" name="search" placeholder="Cari NIK atau Nama..." value="{{ request('search') }}" class="w-full md:w-1/3 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <select name="rt_code" class="w-full md:w-1/4 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua RT</option>
                            <option value="RT001" {{ request('rt_code') == 'RT001' ? 'selected' : '' }}>RT 001</option>
                            <option value="RT002" {{ request('rt_code') == 'RT002' ? 'selected' : '' }}>RT 002</option>
                            <option value="RT003" {{ request('rt_code') == 'RT003' ? 'selected' : '' }}>RT 003</option>
                        </select>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cari
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">NIK</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Nama Lengkap</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">RT</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($wargas as $warga)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $warga->nik }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $warga->nama_lengkap }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $warga->kartuKeluarga->rt_code ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                {{ $warga->status_warga }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('warga.show', $warga->nik) }}" class="btn-detail-action">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-brand-dark dark:text-white">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $wargas->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
