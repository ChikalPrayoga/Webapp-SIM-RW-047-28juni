# FINANCIAL_FINAL_BASELINE.md
# Dokumen Baseline Akhir Modul Keuangan - SIM RW 047

Dokumen ini merupakan **Source of Truth (SoT) permanen** yang mengunci seluruh keputusan desain, arsitektur database, mekanisme otorisasi, service layer, dan strategi pengembangan Modul Keuangan SIM RW 047 sebelum implementasi dimulai.

---

## 📋 1. Executive Summary

Modul Keuangan dalam Sistem Informasi Manajemen RW 047 dirancang untuk memfasilitasi pembukuan kas yang transparan, terintegrasi, dan aman di tingkat RW dan RT. Berdasarkan hasil tinjauan arsitektur (*Architecture Review*), seluruh blueprint modul ini telah disempurnakan untuk menjamin skalabilitas jangka panjang, keamanan data sensitif warga, dan integritas audit yang ketat. 

Dengan disahkannya dokumen baseline ini, seluruh keputusan arsitektural dinyatakan **FINAL & LOCKED (FREEZE)**. Modul ini siap dilanjutkan ke tahap pengodean (*coding*) pada Phase 4A.1 (Sprint 1) dengan target tanpa menghasilkan utang teknis (*technical debt*).

---

## 🛠️ 2. Final Design Decisions

Evaluasi mendalam terhadap 10 poin arsitektural menghasilkan keputusan final berikut:

### A. Universal Ledger (Pilihan: Opsi B - Polymorphic Universal Ledger)
* **Keputusan:** Menggunakan **Opsi B (Polymorphic Relations)** pada tabel `financial_transactions` (`reference_type` dan `reference_id`) dan menghapus kolom `financial_transaction_id` pada tabel `catatan_iuran_wargas`.
* **Justifikasi Teknis:**
  * **Skalabilitas:** Memungkinkan tabel `financial_transactions` bertindak sebagai jurnal umum murni yang dapat menampung transaksi dari modul apa pun di masa mendatang (seperti Donasi Warga atau Bantuan Sosial) secara dinamis tanpa mengubah skema tabel.
  * **Maintainability:** Relasi polimorfik didukung penuh oleh Laravel Eloquent (`morphTo` / `morphMany`), membuat kode query tetap bersih dan standar.
  * **Normalisasi:** Menghindari redundant linking (seperti Opsi C) dan nullable foreign keys yang membingungkan (seperti Opsi A).

### B. Financial Integrity (Mekanisme Transaksi Penyesuaian / Adjustment)
* **Keputusan:** Enforce mutlak prinsip **Immutability (Kekekalan Transaksi)**. Transaksi yang sudah diposting tidak boleh diubah atau dihapus secara fisik (`no physical delete or update`).
* **Mekanisme Koreksi:**
  1. Jika terjadi kesalahan input data (misal: nominal atau kategori salah), Bendahara memposting **Transaksi Penyesuaian (Reversal)** baru.
  2. Karena database menerapkan batasan nominal wajib positif (`amount > 0`), koreksi dilakukan dengan memposting transaksi bernilai positif dengan tipe berlawanan (misal: memposting tipe `EXPENSE` berkategori `ADJUSTMENT` untuk mengoreksi kesalahan tipe `INCOME`).
  3. Transaksi penyesuaian wajib mereferensikan transaksi awal melalui kolom `adjusted_transaction_id`.
  4. Transaksi awal akan mencatat metadata `adjusted_by_user_id` dan `adjusted_at` sebagai penanda audit bahwa transaksi tersebut telah dikoreksi.

### C. Storage Security (Keamanan Bukti Transfer Warga)
* **Keputusan:** Menggunakan direktori privat **`storage/app/private/receipts`** untuk menyimpan berkas bukti transfer warga. Direktori publik (`public/storage`) dilarang keras untuk data sensitif ini.
* **Justifikasi:** Bukti transfer berisi data pribadi perbankan warga yang wajib dilindungi. File disajikan via `FinancialReceiptController` khusus yang dilindungi middleware otorisasi. Akses hanya diberikan kepada admin (sesuai scope wilayah RT/RW) dan warga pemilik data (berdasarkan pencocokan NIK/KK).

### D. Audit Trail Metadata
* **Keputusan:** Menambahkan metadata audit secara eksplisit pada blueprint database:
  * **Tabel `catatan_iuran_wargas`:** Kolom `approved_by_user_id` dan `approved_at` ditambahkan untuk merekam siapa dan kapan verifikasi pembayaran iuran mandiri warga disetujui.
  * **Tabel `financial_transactions`:** Kolom `adjusted_transaction_id`, `adjusted_by_user_id`, dan `adjusted_at` ditambahkan untuk mencatat histori koreksi transaksi secara transparan.

