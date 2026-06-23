# PRODUCT_BLUEPRINT.md

## 1. Product Vision
Sistem Informasi Manajemen (SIM) RW 047 dirancang sebagai platform administrasi digital terpadu tingkat rukun warga yang memadukan keandalan Core System berbasis Laravel dengan kecerdasan buatan (AI) eksternal secara asynchronous. Visi utama produk ini adalah menciptakan tata kelola administrasi lingkungan yang mandiri, transparan, aman, responsif, dan bebas dari ketergantungan personil individu melalui otomatisasi alur kerja (workflow engine).

---

## 2. Product Objectives
* **Sentralisasi Data Demografi**: Mengintegrasikan seluruh data warga ke dalam satu basis data terpusat (single source of truth) untuk mengeliminasi spreadsheet lokal yang tersebar.
* **Transparansi Layanan Publik**: Menyediakan tracking (pelacakan) surat pengantar dan laporan keluhan secara transparan bagi warga melalui portal digital tanpa login.
* **Efisiensi Alur Kerja Administrasi**: Mengurangi beban birokrasi pengurus (RT/RW/Sekretaris) melalui pemrosesan berjenjang digital.
* **Peningkatan Kualitas Informasi via AI**: Memanfaatkan kecerdasan buatan untuk mengklasifikasikan pengaduan secara otomatis serta merangkum isu lingkungan demi mempermudah pengambilan keputusan.
* **Akurasi Data Mutasi**: Menjamin validasi perubahan data profil kependudukan melalui pembuktian berkas fisik yang dikontrol oleh RT dan disinkronisasi otomatis oleh sistem.

---

## 3. Problem Statement
Sistem tata kelola RW 047 konvensional memiliki beberapa hambatan utama:
1. **Penyebaran Data**: Informasi kependudukan tercecer pada banyak berkas spreadsheet mandiri di tingkat RT, memicu redundansi dan ketidakakuratan data.
2. **Keterbatasan Pelacakan Berkas**: Warga tidak mengetahui status pengajuan surat pengantar atau perkembangan penanganan laporan keluhan mereka kecuali dengan bertanya langsung.
3. **Kerentanan Kehilangan Jejak Perubahan**: Riwayat perubahan profil warga, mutasi data, dan alur persetujuan surat tidak terdokumentasi secara runut.
4. **Beban Manual Pengolahan Laporan**: Laporan warga masuk secara acak tanpa kategori terstruktur, sehingga pengurus kesulitan memilah prioritas penanganan.
5. **Ketergantungan Individu**: Administrasi lingkungan menjadi lumpuh apabila personil pengurus tertentu sedang berhalangan hadir.

---

## 4. Product Scope

### In Scope
* Portal Layanan Warga publik (tanpa login) untuk pengajuan surat, pelaporan keluhan, dan pelacakan status.
* Dashboard internal pengurus (Ketua RT, Ketua RW, Sekretaris RW, Bendahara RW, Super Admin).
* Sistem otorisasi berbasis peran (Role-Based Access Control) yang ketat melalui Laravel Policies.
* Pengelolaan data demografis (Kartu Keluarga dan Anggota Keluarga/Warga) berskala RW.
* Alur pengajuan perubahan data kependudukan dengan verifikasi RT.
* Alur approval bertahap surat pengantar (Warga -> RT Review -> RW Review -> Selesai / Ditolak) oleh pengurus.
* Manajemen keluhan dengan penugasan petugas dan tracking status alur kerja (Workflow Status).
* Log aktivitas operasional sistem (Activity Logs) dan audit detail modifikasi data (Audit Logs).
* Integrasi kecerdasan buatan (AI) Gemini via n8n secara asinkron (non-blocking) sebagai layer peningkatan informasi (enhancement).

### Out of Scope
* Integrasi langsung dengan API Dukcapil Nasional.
* Sistem pembayaran iuran secara online (payment gateway integration).
* Tanda tangan digital tersertifikasi pihak ketiga (seperti BSrE/PrivyID).
* Aplikasi mobile native (Android/iOS) mandiri (sistem didesain sebagai web responsive).

