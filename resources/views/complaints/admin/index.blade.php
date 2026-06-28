<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Form Filter & Search (Minimal) -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('complaints.index') }}" class="flex flex-col sm:flex-row gap-4">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keluhan..." class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-auto" autocomplete="off">
                            <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-auto">
                                <option value="">Semua Status</option>
                                @foreach(\App\Enums\ComplaintStatusEnum::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $status->value) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('complaints.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">Reset</a>
                        </form>
                    </div>

                    <!-- Tabel Keluhan -->
                    <div class="overflow-x-auto whitespace-nowrap">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelapor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori / Prioritas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lapor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($complaints as $complaint)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $complaint->aspirasi_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $complaint->pelapor->nama_lengkap ?? 'Anonim/Tidak Terdaftar' }}<br>
                                            <span class="text-xs text-gray-400">{{ $complaint->nik }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $complaint->ai_category->value ?? $complaint->ai_category ?? '-' }}
                                            @if($complaint->ai_priority)
                                                @php
                                                    $priColor = match($complaint->ai_priority) {
                                                        'CRITICAL', 'HIGH' => 'text-red-600 font-bold',
                                                        'MEDIUM' => 'text-yellow-600',
                                                        'LOW' => 'text-blue-600',
                                                        default => 'text-gray-500',
                                                    };
                                                @endphp
                                                <span class="ml-2 {{ $priColor }}">[{{ $complaint->ai_priority }}]</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @php
                                                $statusColor = match($complaint->current_status->value ?? $complaint->current_status) {
                                                    'SUBMITTED' => 'bg-blue-100 text-blue-800',
                                                    'CLASSIFIED' => 'bg-purple-100 text-purple-800',
                                                    'IN_PROGRESS' => 'bg-yellow-100 text-yellow-800',
                                                    'RESOLVED' => 'bg-green-100 text-green-800',
                                                    'CLOSED' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ str_replace('_', ' ', $complaint->current_status->value ?? $complaint->current_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $complaint->submitted_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('complaints.show', $complaint->aspirasi_id) }}" class="btn-detail-action">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data</h3>
                                            <p class="mt-1 text-sm text-gray-500">Tidak ada laporan keluhan yang sesuai dengan pencarian Anda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $complaints->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
