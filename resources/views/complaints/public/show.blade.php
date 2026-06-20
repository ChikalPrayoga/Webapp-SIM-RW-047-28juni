<x-public-layout>
    <div class="mb-6">
        <a href="{{ route('public.complaints.track') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-4 inline-flex items-center">
            &larr; Kembali
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Detail Laporan #{{ $complaint->aspirasi_id }}</h2>
        <p class="mt-1 text-sm text-gray-500">Kanal Laporan: {{ $complaint->kanal_laporan }}</p>
    </div>

    <!-- Status Badge -->
    <div class="mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Status Saat Ini</h3>
            @php
                $statusColor = match($complaint->current_status->value ?? $complaint->current_status) {
                    'SUBMITTED' => 'bg-blue-100 text-blue-800',
                    'CLASSIFIED' => 'bg-purple-100 text-purple-800',
                    'IN_PROGRESS' => 'bg-yellow-100 text-yellow-800',
                    'RESOLVED' => 'bg-green-100 text-green-800',
                    'CLOSED' => 'bg-gray-100 text-gray-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                {{ str_replace('_', ' ', $complaint->current_status->value ?? $complaint->current_status) }}
            </span>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Teks Keluhan</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $complaint->teks_keluhan }}</dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $complaint->ai_category->value ?? $complaint->ai_category ?? '-' }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Pengajuan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $complaint->submitted_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Lampiran -->
    @if($complaint->attachments->count() > 0)
    <div class="mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Lampiran Bukti</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                @foreach($complaint->attachments as $attachment)
                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                    <div class="w-0 flex-1 flex items-center">
                        <!-- Heroicon paper-clip -->
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-2 flex-1 w-0 truncate"> {{ $attachment->file_name }} </span>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <!-- TODO: In Phase 2 this is restricted to admin, for public we might need a presigned URL or public route with signed token. Currently just displaying name. -->
                        <span class="text-gray-400 text-xs">(File Terekam)</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Riwayat Status -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Riwayat Laporan</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($complaint->statusHistories as $index => $history)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white bg-indigo-500">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Status diubah menjadi <span class="font-medium text-gray-900">{{ str_replace('_', ' ', $history->new_status->value ?? $history->new_status) }}</span></p>
                                        @if($history->notes)
                                        <p class="text-sm text-gray-500 mt-1 italic">"{{ $history->notes }}"</p>
                                        @endif
                                    </div>
                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                        <time datetime="{{ $history->changed_at }}">{{ $history->changed_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                    @if($complaint->statusHistories->count() == 0)
                        <p class="text-sm text-gray-500">Laporan baru diajukan, menunggu tinjauan dari pengurus.</p>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</x-public-layout>
