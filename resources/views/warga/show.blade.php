<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Warga: ') }} {{ $warga->nama_lengkap }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-bold text-brand-dark dark:text-white mb-4">Informasi Biodata</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">NIK</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $warga->nik }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">No KK</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">
                                <a href="{{ route('kk.show', $warga->no_kk) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-brand-secondary dark:hover:text-white font-semibold underline">{{ $warga->no_kk }}</a>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $warga->nama_lengkap }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">Tempat, Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $warga->tempat_lahir }}, {{ $warga->tanggal_lahir->format('d-m-Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">Jenis Kelamin</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $warga->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">No HP</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $warga->nomor_hp ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-slate-500 dark:text-white/80">Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-brand-dark dark:text-white font-medium">{{ $warga->pekerjaan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($warga->changeRequests->count() > 0)
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="w-full">
                    <h3 class="text-lg font-bold text-brand-dark dark:text-white mb-4">Riwayat Pengajuan Perubahan Data</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Field</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Nilai Lama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Nilai Baru</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-brand-dark uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach ($warga->changeRequests as $req)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $req->submitted_at }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ $req->field_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ Str::limit($req->old_value, 20) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-dark dark:text-white">{{ Str::limit($req->new_value, 20) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $req->current_status->value == 'APPROVED' ? 'bg-green-100 text-green-800' : ($req->current_status->value == 'REJECTED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $req->current_status->value }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
