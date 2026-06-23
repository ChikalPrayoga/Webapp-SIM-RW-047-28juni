<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Iuran Tunai Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Messages -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('finances.contributions.store') }}" class="space-y-6">
                        @csrf

                        <!-- Kartu Keluarga Warga -->
                        <div>
                            <x-input-label for="no_kk" :value="__('Kartu Keluarga Warga')" />
                            <select id="no_kk" name="no_kk" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Pilih KK Warga</option>
                                @foreach($families as $kk)
                                    <option value="{{ $kk->no_kk }}" {{ old('no_kk') == $kk->no_kk ? 'selected' : '' }}>
                                        KK: {{ $kk->no_kk }} (Alamat: {{ $kk->alamat_lengkap ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('no_kk')" />
                        </div>

                        <!-- Jenis Iuran -->
                        <div>
                            <x-input-label for="iuran_type_id" :value="__('Jenis Iuran')" />
                            <select id="iuran_type_id" name="iuran_type_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="updateDefaultNominal(this)" required>
                                <option value="">Pilih Jenis Iuran</option>
                                @foreach($iuranTypes as $type)
                                    <option value="{{ $type->id }}" data-nominal="{{ $type->default_nominal }}" {{ old('iuran_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} (Tarif default: Rp {{ number_format($type->default_nominal, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('iuran_type_id')" />
                        </div>

                        <!-- Nominal Pembayaran -->
                        <div>
                            <x-input-label for="nominal" :value="__('Nominal yang Dibayarkan Warga (Rp)')" />
                            <x-text-input id="nominal" name="nominal" type="number" step="0.01" class="mt-1 block w-full" :value="old('nominal')" required placeholder="Masukkan nominal tunai..." />
                            <x-input-error class="mt-2" :messages="$errors->get('nominal')" />
                        </div>

                        <!-- Periode Bulan & Tahun -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="periode_bulan" :value="__('Periode Bulan')" />
                                <select id="periode_bulan" name="periode_bulan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('periode_bulan', now()->month) == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('periode_bulan')" />
                            </div>

                            <div>
                                <x-input-label for="periode_tahun" :value="__('Periode Tahun')" />
                                <select id="periode_tahun" name="periode_tahun" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                        <option value="{{ $y }}" {{ old('periode_tahun', now()->year) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('periode_tahun')" />
                            </div>
                        </div>

                        <!-- Tanggal Penerimaan Tunai -->
                        <div>
                            <x-input-label for="tanggal_pembayaran" :value="__('Tanggal Pembayaran / Penerimaan')" />
                            <x-text-input id="tanggal_pembayaran" name="tanggal_pembayaran" type="date" class="mt-1 block w-full" :value="old('tanggal_pembayaran', now()->toDateString())" max="{{ now()->toDateString() }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('tanggal_pembayaran')" />
                        </div>

                        <div class="flex items-center gap-4 border-t pt-4">
                            <x-primary-button>{{ __('Simpan Catatan Iuran') }}</x-primary-button>
                            <a href="{{ route('finances.contributions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDefaultNominal(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const nominal = selectedOption.getAttribute('data-nominal');
            if (nominal) {
                document.getElementById('nominal').value = parseFloat(nominal).toFixed(2);
            }
        }
    </script>
</x-app-layout>
