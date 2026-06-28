<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buku Kas Utama (General Ledger)') }}
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
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    
                    <!-- Search & Filter Form -->
                    <form method="GET" action="{{ route('finances.transactions.index') }}" class="flex flex-col md:flex-row gap-4 justify-between items-center border-b pb-6">
                        <div class="w-full md:w-auto flex flex-wrap gap-3">
                            <!-- Search -->
                            <x-text-input type="text" name="search" placeholder="Cari nomor TRX / rincian..." value="{{ request('search') }}" class="w-full md:w-64 text-sm" />

                            <!-- Type Filter -->
                            <select name="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Tipe</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                                @endforeach
                            </select>

                            <!-- Category Filter -->
                            <select name="category" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->value }}" {{ request('category') == $category->value ? 'selected' : '' }}>{{ $category->value }}</option>
                                @endforeach
                            </select>

                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('finances.transactions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">Reset</a>
                        </div>

                        <!-- Add Button -->
                        @can('create', App\Models\FinancialTransaction::class)
                            <div class="flex gap-2 w-full md:w-auto justify-end">
                                <a href="{{ route('finances.transactions.create', ['type' => 'INCOME']) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-lg text-sm flex items-center gap-1 transition">
                                    + Catat Pemasukan
                                </a>
                                <a href="{{ route('finances.transactions.create', ['type' => 'EXPENSE']) }}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded-lg text-sm flex items-center gap-1 transition">
                                    + Catat Pengeluaran
                                </a>
                            </div>
                        @endcan
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor TRX</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kas Wilayah</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $tx)
                                    <tr class="{{ $tx->adjusted_transaction_id ? 'bg-gray-50 opacity-70' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            <a href="{{ route('finances.transactions.show', $tx->transaction_id) }}">{{ $tx->transaction_number }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ $tx->category->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($tx->isIncome())
                                                <span class="text-green-600 font-bold">MASUK</span>
                                            @else
                                                <span class="text-red-600 font-bold">KELUAR</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $tx->rt_code ?? 'RW (Induk)' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                            Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($tx->adjusted_transaction_id)
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800" title="Transaksi telah dikoreksi">
                                                    Ter-koreksi
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                                            <a href="{{ route('finances.transactions.show', $tx->transaction_id) }}" class="btn-detail-action">Detail</a>

                                            @can('reverse', $tx)
                                                <!-- Inline Reversal Action using a prompt/modal reason -->
                                                <button onclick="triggerReversal({{ $tx->transaction_id }}, '{{ $tx->transaction_number }}')" class="btn-edit-action">Koreksi (Reversal)</button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada riwayat transaksi kas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Reversal Modal Form -->
    <div id="reversalModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeReversalModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="reversalForm" method="POST" action="">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Koreksi Transaksi <span id="targetTrxNumber" class="font-bold text-orange-600"></span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Tindakan ini akan memposting transaksi penyesuaian (reversal) dengan nilai berlawanan guna menetralkan saldo. Transaksi awal akan ditandai ter-koreksi.
                        </p>
                        <div class="mt-4">
                            <x-input-label for="reason" :value="__('Alasan Koreksi / Pembatalan')" />
                            <textarea id="reason" name="reason" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required placeholder="Masukkan alasan penyesuaian kas..."></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:w-auto sm:text-sm">
                            Posting Koreksi
                        </button>
                        <button type="button" onclick="closeReversalModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function triggerReversal(id, trxNumber) {
            const form = document.getElementById('reversalForm');
            form.action = `/finances/transactions/${id}/reverse`;
            document.getElementById('targetTrxNumber').innerText = trxNumber;
            document.getElementById('reversalModal').classList.remove('hidden');
        }

        function closeReversalModal() {
            document.getElementById('reversalModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