### E. Transaction Numbering (Penomoran Unik Transaksi)
* **Keputusan:** Setiap transaksi kas wajib memiliki nomor unik dengan format **`TRX-YYYYMMDD-XXXX`** (di mana `XXXX` adalah sequence harian 4 digit).
* **Justifikasi:**
  * **Keamanan:** Menyembunyikan Auto-Increment ID database agar tidak mudah ditebak atau dieksploitasi.
  * **Auditabilitas:** Memudahkan pelacakan transaksi pada slip fisik, laporan PDF/Excel, notifikasi Telegram, dan komunikasi dengan warga.
  * **Akademik:** Memberikan nilai tambah signifikan pada kualitas perancangan sistem di Bab III Skripsi.

### F. Event Driven Architecture (EDA)
* **Keputusan:** Modul Keuangan mengadopsi Event Driven Architecture (EDA) secara konsisten dengan Complaint Module dan Letter Module untuk mengelola alur kerja non-transaksional sekunder.
* **Arsitektur Event & Listener:**
  1. **Event `PaymentSubmitted`** (Warga mengajukan pembayaran via portal):
     * *Listener:* `NotifyBendaharaNewPayment` (Mengirimkan notifikasi Telegram ke Bendahara RW).
  2. **Event `PaymentApproved`** (Bendahara menyetujui pembayaran):
     * *Listener:* `LogActivityApprovedPayment` (Mencatat log aktivitas sistem).
     * *Listener:* `NotifyCitizenPaymentApproved` (Mengirim notifikasi Telegram/portal ke warga).
  3. **Event `PaymentRejected`** (Bendahara menolak pembayaran):
     * *Listener:* `LogActivityRejectedPayment` (Mencatat log aktivitas penolakan).
     * *Listener:* `NotifyCitizenPaymentRejected` (Mengirim notifikasi Telegram berisi alasan penolakan ke warga).
  4. **Event `FinancialTransactionCreated`** (Mutasi kas masuk/keluar dicatat):
     * *Listener:* `LogActivityCreatedTransaction` (Mencatat log aktivitas kas masuk/keluar).
     * *Listener:* `NotifyRTNewTransaction` (Mengirim notifikasi Telegram ke Ketua RT jika transaksi terkait RT-nya).
     * *Listener:* `UpdateLedgerBalancesCache` (Mengosongkan cache total saldo RT/RW secara asinkron agar loading dasbor tetap instan).
  5. **Event `FinancialAdjustmentCreated`** (Transaksi koreksi/reversal diposting):
     * *Listener:* `LogActivityCreatedAdjustment` (Mencatat log koreksi).
     * *Listener:* `LogAuditTrailAdjustment` (Menulis log audit detail di file log pengaman).
* **Catatan Penting Keuangan:** Penulisan ledger murni (pencatatan baris di `financial_transactions`) saat pembayaran disetujui tetap dilakukan secara **sinkron (synchronous)** di dalam satu `DB::transaction()` melalui `PaymentService` untuk menjaga konsistensi ACID, bukan secara asinkron di dalam listener. Listener hanya menangani dampak operasional sekunder (log, notifikasi, cache).

### G. Service Layer
* **Keputusan:** Memperkenalkan Service Layer sejak awal pengembangan (dimulai dari Sprint 2).
* **Komponen:**
  * `LedgerService`: Mengatur pembuatan nomor transaksi, kalkulasi saldo dinamis, posting jurnal, dan pemrosesan reversal.
  * `PaymentService`: Mengatur siklus hidup pembayaran iuran (pengajuan warga, verifikasi pengurus, penolakan).
* **Manfaat:** Memisahkan logika bisnis dari Controller, mempermudah pembuatan Unit Test, dan mempermudah dokumentasi Bab IV Skripsi.

### H. Database Transaction Strategy
* **Keputusan:** Wajib menggunakan `DB::transaction()` pada proses berikut untuk mencegah *partial updates* dan inkonsistensi:
  1. **Persetujuan Pembayaran Iuran:** Mengubah status pembayaran menjadi `APPROVED` sekaligus memposting kas masuk (`INCOME`) secara atomik.
  2. **Pencatatan Transaksi Koreksi (Reversal):** Mengisi metadata koreksi pada transaksi awal sekaligus membuat catatan transaksi pembanding baru.
  3. **Pencatatan Iuran Manual:** Menyimpan record iuran warga sekaligus memposting kas masuk terkait.

