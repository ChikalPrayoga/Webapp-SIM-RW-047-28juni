<x-public-layout>
    <div class="max-w-md mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-extrabold text-[#37474F]">Cek Riwayat Iuran Warga</h1>
            <p class="text-slate-500 text-sm">
                Masukkan Nomor Kartu Keluarga (KK) dan NIK salah satu anggota keluarga untuk memverifikasi kepemilikan data.
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <form method="POST" action="{{ route('portal.finance.verify') }}" class="space-y-6">
                @csrf

                <!-- Nomor KK -->
                <div>
                    <x-input-label for="no_kk" :value="__('Nomor Kartu Keluarga (KK)')" />
                    <x-text-input id="no_kk" name="no_kk" type="text" class="mt-1 block w-full" :value="old('no_kk')" placeholder="Contoh: 3201xxxxxxxxxxxx" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('no_kk')" />
                </div>

                <!-- NIK -->
                <div>
                    <x-input-label for="nik" :value="__('NIK Anggota Keluarga')" />
                    <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full" :value="old('nik')" placeholder="Contoh: 3201xxxxxxxxxxxx" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nik')" />
                </div>

                <div class="flex items-center justify-between border-t pt-4">
                    <a href="{{ route('portal.finance.index') }}" class="text-sm text-slate-600 hover:text-slate-900 font-medium">Lihat Transparansi Kas</a>
                    <x-primary-button class="bg-[#004D40] hover:bg-[#00382E]">
                        {{ __('Verifikasi Identitas') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
