<x-public-layout>
    <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-[#37474F]">Riwayat Catatan Iuran Keluarga Anda</h1>
                <p class="text-sm text-slate-500 mt-1">Kartu Keluarga: <span class="font-bold text-teal-800">{{ $noKk }}</span></p>
            </div>
            
            <div class="flex gap-2">
                <!-- Submit Link (Confirmation Offline Payment tracker) -->
                <a href="{{ route('portal.finance.submit') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-xl text-sm transition">
                    Form Konfirmasi Iuran
                </a>
                
                <form method="POST" action="{{ route('portal.finance.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2 px-4 rounded-xl text-sm transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-[#37474F] mb-4">Daftar Pembayaran Iuran</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Iuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Audit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($contributions as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">{{ $item->iuranType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $item->formatted_period }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-slate-900">
                                    Rp {{ number_format($item->nominal, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->tanggal_pembayaran ? $item->tanggal_pembayaran->format('d/m/Y') : '-' }}
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
                                    @if($item->payment_proof_path)
                                        <a href="{{ route('finances.receipts.download', $item->iuran_id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">Unduh Kuitansi</a>
                                    @else
                                        <span class="text-gray-400">Pembayaran Offline</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada riwayat catatan iuran untuk keluarga Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-public-layout>