### I. Performance & Indexing Strategy
* **Keputusan:** Menerapkan strategi indexing yang efisien dan terarah pada tabel-tabel keuangan:
  * **`financial_transactions`:**
    * `UNIQUE INDEX` pada kolom `transaction_number`.
    * `INDEX` compound pada `(rt_code, transaction_type, transaction_date)` untuk mengoptimalkan agregasi query saldo dasbor (`SUM`).
    * `INDEX` polimorfik pada `(reference_type, reference_id)`.
  * **`catatan_iuran_wargas`:**
    * `UNIQUE INDEX` composite pada `(no_kk, iuran_type_id, periode_bulan, periode_tahun)`.
    * `INDEX` pada `(status, no_kk)`.

### J. Enum Strategy (Backed Enums PHP 8.1+)
* **Keputusan:** Menerapkan PHP Backed Enums untuk seluruh status dan tipe konstan di level Model untuk menjamin keamanan tipe (*type safety*) dan konsistensi dengan Core System:
  1. **`App\Enums\TransactionType` (string):**
     * `INCOME = 'INCOME'`
     * `EXPENSE = 'EXPENSE'`
  2. **`App\Enums\PaymentStatus` (string):**
     * `PENDING = 'PENDING'`
     * `APPROVED = 'APPROVED'`
     * `REJECTED = 'REJECTED'`
  3. **`App\Enums\ContributionType` (string):**
     * `WAJIB = 'WAJIB'`
     * `SUKARELA = 'SUKARELA'`
  4. **`App\Enums\TransactionCategory` (string):**
     * `IURAN = 'IURAN'`
     * `DONASI = 'DONASI'`
     * `OPERASIONAL = 'OPERASIONAL'`
     * `ADJUSTMENT = 'ADJUSTMENT'`
     * `LAINNYA = 'LAINNYA'`

### K. Activity Log Strategy
* **Keputusan:** Pencatatan log aktivitas pengurus dan warga akan mengikuti pola log terpusat yang sudah ada di Complaint & Letter Module:
  1. **`CREATE_PAYMENT`:** Warga mengajukan bukti transfer.
     * *Format Log:* `Warga KK :no_kk mengajukan konfirmasi pembayaran iuran sebesar Rp :nominal untuk periode :periode.`
  2. **`APPROVE_PAYMENT`:** Bendahara menyetujui transfer.
     * *Format Log:* `Bendahara :operator menyetujui pembayaran iuran KK :no_kk sebesar Rp :nominal untuk periode :periode.`
  3. **`REJECT_PAYMENT`:** Bendahara menolak transfer.
     * *Format Log:* `Bendahara :operator menolak pembayaran iuran KK :no_kk sebesar Rp :nominal. Alasan: :notes`
  4. **`CREATE_INCOME`:** Manual entry pemasukan kas.
     * *Format Log:* `Pengurus :operator mencatat kas masuk :transaction_number sebesar Rp :amount dengan kategori :category. Keterangan: :description`
  5. **`CREATE_EXPENSE`:** Manual entry pengeluaran kas.
     * *Format Log:* `Pengurus :operator mencatat kas keluar :transaction_number sebesar Rp :amount dengan kategori :category. Keterangan: :description`
  6. **`CREATE_ADJUSTMENT`:** Koreksi mutasi kas.
     * *Format Log:* `Pengurus :operator memposting koreksi transaksi :original_number dengan nomor transaksi penyesuaian :transaction_number sebesar Rp :amount.`

---

## 📈 3. Accepted Recommendations

Rekomendasi dari *Architecture Review* yang **diterima sepenuhnya**:

1. **HIGH: Implementasi Universal Ledger**
   * **Tindakan:** Mengubah relasi tabel `financial_transactions` menjadi polimorfik dan menghapus foreign key transaksi di `catatan_iuran_wargas`.
2. **MEDIUM: File Security Storage**
   * **Tindakan:** Menempatkan berkas bukti transfer di private storage dan menyajikannya lewat controller terotentikasi.
3. **LOW: Compound Database Indexing**
   * **Tindakan:** Menambahkan compound index untuk kalkulasi saldo agar query dasbor admin berjalan instan (< 100ms) saat data membesar.

---

## ❌ 4. Rejected Recommendations

* **Tidak ada rekomendasi yang ditolak.** Semua saran perbaikan dinilai konstruktif dan meningkatkan kualitas arsitektur sistem.

---

## 🔄 5. Modified Recommendations

