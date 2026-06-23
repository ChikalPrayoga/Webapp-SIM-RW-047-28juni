# PRODUCT_ROADMAP.md

## 1. Current Project Status
Proyek SIM RW 047 saat ini berada pada status **"Phase 2 Completed - Core System Freeze"**. 
Logika bisnis utama (core business logic), otorisasi hak akses berbasis peran (RBAC), data demografis warga, transaksi surat menyurat berjenjang, penugasan keluhan warga, serta log audit dan aktivitas operasional telah selesai diimplementasikan secara utuh di server backend Laravel dan MySQL. Seluruh fungsionalitas ini telah lolos verifikasi integrasi layout frontend yang mobile responsive.

---

## 2. Completed Modules
* **Modul Autentikasi & RBAC**: Autentikasi aman akun pengurus, penanganan token otentikasi API, dan pengecekan hak akses dinamis berbasis model policies.
* **Modul Kependudukan**: Pengelolaan data terpusat Kartu Keluarga dan Profil Warga, termasuk fitur pencarian NIK/KK terintegrasi.
* **Modul Validasi Data (Resident Change)**: Pengajuan usulan perubahan profile warga oleh warga publik dan workflow persetujuan berjenjang Ketua RT.
* **Modul Administrasi Surat**: Pengajuan mandiri surat pengantar oleh warga publik, visualisasi linimasa pelacakan berkas, serta approval bertahap RT dan RW.
* **Modul Laporan & Aspirasi (Core)**: Pengiriman keluhan warga publik beserta berkas bukti lampiran, penugasan pengurus terkait, dan pembaruan sejarah penanganan laporan.
* **Modul Log Audit & Aktivitas**: Perekaman aktivitas login/logout pengguna serta log audit komparasi detail perubahan atribut data database (`old_value` vs `new_value` JSON).
* **Modul Konfigurasi (System Settings)**: Modul manipulasi parameter dinamis aplikasi di database untuk kebutuhan admin.

---

## 3. Completed Features
* Halaman login dan pengalihan dashboard otomatis berbasis data jabatan aktif di organisasi.
* Pengisian formulir pendaftaran Kartu Keluarga dan Anggota Keluarga melalui dashboard pengurus.
* Pengisian form usulan perubahan data profil warga dan dashboard persetujuan untuk pengurus RT.
* Pengajuan surat pengantar oleh warga publik pada portal layanan warga dengan mengunggah KTP/KK.
* Visualisasi progress persetujuan surat pengantar (Submitted -> RT Review -> RW Review -> Approved/Completed).
* Registrasi pengaduan masalah lingkungan oleh warga publik dengan melampirkan berkas foto.
* Fitur penugasan laporan kepada pengurus tertentu dan perubahan status log penanganan keluhan.
* Tabel log audit perubahan data model utama (menyimpan capture payload lama dan baru secara otomatis).
* Halaman kelola user (pengurus), peran (role), dan konfigurasi pengaturan global di area admin.

---

## 4. Placeholder Modules
Beberapa modul yang telah dirancang di level UI/API namun pemrosesan data riil ke database MySQL masih dinonaktifkan (statik/mockup):
* **Modul Keuangan (Kas & Iuran)**: Halaman utama dashboard Bendahara RW menampilkan banner informasi bahwa modul kas dan iuran bulanan warga masih dalam tahap pengembangan backend dan akan dirilis pada fase pembaruan sistem berikutnya.
* **Modul Informasi RW**: Menu pengumuman warga dan agenda kegiatan lingkungan belum dibuatkan skema migrasi tabel fisik database-nya.
* **Tabel Log AI (AIProcessingLogs)**: Migrasi tabel observabilitas log AI dan model `AIProcessingLog` ditunda sebagai backlog integrasi.
* **Tabel Notifikasi**: Migrasi tabel `notifications` dan model terkait ditangguhkan.

---

## 5. Future Features
* Penerbitan dokumen PDF resmi surat pengantar warga secara otomatis saat status *COMPLETED*.
* Penyematan kode QR validation (QR Code verification) di surat PDF untuk validitas keabsahan dokumen.
* Sistem sinkronisasi data tagihan iuran wajib bulanan otomatis (Rp50.000 per KK) setiap awal bulan.
* Otomatisasi pengiriman pesan notifikasi penugasan pengurus dan update status surat ke Telegram warga.
* Modul analisis analitik tren kategori keluhan warga bulanan dan persentase kepuasan warga.

---

## 6. Future Modules
* **Modul Bantuan Sosial (Social Aid)**: Pengelolaan program bantuan sosial dan pendataan warga penerima bantuan secara adil berbasis status sosio-ekonomi.
* **Modul Metrik Kinerja Pelayanan RT**: Pengukuran KPI respon RT dalam memproses surat dan keluhan warga.
* **Modul Ekspor Laporan Terpadu**: Fitur ekspor data kependudukan, laporan keuangan kas, dan rekap surat ke format Excel/PDF.
* **Modul Konfigurasi Model AI**: Pengaturan model LLM Gemini dan suhu temperatur pemrosesan secara dinamis via UI admin.

---

## 7. Technical Debt Register
* **Database & Migrations**:
  - Absennya tabel fisik MySQL untuk Keuangan (`catatan_iuran_wargas`, `financial_transactions`) dan Informasi (`announcements`, `events`).
  - Absennya tabel log AI (`ai_processing_logs`) dan tabel notifikasi (`notifications`).
* **Backend Layer**:
  - Kelas `GeminiService` dan `N8nService` masih berupa stub/mockup kosong (belum melakukan HTTP client request eksternal sesungguhnya).
  - Queue Job `ProcessComplaintAI` telah dibuat tetapi penanganan callback n8n belum dikoneksikan ke route publik.
  - Listener `CreateInternalNotification` masih memuat tag `TODO: [Phase 3]`.
