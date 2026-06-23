<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jenis Iuran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('finances.iuran-types.update', $iuranType->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nama Iuran -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Jenis Iuran')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $iuranType->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi / Alokasi')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $iuranType->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Nominal Default -->
                        <div>
                            <x-input-label for="default_nominal" :value="__('Tarif Default (Rp)')" />
                            <x-text-input id="default_nominal" name="default_nominal" type="number" step="0.01" class="mt-1 block w-full" :value="old('default_nominal', $iuranType->default_nominal)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('default_nominal')" />
                        </div>

                        <!-- Tipe Iuran -->
                        <div>
                            <x-input-label for="type" :value="__('Sifat Iuran')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="WAJIB" {{ old('type', $iuranType->type->value) == 'WAJIB' ? 'selected' : '' }}>WAJIB</option>
                                <option value="SUKARELA" {{ old('type', $iuranType->type->value) == 'SUKARELA' ? 'selected' : '' }}>SUKARELA</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <!-- Status Keaktifan -->
                        <div>
                            <x-input-label for="is_active" :value="__('Status')" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="1" {{ old('is_active', $iuranType->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active', $iuranType->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>

                        <div class="flex items-center gap-4 border-t pt-4">
                            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                            <a href="{{ route('finances.iuran-types.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
