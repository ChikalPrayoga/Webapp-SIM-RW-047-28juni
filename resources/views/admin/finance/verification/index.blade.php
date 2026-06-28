<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validasi Administratif Iuran Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                    
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-4">Antrean Audit & Pencocokan Berkas</h3>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. KK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Iuran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Input</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Transfer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pendingContributions as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $item->no_kk }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->iuranType->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->formatted_period }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                            Rp {{ number_format($item->nominal, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($item->payment_proof_path)
                                                <a href="{{ route('finances.receipts.download', $item->iuran_id) }}" target="_blank" class="btn-detail-action">Lihat Bukti</a>
                                            @else
                                                <span class="text-gray-400">Tanpa Bukti (Tunai/Luring)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                                            <!-- Approve Form -->
                                            <form action="{{ route('finances.verifications.approve', $item->iuran_id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin data fisik setoran iuran ini sudah cocok dengan sistem?')">
                                                @csrf
                                                <button type="submit" class="btn-detail-action">Approve</button>
                                            </form>

                                            <!-- Reject Button -->
                                            <button onclick="triggerRejection({{ $item->iuran_id }}, '{{ $item->no_kk }}')" class="btn-delete-action">Reject</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Antrean validasi iuran kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pendingContributions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal Form -->
    <div id="rejectionModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectionModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="rejectionForm" method="POST" action="">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Tolak/Batalkan Pencatatan Iuran KK: <span id="targetKk" class="font-bold text-red-600"></span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Pencatatan iuran akan dibatalkan, dan jika ada kas masuk terkait, sistem akan otomatis melakukan reversal kas keluar. Silakan berikan alasan penolakan secara jelas.
                        </p>
                        <div class="mt-4">
                            <x-input-label for="rejection_notes" :value="__('Alasan Penolakan Administratif')" />
                            <textarea id="rejection_notes" name="rejection_notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required placeholder="Contoh: Nominal setoran fisik tidak sesuai dengan sistem..."></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Posting Penolakan
                        </button>
                        <button type="button" onclick="closeRejectionModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function triggerRejection(id, kkNumber) {
            const form = document.getElementById('rejectionForm');
            form.action = `/finances/verifications/${id}/reject`;
            document.getElementById('targetKk').innerText = kkNumber;
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
