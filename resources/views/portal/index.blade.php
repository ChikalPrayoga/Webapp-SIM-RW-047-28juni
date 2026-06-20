<x-public-layout>
    <x-slot name="title">Portal Layanan Warga</x-slot>

    {{-- ===== HERO SECTION ===== --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-[#004D40]/5 to-transparent">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 sm:pt-24 sm:pb-16 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#A5D6A7]/20 border border-[#A5D6A7]/50 rounded-full text-sm font-semibold text-[#004D40] mb-8">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                Layanan Digital 24 Jam
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#37474F] tracking-tight mb-6">
                Portal Layanan Warga
            </h1>
            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Selamat datang di sistem layanan terpadu RW 047. Ajukan surat pengantar atau sampaikan laporan aspirasi Anda dengan mudah dan transparan.
            </p>
        </div>
    </section>

    {{-- ===== SERVICE CARDS ===== --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            {{-- Card: Pengajuan Surat --}}
            <a href="{{ route('public.letters.create') }}" class="group block bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0 w-16 h-16 bg-[#004D40]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#004D40] transition-colors duration-300">
                        <svg class="w-8 h-8 text-[#004D40] group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-[#37474F] group-hover:text-[#004D40] transition-colors mb-2">Ajukan Surat</h2>
                        <p class="text-slate-600 leading-relaxed mb-4">Buat permohonan surat pengantar, surat keterangan domisili, atau surat lainnya secara online.</p>
                        <div class="inline-flex items-center gap-2 text-[#004D40] font-semibold group-hover:gap-3 transition-all">
                            Mulai Pengajuan
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </div>
                    </div>
                </div>
            </a>

            {{-- Card: Buat Laporan / Aspirasi --}}
            <a href="{{ route('public.complaints.create') }}" class="group block bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0 w-16 h-16 bg-[#004D40]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#004D40] transition-colors duration-300">
                        <svg class="w-8 h-8 text-[#004D40] group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-[#37474F] group-hover:text-[#004D40] transition-colors mb-2">Buat Laporan</h2>
                        <p class="text-slate-600 leading-relaxed mb-4">Sampaikan keluhan, aspirasi, atau laporkan kondisi lingkungan untuk ditindaklanjuti.</p>
                        <div class="inline-flex items-center gap-2 text-[#004D40] font-semibold group-hover:gap-3 transition-all">
                            Tulis Laporan
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </section>

    {{-- ===== TRACKING SECTION ===== --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-20" x-data="{ activeTab: 'surat' }">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            
            {{-- Section Header --}}
            <div class="px-6 sm:px-10 pt-8 pb-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-2xl font-bold text-[#37474F] mb-2">Lacak Status Pengajuan</h2>
                <p class="text-slate-600">Masukkan NIK dan Nomor Tiket untuk melihat tahapan proses terkini secara realtime.</p>
            </div>

            {{-- Tab Buttons --}}
            <div class="px-6 sm:px-10 border-b border-slate-200 bg-white">
                <div class="flex gap-8" role="tablist">
                    <button
                        type="button"
                        class="relative py-4 text-sm sm:text-base font-semibold transition-colors focus:outline-none"
                        :class="activeTab === 'surat' ? 'text-[#004D40]' : 'text-slate-500 hover:text-slate-700'"
                        @click="activeTab = 'surat'"
                    >
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            Tracking Surat
                        </span>
                        <div x-show="activeTab === 'surat'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#004D40]"></div>
                    </button>
                    <button
                        type="button"
                        class="relative py-4 text-sm sm:text-base font-semibold transition-colors focus:outline-none"
                        :class="activeTab === 'laporan' ? 'text-[#004D40]' : 'text-slate-500 hover:text-slate-700'"
                        @click="activeTab = 'laporan'"
                    >
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" /></svg>
                            Tracking Laporan
                        </span>
                        <div x-show="activeTab === 'laporan'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#004D40]" style="display: none;"></div>
                    </button>
                </div>
            </div>

            {{-- Tab Panels --}}
            <div class="p-6 sm:p-10 bg-white">
                {{-- Panel: Tracking Surat --}}
                <div x-show="activeTab === 'surat'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" role="tabpanel">
                    <form method="POST" action="{{ route('public.letters.show') }}" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="track_letter_nik" class="block text-sm font-semibold text-[#37474F] mb-1.5">NIK Pemohon</label>
                                <input type="text" id="track_letter_nik" name="nik" required autocomplete="off" placeholder="Masukkan 16 digit NIK" maxlength="16"
                                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#004D40] focus:border-[#004D40] transition-colors placeholder-slate-400 bg-slate-50/50" />
                            </div>
                            <div>
                                <label for="track_letter_id" class="block text-sm font-semibold text-[#37474F] mb-1.5">Nomor Pengajuan (ID)</label>
                                <input type="text" id="track_letter_id" name="pengajuan_id" required autocomplete="off" placeholder="Contoh: 12"
                                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#004D40] focus:border-[#004D40] transition-colors placeholder-slate-400 bg-slate-50/50" />
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base font-semibold text-white bg-[#004D40] rounded-xl hover:bg-[#00382E] shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#004D40]">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                                Lacak Surat
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Panel: Tracking Laporan --}}
                <div x-show="activeTab === 'laporan'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" role="tabpanel">
                    <form method="POST" action="{{ route('public.complaints.track.post') }}" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="track_complaint_nik" class="block text-sm font-semibold text-[#37474F] mb-1.5">NIK Pelapor</label>
                                <input type="text" id="track_complaint_nik" name="nik" required autocomplete="off" placeholder="Masukkan 16 digit NIK" maxlength="16"
                                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#004D40] focus:border-[#004D40] transition-colors placeholder-slate-400 bg-slate-50/50" />
                            </div>
                            <div>
                                <label for="track_complaint_id" class="block text-sm font-semibold text-[#37474F] mb-1.5">Nomor Tiket (ID Laporan)</label>
                                <input type="number" id="track_complaint_id" name="aspirasi_id" required autocomplete="off" placeholder="Contoh: 15"
                                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#004D40] focus:border-[#004D40] transition-colors placeholder-slate-400 bg-slate-50/50" />
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base font-semibold text-white bg-[#004D40] rounded-xl hover:bg-[#00382E] shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#004D40]">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                                Lacak Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
