<x-public-layout>
    <div class="max-w-6xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <div class="text-center space-y-2">
            <h1 class="text-3xl font-extrabold text-[#37474F]">Transparansi Arus Kas RW 047</h1>
            <p class="text-slate-500 max-w-xl mx-auto text-sm">
                Informasi mutasi kas masuk dan keluar tingkat RW secara berkala sebagai komitmen keterbukaan keuangan lingkungan.
            </p>
        </div>

        <!-- Saldo Kas RW Card -->
        <div class="bg-gradient-to-r from-teal-800 to-emerald-700 text-white rounded-2xl p-6 sm:p-8 shadow-lg flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-1">
                <span class="text-[#A5D6A7] text-xs font-semibold uppercase tracking-wider">Saldo Utama Kas Induk RW</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold">Rp {{ number_format($saldoRW, 2, ',', '.') }}</h2>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('portal.finance.history') }}" class="bg-white text-teal-900 font-semibold py-2.5 px-5 rounded-xl hover:bg-slate-100 transition shadow-sm text-sm">
                    Cek Riwayat Iuran Keluarga
                </a>
            </div>
        </div>

        <!-- Mutasi Kas Table -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-[#37474F] border-b pb-4">Mutasi Buku Kas RW</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor TRX</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mutasiRW as $tx)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">{{ $tx->transaction_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $tx->category->value }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($tx->isIncome())
                                        <span class="text-emerald-600 font-bold">MASUK</span>
                                    @else
                                        <span class="text-rose-600 font-bold">KELUAR</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-slate-900">
                                    Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $tx->description }}">{{ $tx->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada riwayat transaksi kas RW.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $mutasiRW->links() }}
            </div>
        </div>

    </div>
</x-public-layout>
