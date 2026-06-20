<x-public-layout>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h2 class="text-2xl font-bold">Detail Pengajuan Surat #{{ $letter->pengajuan_id }}</h2>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($letter->current_status->value === 'COMPLETED') bg-green-200 text-green-800
                    @elseif($letter->current_status->value === 'REJECTED') bg-red-200 text-red-800
                    @else bg-blue-200 text-blue-800 @endif
                ">
                    {{ $letter->current_status->value }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500">Pemohon</h3>
                    <p class="text-lg">{{ $letter->pemohon->nama_lengkap ?? 'Tidak diketahui' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500">NIK</h3>
                    <p class="text-lg">{{ $letter->nik }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500">Jenis Surat</h3>
                    <p class="text-lg">{{ ucwords(strtolower(str_replace('_', ' ', $letter->jenis_surat->value))) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500">Nomor Surat</h3>
                    <p class="text-lg">{{ $letter->nomor_surat ?? '-' }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-500">Keperluan</h3>
                <p class="text-gray-800 mt-1 p-3 bg-gray-50 rounded">{{ $letter->keperluan }}</p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Timeline Histori</h3>
            <div class="relative border-l border-gray-200 ml-3">
                @foreach($letter->statusHistories as $history)
                    <div class="mb-8 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white">
                            <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                        <h4 class="mb-1 text-lg font-semibold text-gray-900">{{ $history->new_status->value }}</h4>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400">{{ $history->changed_at->format('d M Y, H:i') }}</time>
                        @if($history->notes)
                            <p class="text-base font-normal text-gray-500 bg-gray-50 p-2 rounded mt-2">{{ $history->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-public-layout>
