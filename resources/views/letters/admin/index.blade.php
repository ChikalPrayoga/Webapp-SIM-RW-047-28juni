<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('letters.index') }}" class="mb-6 flex flex-col md:flex-row gap-4">
                        <x-text-input type="text" name="search" placeholder="Cari NIK..." value="{{ request('search') }}" class="w-full md:w-1/3" />
                        <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full md:w-1/4">
                            <option value="">Semua Status</option>
                            <option value="SUBMITTED" {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
                            <option value="RT_REVIEW" {{ request('status') == 'RT_REVIEW' ? 'selected' : '' }}>RT_REVIEW</option>
                            <option value="RW_REVIEW" {{ request('status') == 'RW_REVIEW' ? 'selected' : '' }}>RW_REVIEW</option>
                            <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                        </select>
                        <x-primary-button>Filter</x-primary-button>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Surat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($letters as $letter)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $letter->pengajuan_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $letter->jenis_surat->value }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $letter->pemohon->nama_lengkap ?? $letter->nik }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($letter->current_status->value === 'COMPLETED') bg-green-100 text-green-800 
                                                @elseif($letter->current_status->value === 'REJECTED') bg-red-100 text-red-800 
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ $letter->current_status->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('letters.show', $letter->pengajuan_id) }}" class="btn-detail-action">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada pengajuan surat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $letters->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