---

## 5. Target Users
* **Warga RW 047**: Mengakses portal publik untuk memanfaatkan layanan administrasi mandiri.
* **Ketua RT**: Memverifikasi pengajuan surat dan usulan perubahan data warga di wilayah RT terkait.
* **Sekretaris RW**: Mengelola data kependudukan makro RW dan merekapitulasi dokumen administrasi.
* **Bendahara RW**: Mencatat mutasi kas masuk/keluar dan iuran warga RW 047.
* **Ketua RW**: Memantau kondisi wilayah, memberikan approval akhir surat pengantar, dan mengawasi laporan warga.
* **Super Admin**: Mengonfigurasi parameter sistem, mengelola user/role, dan memantau log audit keamanan.

---

## 6. Actor & Roles

| Actor / Role | Cakupan Akses & Tanggung Jawab Utama |
| --- | --- |
| **Warga (Publik)** | Mengajukan surat, membuat laporan aduan, melacak progress dokumen via NIK & ID. |
| **Ketua RT** | Meninjau warga di wilayah RT, menyetujui request mutasi data warga, memvalidasi draf surat. |
| **Sekretaris RW** | CRUD Kartu Keluarga, CRUD Anggota Keluarga, merekap persuratan, verifikasi berkas perubahan data. |
| **Bendahara RW** | Mencatat kas masuk (iuran wajib/sukarela) dan pengeluaran RW, menyusun laporan keuangan. |
| **Ketua RW** | Executive monitoring, menandatangani surat pengantar (final approval), memantau ringkasan laporan. |
| **Super Admin** | Manajemen user pengurus, manipulasi RBAC matrix, konfigurasi setting global, analisis audit trail. |

---

## 7. Final RBAC Overview
Aplikasi menerapkan **Role-Based Access Control (RBAC)** dan **Permission-Based Access Control** secara murni yang diikat melalui sistem otorisasi internal Laravel (Policies & Gates). Aturan RBAC final:
* **Tidak Ada Hardcoded Role**: Otorisasi dicek berdasarkan izin spesifik (*permission*) yang dipetakan pada tabel junction `role_permissions`, bukan mengecek string role di database.
* **Policy Binding**: Setiap model utama (`KartuKeluarga`, `AnggotaKeluarga`, `PengajuanSurat`, `LogLaporanAspirasi`) dilindungi oleh class Policy terkait yang memeriksa izin user (misal: `PermissionEnum::VIEW_RESIDENTS` atau `PermissionEnum::MANAGE_SYSTEM`).

---

## 8. Core Modules
1. **Modul Autentikasi & RBAC**: Autentikasi akun pengurus, reset password, dan manajemen hak akses dinamis.
2. **Modul Kependudukan**: Pengelolaan data Kartu Keluarga, Profil Anggota Keluarga, dan pencatatan mutasi warga.
3. **Modul Validasi Data (Resident Change)**: Validasi workflow usulan perubahan data profil warga yang diajukan ke Sekretaris.
4. **Modul Pengaduan & Laporan**: Penyampaian aspirasi/keluhan, penugasan petugas pelaksana, dan log perkembangan.
5. **Modul Persuratan**: Registrasi surat pengantar warga dan alur validasi bertahap RT/RW.
6. **Modul Log Audit & Aktivitas**: Pencatatan riwayat perubahan data (sebelum/sesudah) dan rekaman login/logout pengurus.
7. **Modul Konfigurasi (System Settings)**: Penyimpanan parameter operasional aplikasi (key-value).

---

