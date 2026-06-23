<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Jenis Iuran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    
                    <div class="border-b pb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Jenis Iuran</h3>
                        @can('update', $iuranType)
                            <a href="{{ route('finances.iuran-types.edit', $iuranType->id) }}" class="text-sm bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1.5 rounded-lg font-medium transition">Edit</a>
                        @endcan
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Jenis Iuran</div>
                            <div class="mt-1 text-base font-bold text-gray-900">{{ $iuranType->name }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nominal Default</div>
                            <div class="mt-1 text-base font-bold text-gray-900">Rp {{ number_format($iuranType->default_nominal, 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sifat Iuran</div>
                            <div class="mt-1 text-sm text-gray-900">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $iuranType->type->value === 'WAJIB' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $iuranType->type->value }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</div>
                            <div class="mt-1 text-sm text-gray-900">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $iuranType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $iuranType->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Deskripsi / Alokasi Penggunaan</div>
                            <div class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border">{{ $iuranType->description ?? 'Tidak ada deskripsi.' }}</div>
                        </div>
                    </div>

                    <div class="border-t pt-4 flex gap-4">
                        <a href="{{ route('finances.iuran-types.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">&larr; Kembali ke Daftar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
