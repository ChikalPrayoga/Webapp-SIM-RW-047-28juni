<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <a href="{{ route('complaints.index') }}" class="text-indigo-600 hover:text-indigo-500 mr-2">&larr;</a>
            {{ __('Detail Keluhan #') }}{{ $complaint->aspirasi_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 p-4 rounded-md bg-green-50 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Split Layout for Desktop (Grid 3 Columns: 2 Left, 1 Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Info, Attachments, Forms -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Informasi Laporan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Laporan</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Pelapor</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $complaint->pelapor->nama_lengkap ?? 'Anonim' }} (NIK: {{ $complaint->nik }})</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Kanal Laporan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $complaint->kanal_laporan }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Pengajuan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $complaint->submitted_at->format('d F Y, H:i') }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Kategori & Prioritas</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $complaint->ai_category->value ?? $complaint->ai_category ?? 'Belum Diklasifikasi' }}
                                        @if($complaint->ai_priority)
                                            <span class="font-bold {{ in_array($complaint->ai_priority, ['HIGH','CRITICAL']) ? 'text-red-600' : 'text-gray-900' }}">[{{ $complaint->ai_priority }}]</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Deskripsi Keluhan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap p-3 bg-gray-50 rounded-md border">{{ $complaint->teks_keluhan }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Attachment Review -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Lampiran Bukti</h3>
                        </div>
                        <div class="p-6">
                            @if($complaint->attachments->count() > 0)
                                <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                    @foreach($complaint->attachments as $attachment)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-2 flex-1 w-0 truncate"> {{ $attachment->file_name }} </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ route('complaints.attachments.download', $attachment->attachment_id) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                Download
                                            </a>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">Tidak ada lampiran disertakan.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Status Management Form -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Update Status</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('complaints.updateStatus', $complaint->aspirasi_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="status" value="Status Laporan" />
                                        <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            @foreach(\App\Enums\ComplaintStatusEnum::cases() as $status)
                                                @php $curr = $complaint->current_status->value ?? $complaint->current_status; @endphp
                                                <option value="{{ $status->value }}" {{ $curr === $status->value ? 'selected' : '' }}>
                                                    {{ str_replace('_', ' ', $status->value) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="category" value="Kategori (Optional)" />
                                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">-- Biarkan Kosong --</option>
                                            @foreach(\App\Enums\ComplaintCategoryEnum::cases() as $cat)
                                                @php $currCat = $complaint->ai_category->value ?? $complaint->ai_category; @endphp
                                                <option value="{{ $cat->value }}" {{ $currCat === $cat->value ? 'selected' : '' }}>
                                                    {{ $cat->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="priority" value="Prioritas (Optional)" />
                                        <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">-- Set Default --</option>
                                            @foreach(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'] as $pri)
                                                <option value="{{ $pri }}" {{ $complaint->ai_priority === $pri ? 'selected' : '' }}>
                                                    {{ $pri }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <x-input-label for="notes" value="Catatan Internal / Pesan Resolusi" />
                                        <textarea id="notes" name="notes" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <x-primary-button>Update Status</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Assignment Management Form -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Delegasikan ke Staf</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('complaints.assign', $complaint->aspirasi_id) }}" method="POST">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="assigned_to_user_id" value="Pilih Pengurus / Petugas" />
                                        <select id="assigned_to_user_id" name="assigned_to_user_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">-- Pilih Petugas --</option>
                                            @foreach($usersList as $u)
                                                <option value="{{ $u->user_id }}">{{ $u->full_name }} ({{ $u->role->role_name ?? 'N/A' }})</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('assigned_to_user_id')" class="mt-2" />
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <x-input-label for="assign_notes" value="Pesan Penugasan" />
                                        <textarea id="assign_notes" name="notes" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Contoh: Tolong segera tindaklanjuti laporan ini di lapangan."></textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <x-primary-button class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">Tugaskan Tiket</x-primary-button>
                                </div>
                            </form>
                            
                            <!-- Daftar Penugasan yang sudah ada -->
                            @if($complaint->assignments && $complaint->assignments->count() > 0)
                                <div class="mt-6 border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Riwayat Delegasi:</h4>
                                    <ul class="space-y-2">
                                        @foreach($complaint->assignments as $assign)
                                            <li class="bg-gray-50 p-2 rounded text-xs text-gray-600 border">
                                                Ditugaskan ke <span class="font-bold">{{ $assign->assignedTo->full_name ?? 'N/A' }}</span> 
                                                pada {{ $assign->assigned_at->format('d M H:i') }}
                                                @if($assign->notes) <br><span class="italic text-gray-500">"{{ $assign->notes }}"</span> @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div> <!-- End Left Column -->

                <!-- Right Column: Timeline -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Linimasa Riwayat</h3>
                        </div>
                        <div class="p-6 max-h-screen overflow-y-auto">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($histories as $index => $history)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white bg-indigo-500">
                                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            Status <span class="font-medium text-gray-900">{{ str_replace('_', ' ', $history->new_status->value ?? $history->new_status) }}</span>
                                                            <br>oleh <span class="font-medium text-gray-900">{{ $history->actor->full_name ?? 'Sistem' }}</span>
                                                        </p>
                                                        @if($history->notes)
                                                        <p class="text-sm text-gray-500 mt-1 italic">"{{ $history->notes }}"</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $history->changed_at }}">{{ $history->changed_at->format('d M H:i') }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                    @if($histories->count() == 0)
                                        <p class="text-sm text-gray-500">Belum ada riwayat transisi.</p>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Right Column -->

            </div> <!-- End Split Layout -->
        </div>
    </div>
</x-app-layout>
