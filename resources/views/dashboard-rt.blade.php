<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard RT') }} {{ auth()->user()->position ? auth()->user()->position->area_code : '' }}
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
                            @if(auth()->user()->position)
                                Wilayah: <span class="font-semibold">{{ auth()->user()->position->area_code }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </div>

            <!-- RT Approval Alert -->
            @if($metrics['approval_surat_rt'] > 0)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Pemberitahuan Validasi
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Ada {{ $metrics['approval_surat_rt'] }} pengajuan surat dari warga wilayah Anda yang menunggu review dan persetujuan Pengantar RT.</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('letters.index') }}" class="text-sm font-medium text-blue-800 hover:text-blue-900 bg-blue-100 px-3 py-1 rounded">Lihat Antrean Surat RT &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistic Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Warga RT</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['total_warga']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Jiwa terdaftar</p>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">KK RT</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['total_kk']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Keluarga terdaftar</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Surat RT Diproses</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['surat_pending']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Menunggu RT/RW</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Laporan RT Aktif</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['laporan_pending']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Keluhan warga area Anda</p>
                </div>
            </div>

            <!-- Recent Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Letters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Surat Warga RT Terbaru</h3>
                        <a href="{{ route('letters.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Lihat Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentSurat as $surat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $surat->pemohon->nama_lengkap ?? $surat->nik }}</div>
                                        <div class="text-xs text-gray-500">{{ $surat->jenis_surat->value }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($surat->current_status->value === 'COMPLETED') bg-green-100 text-green-800 
                                            @elseif($surat->current_status->value === 'REJECTED') bg-red-100 text-red-800 
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $surat->current_status->value }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('letters.show', $surat->pengajuan_id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada surat</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Complaints -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Laporan Warga RT Terbaru</h3>
                        <a href="{{ route('complaints.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Lihat Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentLaporan as $laporan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-[200px]">{{ $laporan->teks_keluhan }}</div>
                                        <div class="text-xs text-gray-500">{{ $laporan->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @php
                                            $cStatus = $laporan->current_status->value ?? $laporan->current_status;
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ in_array($cStatus, ['RESOLVED', 'CLOSED']) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $cStatus }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('complaints.show', $laporan->aspirasi_id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada laporan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
