<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Kartu Keluarga: ') }} {{ $kk->no_kk }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-bold text-brand-dark dark:text-white mb-4">Informasi Keluarga</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">No KK</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $kk->no_kk }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">RT</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $kk->rt_code }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">Alamat Lengkap</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $kk->alamat_lengkap }}, Blok {{ $kk->blok }}, No. {{ $kk->nomor_rumah }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">Status Rumah</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $kk->status_kepemilikan_rumah }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="w-full">
                    <h3 class="text-lg font-bold text-brand-dark dark:text-white mb-4">Anggota Keluarga</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">NIK</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Hubungan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($kk->anggotaKeluargas as $warga)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $warga->nik }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $warga->nama_lengkap }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $warga->status_hubungan_keluarga }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('warga.show', $warga->nik) }}" class="btn-detail-action">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-brand-dark dark:text-white">Belum ada anggota keluarga.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
