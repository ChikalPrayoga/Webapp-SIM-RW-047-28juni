<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Keuangan') }} {{ !empty($userArea) ? '- Wilayah ' . $userArea : '' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Info Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Card Saldo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Saldo Kas Anda</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($saldoKas, 2, ',', '.') }}</div>
                </div>

                @if(!empty($userArea))
                <!-- Card Saldo RW (for RT) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-400">
                    <div class="text-sm font-medium text-gray-500 uppercase">Saldo Induk RW (Read-Only)</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($saldoKasRW, 2, ',', '.') }}</div>
                </div>
                @else
                <!-- Pending Verifications Queue (only Bendahara RW) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Antrean Audit Iuran</div>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</span>
                        @if($pendingCount > 0)
                            <a href="{{ route('finances.verifications.index') }}" class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-2.5 py-1 rounded-md font-semibold">Audit Antrean</a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Total Kas Masuk -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Kas Masuk</div>
                    <div class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 2, ',', '.') }}</div>
                </div>

                <!-- Total Kas Keluar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Kas Keluar</div>
                    <div class="mt-2 text-2xl font-bold text-red-600">Rp {{ number_format($totalExpense, 2, ',', '.') }}</div>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Activities Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 lg:col-span-3 space-y-4">
                    <div class="flex justify-between items-center border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-800">5 Transaksi Kas Terbaru</h3>
                        <a href="{{ route('finances.transactions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Lihat Semua</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor TRX</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            <a href="{{ route('finances.transactions.show', $tx->transaction_id) }}">{{ $tx->transaction_number }}</a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ $tx->category->value }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            @if($tx->isIncome())
                                                <span class="text-green-600 font-semibold">MASUK</span>
                                            @else
                                                <span class="text-red-600 font-semibold">KELUAR</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium">
                                            Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada transaksi terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>
    </div>
</x-app-layout>
