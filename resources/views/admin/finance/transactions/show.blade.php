<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Transaksi Kas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    
                    <div class="border-b pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Bukti Audit Transaksi</h3>
                            <span class="text-xs text-gray-400">Nomor TRX: {{ $transaction->transaction_number }}</span>
                        </div>
                        
                        @if($transaction->adjusted_transaction_id)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                Transaksi Terkoreksi
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Transaksi Aktif
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tipe Transaksi</div>
                            <div class="mt-1 text-base font-bold {{ $transaction->isIncome() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->transaction_type->value }} ({{ $transaction->isIncome() ? 'Kas Masuk' : 'Kas Keluar' }})
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori Kas</div>
                            <div class="mt-1 text-base font-bold text-gray-900">{{ $transaction->category->value }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nominal Transaksi</div>
                            <div class="mt-1 text-xl font-extrabold text-gray-900">Rp {{ number_format($transaction->amount, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Efektif</div>
                            <div class="mt-1 text-base font-medium text-gray-900">{{ $transaction->transaction_date->format('l, d F Y') }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Wilayah Kas</div>
                            <div class="mt-1 text-sm text-gray-900">{{ $transaction->rt_code ? 'RT ' . $transaction->rt_code : 'RW (Induk)' }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dibuat Oleh</div>
                            <div class="mt-1 text-sm text-gray-900">{{ $transaction->creator->full_name ?? 'Sistem' }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rincian / Deskripsi</div>
                            <div class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border">{{ $transaction->description }}</div>
                        </div>

                        <!-- Polymorphic Reference Document -->
                        @if($transaction->reference)
                            <div class="md:col-span-2 border-t pt-4">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dokumen Sumber (Source Document)</div>
                                <div class="mt-2 flex items-center justify-between p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
                                    <span class="text-sm font-semibold text-indigo-900">
                                        Catatan Iuran Warga (Periode: {{ $transaction->reference->formatted_period }})
                                    </span>
                                    <a href="{{ route('finances.contributions.show', $transaction->reference->iuran_id) }}" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-3 py-1.5 rounded-lg transition">Lihat Dokumen</a>
                                </div>
                            </div>
                        @endif

                        <!-- Audit Log Reversal -->
                        @if($transaction->adjusted_transaction_id)
                            <div class="md:col-span-2 border-t pt-4 bg-orange-50 border border-orange-100 p-4 rounded-lg space-y-2">
                                <div class="text-sm font-bold text-orange-800">Detail Audit Koreksi (Reversal)</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-orange-950">
                                    <div>
                                        <span class="font-semibold">Diproses Oleh:</span> {{ $transaction->adjuster->full_name ?? 'Sistem' }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Waktu Koreksi:</span> {{ $transaction->adjusted_at ? $transaction->adjusted_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="font-semibold">Transaksi Pembanding:</span> 
                                        @if($transaction->reversalTransaction)
                                            <a href="{{ route('finances.transactions.show', $transaction->reversalTransaction->transaction_id) }}" class="underline font-bold">{{ $transaction->reversalTransaction->transaction_number }}</a>
                                        @elseif($transaction->originalTransaction)
                                            <a href="{{ route('finances.transactions.show', $transaction->originalTransaction->transaction_id) }}" class="underline font-bold">{{ $transaction->originalTransaction->transaction_number }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="border-t pt-4 flex gap-4">
                        <a href="{{ route('finances.transactions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">&larr; Kembali ke Daftar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
