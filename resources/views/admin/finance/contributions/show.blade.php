<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Catatan Iuran Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    
                    <div class="border-b pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Catatan Administratif</h3>
                            <span class="text-xs text-gray-400">ID Iuran: #{{ $contribution->iuran_id }}</span>
                        </div>
                        
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($contribution->isApproved()) bg-green-100 text-green-800
                            @elseif($contribution->isRejected()) bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $contribution->status->value }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Kartu Keluarga</div>
                            <div class="mt-1 text-base font-bold text-gray-900">{{ $contribution->no_kk }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis Iuran</div>
                            <div class="mt-1 text-base font-bold text-gray-900">{{ $contribution->iuranType->name }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nominal Pembayaran</div>
                            <div class="mt-1 text-xl font-extrabold text-gray-900">Rp {{ number_format($contribution->nominal, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Periode Bulan/Tahun</div>
                            <div class="mt-1 text-base font-medium text-gray-900">{{ $contribution->formatted_period }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Pembayaran</div>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ $contribution->tanggal_pembayaran ? $contribution->tanggal_pembayaran->format('d F Y') : '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Petugas Penginput</div>
                            <div class="mt-1 text-sm text-gray-900">{{ $contribution->recorder->full_name ?? 'Warga (Portal)' }}</div>
                        </div>

                        <!-- Verification info if audited -->
                        @if($contribution->approved_by_user_id)
                            <div class="md:col-span-2 border-t pt-4 bg-gray-50 p-4 rounded-lg space-y-2 border">
                                <div class="text-sm font-bold text-gray-800">Detail Audit Administratif</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600">
                                    <div>
                                        <span class="font-semibold">Auditor:</span> {{ $contribution->approver->full_name ?? '-' }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Waktu Audit:</span> {{ $contribution->approved_at ? $contribution->approved_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    @if($contribution->isRejected())
                                        <div class="md:col-span-2 text-red-700 font-medium">
                                            <span class="font-semibold text-gray-700">Alasan Penolakan:</span> {{ $contribution->rejection_notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Ledger Reference -->
                        @if($contribution->ledgerEntry)
                            <div class="md:col-span-2 border-t pt-4">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Referensi Mutasi Kas (Ledger)</div>
                                <div class="mt-2 flex items-center justify-between p-3 bg-green-50 border border-green-100 rounded-lg">
                                    <span class="text-sm font-semibold text-green-900">
                                        {{ $contribution->ledgerEntry->transaction_number }} (Nominal: Rp {{ number_format($contribution->ledgerEntry->amount, 2, ',', '.') }})
                                    </span>
                                    <a href="{{ route('finances.transactions.show', $contribution->ledgerEntry->transaction_id) }}" class="text-xs bg-green-600 hover:bg-green-700 text-white font-medium px-3 py-1.5 rounded-lg transition">Lihat Transaksi</a>
                                </div>
                            </div>
                        @endif

                        <!-- Proof of payment download if exists -->
                        @if($contribution->payment_proof_path)
                            <div class="md:col-span-2 border-t pt-4 space-y-2">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Berkas Bukti Transfer</div>
                                <div class="flex items-center gap-4 bg-gray-50 border p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-medium text-gray-900 truncate">{{ $contribution->payment_proof_path }}</span>
                                    </div>
                                    <a href="{{ route('finances.receipts.download', $contribution->iuran_id) }}" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-3 py-1.5 rounded-lg transition" target="_blank">Unduh Bukti</a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="border-t pt-4 flex gap-4">
                        <a href="{{ route('finances.contributions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">&larr; Kembali ke Daftar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