## 9. Module Responsibilities
* **Core Laravel System**: Bertanggung jawab penuh atas keandalan otentikasi, otorisasi hak akses, logika bisnis, validasi input data, keamanan transaksi database, dan pencatatan audit log.
* **MySQL Database**: Bertindak sebagai satu-satunya *Source of Truth* yang menyimpan data permanen seluruh modul.
* **AI & Workflow Engine (n8n & Gemini)**: Bertanggung jawab memproses analisis semantik teks keluhan laporan warga secara terpisah (asynchronous) agar tidak membebani performa aplikasi Laravel utama.

---

## 10. Current Functional Scope (Phase 2B State)
Fitur yang telah aktif dan teruji di core system Laravel:
* Sistem login dan logout terproteksi dengan pengalihan dashboard berbasis jabatan organisasi.
* CRUD manajemen data Kartu Keluarga dan Anggota Keluarga untuk pengurus.
* Pengajuan permohonan perubahan data warga (usulan field lama vs field baru) lengkap dengan histori persetujuan.
* Registrasi laporan pengaduan warga publik disertai pengunggahan berkas lampiran bukti foto/dokumen.
* Penugasan keluhan warga kepada pengurus dan pembaruan riwayat status keluhan (`complaint_status_histories`).
* Pengajuan surat pengantar oleh warga publik disertai formulir tracking status linimasa vertikal.
* Alur pemrosesan surat pengantar berjenjang (RT Review -> RW Review -> Selesai / Ditolak) oleh pengurus.
* Sistem log audit detail (`old_value` vs `new_value` dalam format JSON) untuk memantau perubahan data.
* Pencatatan log aktivitas operasional pengguna sistem (`activity_logs`).
* Panel pengaturan parameter global aplikasi (`system_settings`) untuk kebutuhan administrator.

---

## 11. Planned Functional Scope
Fitur yang dirancang dan akan diintegrasikan pada pengembangan tahap berikutnya:
* **Modul Keuangan Kas RW**: Pencatatan pembayaran iuran bulanan (wajib/sukarela) per KK dan mutasi transaksi kas RW.
* **Modul Informasi RW**: Publikasi berita pengumuman warga dan agenda kegiatan lingkungan terstruktur.
* **External Workflow Automation (n8n)**: Integrasi antrean event jobs ke webhook workflow engine.
* **External AI Agent (Gemini)**: Klasifikasi otomatis kategori laporan keluhan, prioritas penanganan, ringkasan eksekutif, dan tren isu lingkungan.
* **Telegram Notification Bot**: Pengiriman bot alert kepada warga terkait status surat/laporan dan notifikasi penugasan pengurus.

---

## 12. High Level Business Workflow

### A. Alur Kerja Pengajuan & Proses Persuratan
```text
Warga (Publik)         Ketua RT (Review)        Ketua RW (Approve)     Sekretaris RW (Complete)
    │                         │                         │                         │
    ├──► Ajukan Surat ───────►│                         │                         │
    │    (Status: SUBMITTED)  ├──► Setujui/Forward ────►│                         │
    │                         │    (Status: RT_REVIEW)  ├──► Setujui/Approve ────►│
    │                         │                         │    (Status: RW_REVIEW)  ├──► Cetak & Terbitkan
    │                         │                         │                         │    (Status: COMPLETED)
```

### B. Alur Kerja Pengaduan Laporan (AI Assisted)
```text
Warga (Publik)           Laravel Core System         n8n Workflow Engine        Gemini AI Engine
    │                             │                           │                         │
    ├──► Kirim Laporan Keluhan ──►│                           │                         │
    │    (Status: SUBMITTED)      ├──► Simpan data & Queue ──►│                         │
    │                             │                           ├──► Analisis teks ──────►│
    │                             │                           │    (Kategori, Prioritas)◄─── Hasil analisis
    │                             │◄── Update Data Laporan ───┤                         │
    │                             │    (Status: CLASSIFIED)   │                         │
    │                             ├──► Tampilkan di Dashboard │                         │
```

---

