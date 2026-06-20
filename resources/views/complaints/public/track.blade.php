<x-public-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Lacak Laporan</h2>
        <p class="mt-2 text-sm text-gray-600">Pantau progres laporan keluhan yang telah Anda kirimkan.</p>
    </div>

    <form method="POST" action="{{ route('public.complaints.track.post') }}">
        @csrf

        <!-- NIK -->
        <div>
            <x-input-label for="nik" value="NIK Pelapor" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required autofocus autocomplete="off" placeholder="Masukkan 16 digit NIK Anda" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- Nomor Tiket -->
        <div class="mt-4">
            <x-input-label for="aspirasi_id" value="Nomor Tiket (ID Laporan)" />
            <x-text-input id="aspirasi_id" class="block mt-1 w-full" type="number" name="aspirasi_id" :value="old('aspirasi_id')" required autocomplete="off" placeholder="Contoh: 15" />
            <x-input-error :messages="$errors->get('aspirasi_id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('public.complaints.create') }}">
                Buat Laporan Baru
            </a>

            <x-primary-button class="ml-4">
                Lacak Status
            </x-primary-button>
        </div>
    </form>
</x-public-layout>
