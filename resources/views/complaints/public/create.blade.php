<x-public-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Lapor RW 047</h2>
        <p class="mt-2 text-sm text-gray-600">Sampaikan keluhan dan aspirasi Anda untuk lingkungan yang lebih baik.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-md bg-green-50 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">{{ session('success')['message'] }}</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Nomor Tiket Anda: <span class="font-bold text-lg bg-white px-2 py-1 rounded shadow-sm">{{ session('success')['ticket_id'] }}</span></p>
                        <p class="mt-2">{{ session('success')['instruction'] }}</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('public.complaints.track') }}" class="text-sm font-medium text-green-800 hover:text-green-900 underline">Lacak Laporan Sekarang &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('public.complaints.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- NIK -->
        <div>
            <x-input-label for="nik" value="NIK Pelapor" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required autofocus autocomplete="off" placeholder="Masukkan 16 digit NIK" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- Teks Keluhan -->
        <div class="mt-4">
            <x-input-label for="teks_keluhan" value="Isi Laporan / Keluhan" />
            <textarea id="teks_keluhan" name="teks_keluhan" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required placeholder="Jelaskan masalah secara detail...">{{ old('teks_keluhan') }}</textarea>
            <x-input-error :messages="$errors->get('teks_keluhan')" class="mt-2" />
        </div>

        <!-- Lampiran -->
        <div class="mt-4">
            <x-input-label for="attachments" value="Lampiran Foto / Dokumen (Opsional)" />
            <input type="file" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            <p class="mt-1 text-xs text-gray-500">Maks. 5MB per file (JPG, PNG, PDF). Bisa pilih lebih dari 1.</p>
            <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('public.complaints.track') }}">
                Sudah punya tiket? Lacak di sini
            </a>

            <x-primary-button class="ml-4">
                Kirim Laporan
            </x-primary-button>
        </div>
    </form>
</x-public-layout>
