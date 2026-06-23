<x-public-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-extrabold text-[#37474F]">Konfirmasi Pembayaran Iuran</h1>
            <p class="text-slate-500 text-sm">
                Buku Kas SIM RW 047 berbasis administrasi manual/offline. Untuk membayar iuran, silakan hubungi Ketua RT Anda secara langsung.
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="space-y-4">
                <h3 class="font-bold text-slate-800 border-b pb-2">Informasi Pembayaran Offline</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Sistem ini tidak menerima pembayaran digital (transfer, QRIS, dll.) secara langsung. Seluruh pembayaran iuran dilakukan secara tunai/luring langsung kepada Ketua RT masing-masing.
                </p>
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-sm text-indigo-900 leading-relaxed">
                    <strong>Catatan:</strong> Setelah Anda menyerahkan uang iuran tunai secara luring, Ketua RT akan melakukan pencatatan di dashboard sistem. Anda dapat memantau status auditnya di halaman <a href="{{ route('portal.finance.history') }}" class="underline font-bold">Riwayat Iuran Keluarga</a>.
                </div>
            </div>

            <div class="space-y-3">
                <h4 class="font-bold text-slate-800 text-sm">Daftar Tarif Iuran Aktif:</h4>
                <div class="divide-y border rounded-xl overflow-hidden bg-gray-50">
                    @forelse($iuranTypes as $type)
                        <div class="flex justify-between items-center p-3 text-sm">
                            <span class="font-medium text-slate-800">{{ $type->name }}</span>
                            <span class="font-bold text-teal-800">Rp {{ number_format($type->default_nominal, 2, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="p-3 text-center text-xs text-slate-400">Tidak ada tarif iuran terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <div class="border-t pt-4 flex justify-between items-center">
                <a href="{{ route('portal.finance.history') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">&larr; Kembali ke Riwayat</a>
            </div>
        </div>

    </div>
</x-public-layout>
