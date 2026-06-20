<x-public-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <h2 class="text-2xl font-bold mb-4">Pengajuan Surat Pengantar</h2>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('public.letters.store') }}" id="letterForm">
            @csrf

            <!-- NIK -->
            <div>
                <x-input-label for="nik" :value="__('NIK')" />
                <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required autofocus />
                <x-input-error :messages="$errors->get('nik')" class="mt-2" />
            </div>

            <!-- Jenis Surat -->
            <div class="mt-4">
                <x-input-label for="jenis_surat" :value="__('Jenis Surat')" />
                <select id="jenis_surat" name="jenis_surat" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Pilih Jenis Surat</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ old('jenis_surat') == $type->value ? 'selected' : '' }}>
                            {{ ucwords(strtolower(str_replace('_', ' ', $type->value))) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('jenis_surat')" class="mt-2" />
            </div>

            <!-- Keperluan -->
            <div class="mt-4">
                <x-input-label for="keperluan" :value="__('Keperluan')" />
                <textarea id="keperluan" name="keperluan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" required>{{ old('keperluan') }}</textarea>
                <x-input-error :messages="$errors->get('keperluan')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('public.letters.track') }}" class="underline text-sm text-gray-600 hover:text-gray-900 mr-4">Lacak Surat</a>
                <x-primary-button class="ml-4" onclick="this.disabled=true; this.form.submit();">
                    {{ __('Ajukan Surat') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-public-layout>