* **Frontend Layer**:
  - Grafik dan tabel di dashboard Bendahara masih menggunakan data statik/simulasi di controller.

---

## 8. Known Limitations
* **Ketergantungan Pembayaran Manual**: Sistem kas dan iuran masih mengandalkan pencatatan manual oleh Bendahara (belum terintegrasi payment gateway).
* **Fallback Status AI**: Apabila API Gemini/n8n mengalami kendala jaringan atau kegagalan respon, status kategori laporan keluhan akan otomatis diatur ke *UNCATEGORIZED* dan tingkat prioritas diatur ke *Medium*.
* **QR Code belum Terenkripsi**: QR Code pelacakan dokumen surat pengantar saat ini hanya berisi tautan URL verifikasi mentah (belum menggunakan tanda tangan kriptografis/digital signature terenkripsi).

---

## 9. Development Backlog
* [ ] Implementasi migrasi database tabel `catatan_iuran_wargas` dan `financial_transactions`.
* [ ] Implementasi controller keuangan dan logika bisnis setoran iuran Bendahara.
* [ ] Implementasi migrasi database tabel `announcements` dan `events`.
* [ ] Implementasi migrasi database tabel `ai_processing_logs` dan `notifications`.
* [ ] Konfigurasi webhook endpoint n8n untuk orkestrasi data keluhan warga.
* [ ] Koneksi Gemini API Client menggunakan API Key terenkripsi di environment.
* [ ] Pembuatan integrasi Telegram Bot Service untuk alur notifikasi warga.
* [ ] Implementasi library generator PDF (misal: Snappy/Dompdf) untuk pencetakan surat pengantar.
* [ ] Implementasi QR code generator pada surat PDF.

---

## 10. Development Priority
1. **Prioritas 1**: Integrasi Webhook n8n (Asynchronous Queue Job) untuk menghubungkan event laporan aduan warga.
2. **Prioritas 2**: Integrasi Gemini API untuk memproses klasifikasi teks pengaduan secara otomatis.
3. **Prioritas 3**: Pembuatan modul Telegram Bot untuk saluran notifikasi publik.
4. **Prioritas 4**: Implementasi database fisik dan modul Keuangan (Iuran & Kas) Bendahara RW.
5. **Prioritas 5**: Integrasi generator berkas PDF dan QR Code surat pengantar.
6. **Prioritas 6**: Pembuatan modul Informasi RW (Pengumuman & Agenda).

---

## 11. Roadmap Per Phase

### Phase 1: Core System Development (STATUS: COMPLETED)
* Inisialisasi struktur arsitektur Laravel (Service & Repository).
* Migrasi database inti kependudukan dan alur kerja surat/laporan.
* Implementasi otorisasi RBAC (Laravel Policies).
* Dashboard RT, RW, Sekretaris, dan Admin Super.

### Phase 2: Integration Hook Preparation (STATUS: COMPLETED)
* Pembuatan user interface manajemen user dan system settings untuk Admin.
* Pembuatan struktur Queue Jobs, Events, dan Listeners untuk hook AI.
* Pembuatan log audit data JSON (`audit_logs`) dan log aktivitas (`activity_logs`).
* Bekukan (*Freeze*) fungsionalitas sistem utama.

### Phase 3: n8n Workflow Integration (STATUS: PLANNED / NEXT)
* Hubungkan Queue ke webhook server n8n.
* Bangun alur kerja asinkronus pengaduan dan pengiriman callback ke Laravel database.

### Phase 3.5: Telegram Integration (STATUS: PLANNED)
* Konfigurasi Bot Telegram untuk pengaduan keluhan warga dan pengiriman update status surat.

### Phase 4: Gemini Integration (STATUS: PLANNED)
* Hubungkan model NLP Gemini untuk otomatisasi kategori keluhan, tingkat urgensi, prioritas, dan executive summary.

### Phase 5: AI Dashboard & Analytics (STATUS: PLANNED)
* Implementasi kueri analitik grafik laporan warga.
* Halaman log pemrosesan AI (`AIProcessingLogs`) untuk observabilitas admin.

### Phase 6: Testing, Optimization, & Launch (STATUS: PLANNED)
* Optimasi indeks kueri MySQL.
* Uji performa pemuatan halaman dan audit celah keamanan (Authorization).
* Rilis sistem secara resmi.

---

## 12. Milestone History
* **M1: Core Database & Model Setup** (*10 Juni 2026*)
  - Struktur tabel kependudukan (`kartu_keluargas`, `anggota_keluargas`) didefinisikan dan berhasil dimigrasi.
* **M2: Workflow Foundation Implementation** (*11 Juni 2026*)
  - Form pengajuan surat pengantar warga publik dan pelacakan status linimasa selesai dibuat.
* **M3: Admin Management & RBAC Finalization** (*21 Juni 2026*)
  - Modul manajemen user, role permission viewer, audit log, dan setting global untuk Super Admin selesai diuji. Sistem dibekukan (*Freeze*).

---

## 13. Version History
* **v1.0.0-draft**: Rancangan konseptual skema relasi basis data (3NF minimal).
* **v1.0.0**: Implementasi basis data demografis kependudukan dan fungsionalitas CRUD warga.
* **v2.0.0-freeze**: Penambahan audit trail lengkap, activity log, panel administrasi user/setting, dan pembekuan modul Phase 2.

---

## 14. Next Target
* **Target Terdekat**: Memulai pengembangan **Phase 3 (n8n Workflow Integration)** dengan fokus menghubungkan queue event `ProcessComplaintAI` Laravel ke webhook server n8n untuk memulai otomatisasi alur pemrosesan data keluhan warga tanpa memblokir request pengguna (*non-blocking*).