1. **MEDIUM: Adjustment Transaction Logic**
   * **Modifikasi:** Dari usulan awal menggunakan nominal negatif (minus) untuk penyesuaian, dimodifikasi menjadi **penggunaan tipe transaksi berlawanan bernilai positif** (misalnya, membuat `EXPENSE` berkategori `ADJUSTMENT` untuk mengoreksi `INCOME` salah). Ini dilakukan untuk mematuhi *check constraint* `amount > 0` di level database dan mempertahankan formula kalkulasi saldo yang bersih: `SUM(INCOME) - SUM(EXPENSE)`.

---

## 🗄️ 6. Final Database Architecture

Berikut skema tabel final yang telah disesuaikan:

### A. Tabel `iuran_types`
| Nama Kolom | Tipe Data | Nullable | Default | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | AutoIncrement | Unique Identifier |
| `name` | VarChar(100) | No | - | Nama iuran (misal: "Iuran Keamanan") |
| `description` | Text | Yes | Null | Deskripsi iuran |
| `default_nominal` | Decimal(15,2) | No | 0.00 | Tarif default iuran |
| `type` | Enum | No | 'WAJIB' | Nilai: `WAJIB`, `SUKARELA` |
| `is_active` | Boolean | No | True | Status keaktifan |

### B. Tabel `catatan_iuran_wargas`
| Nama Kolom | Tipe Data | Nullable | Default | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `iuran_id` | BigInt (PK) | No | AutoIncrement | Unique Identifier |
| `no_kk` | VarChar(16) (FK) | No | - | Referensi ke `kartu_keluargas.no_kk` |
| `iuran_type_id` | BigInt (FK) | No | - | Referensi ke `iuran_types.id` |
| `nominal` | Decimal(15,2) | No | - | Nominal yang dibayar |
| `periode_bulan` | TinyInt | No | - | Bulan (1-12) |
| `periode_tahun` | Int | No | - | Tahun (misal: 2026) |
| `tanggal_pembayaran`| Date | Yes | Null | Tanggal disetujui |
| `recorded_by_user_id`| BigInt (FK) | Yes | Null | Petugas penginput manual |
| `approved_by_user_id`| BigInt (FK) | Yes | Null | Bendahara pengapprove |
| `approved_at` | Timestamp | Yes | Null | Waktu approval |
| `status` | Enum | No | 'PENDING' | Nilai: `PENDING`, `APPROVED`, `REJECTED` |
| `payment_proof_path`| VarChar(255) | Yes | Null | Bukti transfer (privat) |
| `rejection_notes` | Text | Yes | Null | Alasan penolakan |

### C. Tabel `financial_transactions`
| Nama Kolom | Tipe Data | Nullable | Default | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `transaction_id` | BigInt (PK) | No | AutoIncrement | Unique Identifier |
| `transaction_number`| VarChar(30) | No | - | Nomor Transaksi Unik |
| `rt_code` | VarChar(10) | Yes | Null | Kode RT (Null = Kas RW) |
| `transaction_type` | Enum | No | - | Nilai: `INCOME`, `EXPENSE` |
| `category` | VarChar(50) | No | - | Kategori transaksi |
| `amount` | Decimal(15,2) | No | - | Nominal transaksi (> 0) |
| `description` | Text | No | - | Rincian transaksi |
| `transaction_date` | Date | No | - | Tanggal efektif |
| `reference_type` | VarChar(255) | Yes | Null | Polymorphic Class |
| `reference_id` | BigInt | Yes | Null | Polymorphic ID |
| `adjusted_transaction_id`| BigInt (FK) | Yes | Null | ID Transaksi Asal (Self-referential) |
| `adjusted_by_user_id`| BigInt (FK) | Yes | Null | User yang mengkoreksi |
| `adjusted_at` | Timestamp | Yes | Null | Tanggal koreksi |
| `created_by_user_id`| BigInt (FK) | No | - | Operator pembuat |

---

## 🔐 7. Final Authorization Strategy

Otorisasi Modul Keuangan berjalan di atas baseline **RBAC Phase 3** yang kokoh tanpa membuat permission baru, dikombinasikan dengan logika **Data Scoping (ABAC)** pada Policy:

1. **Permission Mapping:**
   * `view_finances`: Diberikan kepada `BENDAHARA_RW`, `KETUA_RT`, dan `KETUA_RW`.
   * `manage_finances`: Diberikan kepada `BENDAHARA_RW` dan `KETUA_RT`.
