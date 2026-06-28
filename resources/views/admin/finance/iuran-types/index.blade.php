<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jenis Iuran Keuangan') }}
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
                    
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('finances.iuran-types.index') }}" class="w-full md:w-auto flex gap-2">
                            <x-text-input type="text" name="search" placeholder="Cari nama iuran..." value="{{ $search }}" class="w-full md:w-80" />
                            <x-primary-button>Cari</x-primary-button>
                        </form>

                        <!-- Add Button -->
                        @can('create', App\Models\IuranType::class)
                            <a href="{{ route('finances.iuran-types.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm inline-flex items-center gap-1 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Jenis Iuran
                            </a>
                        @endcan
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jenis Iuran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Default</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($iuranTypes as $type)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $type->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $type->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($type->default_nominal, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $type->type->value === 'WAJIB' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $type->type->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                            <a href="{{ route('finances.iuran-types.show', $type->id) }}" class="btn-detail-action">Detail</a>
                                            
                                            @can('update', $type)
                                                <a href="{{ route('finances.iuran-types.edit', $type->id) }}" class="btn-edit-action">Edit</a>
                                            @endcan

                                            @can('delete', $type)
                                                <form action="{{ route('finances.iuran-types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan jenis iuran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-delete-action">Hapus</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada jenis iuran ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $iuranTypes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
