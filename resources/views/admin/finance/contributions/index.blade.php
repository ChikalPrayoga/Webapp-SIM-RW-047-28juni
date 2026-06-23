<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Catatan Iuran Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    
                    <!-- Search & Filter Form -->
                    <form method="GET" action="{{ route('finances.contributions.index') }}" class="flex flex-col md:flex-row gap-4 justify-between items-center border-b pb-6">
                        <div class="w-full md:w-auto flex flex-wrap gap-3">
                            <!-- Search KK -->
                            <x-text-input type="text" name="search" placeholder="Cari nomor KK..." value="{{ request('search') }}" class="w-full md:w-64 text-sm" />

                            <!-- Iuran Type Filter -->
                            <select name="iuran_type_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Jenis Iuran</option>
                                @foreach($iuranTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('iuran_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>

                            <!-- Status Filter -->
                            <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Status</option>
                                <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                                <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                            </select>

                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('finances.contributions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">Reset</a>
                        </div>

                        <!-- Add Button -->
                        @can('create', App\Models\CatatanIuranWarga::class)
                            <a href="{{ route('finances.contributions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm flex items-center gap-1 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Catat Iuran Tunai
                            </a>
                        @endcan
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. KK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Iuran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Input</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($contributions as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $item->no_kk }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->iuranType->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->formatted_period }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                            Rp {{ number_format($item->nominal, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                @if($item->isApproved()) bg-green-100 text-green-800
                                                @elseif($item->isRejected()) bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $item->status->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('finances.contributions.show', $item->iuran_id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada riwayat catatan iuran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $contributions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
