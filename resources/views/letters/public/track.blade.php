<x-public-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <h2 class="text-2xl font-bold mb-4">Lacak Pengajuan Surat</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <span class="block sm:inline">{{ session('success') }}</span>
                @if(session('pengajuan_id'))
                    <div class="font-bold mt-2">Nomor Pengajuan: {{ session('pengajuan_id') }}</div>
                    <div class="text-sm">Harap simpan Nomor Pengajuan dan NIK ini untuk melacak status surat.</div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('public.letters.show') }}">
            @csrf
            <!-- NIK -->
            <div>
                <x-input-label for="nik" :value="__('NIK')" />
                <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="session('nik') ?? old('nik')" required autofocus />
            </div>

            <!-- Nomor Pengajuan -->
            <div class="mt-4">
                <x-input-label for="pengajuan_id" :value="__('Nomor Pengajuan (ID)')" />
                <x-text-input id="pengajuan_id" class="block mt-1 w-full" type="text" name="pengajuan_id" :value="session('pengajuan_id') ?? old('pengajuan_id')" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ml-4">
                    {{ __('Lacak Surat') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-public-layout>
