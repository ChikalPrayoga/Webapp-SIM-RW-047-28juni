<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catat Mutasi Kas - {{ $type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('finances.transactions.store') }}" class="space-y-6">
                        @csrf
                        
                        <input type="hidden" name="transaction_type" value="{{ $type }}">

                        <!-- Tanggal Transaksi -->
                        <div>
                            <x-input-label for="transaction_date" :value="__('Tanggal Efektif')" />
                            <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full" :value="old('transaction_date', now()->toDateString())" max="{{ now()->toDateString() }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('transaction_date')" />
                        </div>

                        <!-- Kategori -->
                        <div>
                            <x-input-label for="category" :value="__('Kategori Kas')" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->value }}" {{ old('category') == $cat->value ? 'selected' : '' }}>{{ $cat->value }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                        </div>

                        <!-- Nominal (Amount) -->
                        <div>
                            <x-input-label for="amount" :value="__('Nominal Uang (Rp)')" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount')" required placeholder="Contoh: 50000" />
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>

                        <!-- Deskripsi / Rincian -->
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi / Keperluan')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" required placeholder="Tulis rincian penggunaan/alasan transaksi... (min 5 karakter)">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4 border-t pt-4">
                            <x-primary-button class="{{ $type === 'INCOME' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                                {{ $type === 'INCOME' ? 'Posting Pemasukan' : 'Posting Pengeluaran' }}
                            </x-primary-button>
                            <a href="{{ route('finances.transactions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