## 13. High Level System Architecture
Aplikasi SIM RW 047 menerapkan pola arsitektur **Hybrid Event-Driven** dengan pembagian tanggung jawab sebagai berikut:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER (Blade Web UI)                      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP Requests
┌───────────────────────────────────▼────────────────────────────────────┐
│                    CORE SYSTEM LAYER (Laravel 10 PHP)                  │
├────────────────────────────────────────────────────────────────────────┤
│ - Auth & RBAC   - Audit Engine   - Integration Services                │
│ - Policies      - Queues & Jobs  - Event Listeners                     │
└───────────────────┬───────────────────────────────┬────────────────────┘
                    │ DB Query                      │ Dispatch Job
┌───────────────────▼──────────────┐      ┌─────────▼────────────────────┐
│   DATA LAYER (MySQL Database)    │      │ WORKFLOW ENGINE (n8n Engine) │
├──────────────────────────────────┤      ├──────────────────────────────┤
│ - Master & Transaction Data      │      │ - Orchestration & Flows      │
│ - Audit & System Logs            │      └─────────┬────────────────────┘
└──────────────────────────────────┘                │ Call API
                                          ┌─────────▼────────────────────┐
                                          │     AI ENGINE (Gemini API)   │
                                          ├──────────────────────────────┤
                                          │ - NLP Text Classification    │
                                          └──────────────────────────────┘
```

* **Laravel Service Layer**: Seluruh controllers memanggil kelas layanan khusus (*Service Layer*) untuk memisahkan logika bisnis dari HTTP request.
* **Repository Pattern**: Digunakan untuk kueri pelaporan analitik yang kompleks guna menjaga performa kueri database.
* **Queue System**: Integrasi dengan API eksternal (n8n, Gemini, Telegram) wajib berjalan secara asinkron menggunakan queue worker untuk mencegah delay respon bagi pengguna.

---

## 14. Non Functional Requirements
* **Security & Data Confidentiality**: Seluruh sandi di-hash menggunakan algoritma Bcrypt. Kebijakan otorisasi dievaluasi di level middleware dan policy sebelum mengeksekusi logika database. Parameter masukan form wajib melalui tahap sanitasi input.
* **Graceful Degradation (AI Resilience)**: Kegagalan atau hilangnya konektivitas dengan Gemini API/n8n tidak boleh memicu kegagalan transaksi penyimpanan laporan warga di database Laravel. Aplikasi harus melakukan penanganan eksepsi secara mandiri (*error recovery*) dan menetapkan status *default fallback* (Kategori: UNCATEGORIZED, Urgency: Medium).
* **Observability**: Setiap interaksi integrasi dengan kecerdasan buatan wajib tercatat ke dalam log audit (`AIProcessingLogs` / `activity_logs`) untuk memudahkan penelusuran durasi eksekusi, payload data, status respon, dan pesan error jika terjadi kegagalan sistem.
* **Performance**: Kecepatan pemuatan halaman dashboard pengurus ditargetkan kurang dari 3 detik pada kondisi jaringan internet standar, didukung oleh optimalisasi indeks kueri MySQL.

---

## 15. Product Success Criteria
* **Pusat Integrasi Demografi (100%)**: Seluruh administrasi warga berbasis nomor Kartu Keluarga dan NIK dikontrol secara terpusat tanpa ada duplikasi data.
* **Transparansi Berkas (100%)**: Warga dapat memantau status persis pengajuan dokumen mereka kapan saja melalui portal publik.
* **Efisiensi Validasi Data**: Waktu respon verifikasi berkas pengajuan surat dan usulan mutasi profil warga turun secara signifikan dibandingkan proses manual.
* **Akurasi Analisis AI (≥ 90%)**: Hasil otomatisasi klasifikasi kategori pengaduan oleh kecerdasan buatan mencapai tingkat kecocokan minimum 90% dengan evaluasi manual oleh pengurus.
* **Fleksibilitas Tampilan**: Aplikasi lulus pengujian responsifitas pada perangkat ponsel pintar (mobile-friendly) dengan skor rendering layout yang baik.