2. **Policy Scoping (`FinancePolicy` & `IuranPolicy`):**
   * **`BENDAHARA_RW`**: Dapat mengelola semua jenis iuran, verifikasi semua antrean iuran, dan mengelola kas RW (`rt_code = null`). Memiliki akses *read-only* ke seluruh kas RT.
   * **`KETUA_RT`**: Hanya dapat mengelola kas RT miliknya sendiri (`financial_transactions.rt_code == auth_user->rt_code`) dan menginput iuran manual warga di RT-nya. Dilarang mengakses data keuangan RT lain atau kas induk RW secara *write*.
   * **`KETUA_RW`**: Akses *read-only* global untuk seluruh kas RW dan RT.
   * **`WARGA`**: Akses portal publik tanpa login untuk melihat transparansi kas dan iuran miliknya (melalui validasi NIK/KK).

---

## ⚙️ 8. Final Service Architecture

Penerapan Service Layer untuk memisahkan logika bisnis dari Controller:

```text
├── App
│   ├── Services
│   │   ├── LedgerService.php
│   │   │   ├── createTransaction(array $data): FinancialTransaction
│   │   │   ├── createReversal(int $transactionId, string $reason, int $userId): FinancialTransaction
│   │   │   ├── generateTransactionNumber(): string
│   │   │   └── getBalance(string $rtCode = null): float
│   │   └── PaymentService.php
│   │       ├── submitPayment(array $data, UploadedFile $proof): CatatanIuranWarga
│   │       ├── approvePayment(int $paymentId, int $userId): bool (triggers LedgerService)
│   │       └── rejectPayment(int $paymentId, string $notes, int $userId): bool
```

---

## 📅 9. Final Development Strategy

Pengembangan dibagi ke dalam 6 Sprint berurutan:

1. **Sprint 1: Master Data Jenis Iuran**
   * *Deliverable:* Tabel `iuran_types`, CRUD UI admin, validasi form, unit testing.
2. **Sprint 2: Buku Kas Utama (General Ledger)**
   * *Deliverable:* Tabel `financial_transactions`, `LedgerService`, `FinancePolicy`, transaksi penyesuaian (reversal), UI Mutasi Kas.
3. **Sprint 3: Pencatatan Pembayaran Iuran (Jalur Manual)**
   * *Deliverable:* Tabel `catatan_iuran_wargas`, `PaymentService`, integrasi atomik pembayaran-kas via DB transaction, UI input iuran manual.
4. **Sprint 4: Portal Warga & Antrean Verifikasi**
   * *Deliverable:* Form upload warga (private storage), antrean persetujuan bendahara, download bukti transfer privat.
5. **Sprint 5: Dashboard Analytics & Pelaporan Keuangan**
   * *Deliverable:* Chart.js integrasi kas masuk/keluar, widget statistik saldo dinamis, visualisasi portal transparansi warga.
6. **Sprint 6: Ekspor Dokumen & Uji Coba Integrasi (Final)**
   * *Deliverable:* Ekspor PDF (DomPDF), Ekspor Excel, pengujian akhir End-to-End.

---

## ⚠️ 10. Final Risk Assessment

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Bypass Bukti Transfer (IDOR)** | Warga mengunduh bukti transfer keluarga lain dengan menebak URL. | Menyimpan di private storage dan menerapkan otorisasi ketat di `FinancialReceiptController` berdasarkan kepemilikan NIK/KK. |
| **Inkonsistensi Saldo** | Pembayaran iuran disetujui tetapi data kas masuk gagal ditulis. | Membungkus proses approval iuran warga dan penulisan transaksi kas dalam satu blok `DB::transaction()`. |
| **Performa Agregasi Lambat** | Query `SUM(amount)` melambat saat data transaksi menyentuh puluhan ribu. | Menerapkan compound index pada kombinasi kolom `(rt_code, transaction_type, transaction_date)`. |
| **Manipulasi Nominal Transaksi** | Pengguna menginput nominal negatif atau nol. | Memasang Check Constraint `amount > 0` di level database dan validasi bertingkat di Form Request & Service Layer. |

---

## 🏁 11. Financial Module Final Baseline

Dengan ditandatanganinya baseline ini, seluruh spesifikasi dinyatakan **VALID, SELESAI, DAN DIKUNCI**. 

Tidak akan ada perubahan struktural pada skema database atau alur otorisasi selama fase implementasi, kecuali jika ditemukan bug desain yang sangat kritis. Pengembangan Modul Keuangan SIM RW 047 siap dilanjutkan ke **Phase 4A.1 (Sprint 1: Master Data Jenis Iuran)**.

---
*SIM RW 047 - Financial Module Blueprint Finalization Complete.*
