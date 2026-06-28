<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengurus') }}
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

            <!-- Quick Access Menu -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('warga.index') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                    <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <span class="font-medium text-gray-700">Data Warga</span>
                </a>
                <a href="{{ route('kk.index') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span class="font-medium text-gray-700">Kartu Keluarga</span>
                </a>
                <a href="{{ route('letters.index') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    <span class="font-medium text-gray-700">Surat Pengantar</span>
                </a>
                <a href="{{ route('complaints.index') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                    <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                    <span class="font-medium text-gray-700">Laporan / Aspirasi</span>
                </a>
            </div>

            <!-- Statistic Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Penduduk</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['total_warga']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Dari {{ number_format($metrics['total_kk']) }} Kartu Keluarga</p>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Surat Menunggu</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['surat_pending']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Total diproses: {{ number_format($metrics['total_surat']) }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Laporan Menunggu</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['laporan_pending']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Total masuk: {{ number_format($metrics['total_laporan']) }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <dt class="text-sm font-medium text-gray-500 truncate">Surat / Laporan Selesai</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($metrics['surat_selesai'] + $metrics['laporan_selesai']) }}</dd>
                    <p class="text-xs text-gray-500 mt-2">Pelayanan tuntas</p>
                </div>
            </div>

            <!-- Recent Lists -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Letters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">5 Surat Terbaru</h3>
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
                                        <a href="{{ route('letters.show', $surat->pengajuan_id) }}" class="btn-detail-action">Detail</a>
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
                        <h3 class="font-bold text-gray-800">5 Laporan Terbaru</h3>
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
                                        <a href="{{ route('complaints.show', $laporan->aspirasi_id) }}" class="btn-detail-action">Detail</a>
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
