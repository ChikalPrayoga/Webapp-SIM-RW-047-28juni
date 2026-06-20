<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Surat #') }}{{ $letter->pengajuan_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Data Pengajuan</h3>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($letter->current_status->value === 'COMPLETED') bg-green-200 text-green-800
                        @elseif($letter->current_status->value === 'REJECTED') bg-red-200 text-red-800
                        @else bg-blue-200 text-blue-800 @endif">
                        {{ $letter->current_status->value }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border-t pt-4">
                    <div>
                        <p class="text-sm text-gray-500">Pemohon</p>
                        <p class="font-semibold">{{ $letter->pemohon->nama_lengkap ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">NIK</p>
                        <p class="font-semibold">{{ $letter->nik }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jenis Surat</p>
                        <p class="font-semibold">{{ $letter->jenis_surat->value }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nomor Surat</p>
                        <p class="font-semibold">{{ $letter->nomor_surat ?? '-' }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Keperluan</p>
                    <p class="bg-gray-50 p-3 rounded mt-1">{{ $letter->keperluan }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Aksi & Proses</h3>
                
                <div class="flex flex-wrap gap-4 border-b pb-6 mb-6">
                    @can('process', $letter)
                    <form method="POST" action="{{ route('letters.process', $letter->pengajuan_id) }}">
                        @csrf
                        <x-primary-button>Mulai Review (RT)</x-primary-button>
                    </form>
                    @endcan

                    @can('forwardToRw', $letter)
                    <form method="POST" action="{{ route('letters.forward', $letter->pengajuan_id) }}">
                        @csrf
                        <x-primary-button class="bg-yellow-600 hover:bg-yellow-700">Teruskan ke RW</x-primary-button>
                    </form>
                    @endcan

                    @can('complete', $letter)
                    <form method="POST" action="{{ route('letters.complete', $letter->pengajuan_id) }}" class="flex gap-2 items-center">
                        @csrf
                        <x-text-input name="nomor_surat" placeholder="Nomor Surat (Opsional)" class="text-sm" />
                        <x-primary-button class="bg-green-600 hover:bg-green-700">Selesaikan (COMPLETED)</x-primary-button>
                    </form>
                    @endcan

                    @can('reject', $letter)
                    <form method="POST" action="{{ route('letters.reject', $letter->pengajuan_id) }}" class="flex gap-2 items-center w-full mt-4">
                        @csrf
                        <x-text-input name="reason" placeholder="Alasan Penolakan..." required class="text-sm w-full md:w-1/2" />
                        <x-danger-button>Tolak (REJECTED)</x-danger-button>
                    </form>
                    @endcan

                    @if(!auth()->user()->can('process', $letter) && !auth()->user()->can('forwardToRw', $letter) && !auth()->user()->can('complete', $letter) && !auth()->user()->can('reject', $letter))
                        <p class="text-gray-500 italic">Tidak ada aksi yang tersedia atau Anda tidak memiliki izin untuk memanipulasi surat pada status saat ini.</p>
                    @endif
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-4">Histori Pergerakan</h3>
                <div class="space-y-4">
                    @foreach($letter->statusHistories as $history)
                    <div class="border-l-4 border-indigo-400 pl-4 py-1">
                        <p class="font-bold text-indigo-700">{{ $history->new_status->value }}</p>
                        <p class="text-sm text-gray-600">{{ $history->changed_at->format('d M Y H:i:s') }} oleh {{ $history->actor->name ?? 'Sistem' }}</p>
                        @if($history->notes)
                            <p class="text-sm text-gray-800 bg-gray-100 p-2 mt-2 rounded">{{ $history->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
