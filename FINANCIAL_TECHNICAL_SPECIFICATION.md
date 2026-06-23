# FINANCIAL_TECHNICAL_SPECIFICATION.md
# Spesifikasi Teknis Implementasi Modul Keuangan - SIM RW 047

Dokumen ini mendefinisikan spesifikasi teknis implementasi Modul Keuangan pada aplikasi Sistem Informasi Manajemen RW 047 berbasis Laravel 10. Dokumen ini bertindak sebagai panduan teknis operasional dan jembatan antara spesifikasi blueprint dengan penulisan kode sumber.

---

## 📋 1. Executive Summary

Blueprint Modul Keuangan diterjemahkan ke dalam arsitektur Laravel 10 menggunakan pendekatan **Clean Architecture** ringan dengan pemisahan tanggung jawab yang ketat. 
* **Controller** diimplementasikan sebagai orchestration layer tipis (*thin controller*) yang mendelegasikan validasi ke **Form Request**, logika bisnis ke **Service Layer**, dan otorisasi ke **Policy**.
* **Database Integrity** diamankan melalui constraint database ketat, transaksi database atomik (`DB::transaction`), serta penguncian baris (*row locking*) untuk mencegah race condition.
* **Security First** diterapkan pada bukti transfer yang disimpan di private storage, pengunduhan file via file streaming terkontrol, perlindungan IDOR di level Policy, dan pencegahan Mass Assignment.

---

## 📂 2. Final Folder Structure

Struktur folder baru yang akan dibuat selama Sprint 1-6 adalah sebagai berikut:

```text
app/
├── Enums/
│   ├── TransactionType.php
│   ├── PaymentStatus.php
│   ├── ContributionType.php
│   └── TransactionCategory.php
├── Events/
│   ├── PaymentSubmitted.php
│   ├── PaymentApproved.php
│   ├── PaymentRejected.php
│   ├── FinancialTransactionCreated.php
│   └── FinancialAdjustmentCreated.php
├── Http/
│   ├── Controllers/
│   │   ├── Finance/
│   │   │   ├── FinanceDashboardController.php
│   │   │   ├── FinancialTransactionController.php
│   │   │   ├── IuranTypeController.php
│   │   │   ├── PaymentVerificationController.php
│   │   │   └── FinancialReceiptController.php
│   │   └── Portal/
│   │       └── PortalFinanceController.php
│   └── Requests/
│       ├── StoreTransactionRequest.php
│       ├── ApprovePaymentRequest.php
│       ├── RejectPaymentRequest.php
│       ├── StoreIuranTypeRequest.php
│       ├── StoreManualPaymentRequest.php
│       └── SubmitPaymentRequest.php
├── Listeners/
│   ├── NotifyBendaharaNewPayment.php
│   ├── LogActivityApprovedPayment.php
│   ├── NotifyCitizenPaymentApproved.php
│   ├── LogActivityRejectedPayment.php
│   ├── NotifyCitizenPaymentRejected.php
│   ├── LogActivityCreatedTransaction.php
│   ├── NotifyRTNewTransaction.php
│   ├── UpdateLedgerBalancesCache.php
│   ├── LogActivityCreatedAdjustment.php
│   └── LogAuditTrailAdjustment.php
├── Models/
│   ├── IuranType.php
│   ├── CatatanIuranWarga.php
│   └── FinancialTransaction.php
├── Policies/
│   ├── IuranTypePolicy.php
│   ├── CatatanIuranWargaPolicy.php
│   └── FinancialTransactionPolicy.php
└── Services/
    ├── LedgerService.php
    └── PaymentService.php

database/
├── migrations/
│   ├── 2026_06_22_000001_create_iuran_types_table.php
│   ├── 2026_06_22_000002_create_financial_transactions_table.php
│   └── 2026_06_22_000003_create_catatan_iuran_wargas_table.php
├── seeders/
│   └── FinanceSeeder.php
└── factories/
    ├── IuranTypeFactory.php
    ├── CatatanIuranWargaFactory.php
    └── FinancialTransactionFactory.php

resources/
└── views/
    ├── admin/
    │   └── finance/
    │       ├── dashboard.blade.php
    │       ├── iuran-types/
    │       ├── transactions/
    │       └── verification/
    └── portal/
        └── finance/
            ├── transparency.blade.php
            └── invoice.blade.php
```

### Rationale Folder:
1. **`app/Enums`**: Menyimpan PHP Backed Enums untuk mencegah "magic strings" di database.
2. **`app/Services`**: Mengisolasi logika transaksi kas dan persetujuan pembayaran agar terpisah dari controller dan mempermudah unit testing.
3. **`app/Policies`**: Menangani logika otorisasi (ABAC scoping berdasarkan `rt_code` dan KK) untuk menjaga kontrol akses yang seragam.
4. **`app/Http/Requests`**: Memvalidasi data input dan tipe data masukan sebelum masuk ke logika bisnis.
5. **`app/Events` & `app/Listeners`**: Mengelola pemrosesan asinkron untuk notifikasi Telegram, cache clearing, dan audit logging.

---

## 🗄️ 3. Migration Strategy

Migrasi database harus dieksekusi secara berurutan sesuai dependensi kunci asing (*foreign key*):

1. **`create_iuran_types_table`**:
   * *Alasan:* Berdiri sendiri (master data jenis iuran).
2. **`create_financial_transactions_table`**:
   * *Alasan:* Menjadi tabel penampung utama (ledger) polimorfik. Bergantung pada tabel `users` (untuk `created_by_user_id` & `adjusted_by_user_id`). Memiliki *self-referential foreign key* (`adjusted_transaction_id`).
3. **`create_catatan_iuran_wargas_table`**:
   * *Alasan:* Tabel iuran warga bergantung pada `kartu_keluargas` (no_kk), `iuran_types` (iuran_type_id), dan `users` (recorded_by_user_id / approved_by_user_id).
4. **Indeks & Constraint Tambahan (Ditulis langsung di masing-masing skrip migrasi):**
   * *Compound Index* di `financial_transactions`: `(rt_code, transaction_type, transaction_date)`.
   * *Unique Index* di `financial_transactions`: `transaction_number`.
   * *Composite Unique Index* di `catatan_iuran_wargas`: `(no_kk, iuran_type_id, periode_bulan, periode_tahun)`.
   * *Check Constraint* di `financial_transactions`: `amount > 0`.

### Rollback Safety:
Semua metode `down()` di dalam skrip migrasi wajib menghapus foreign key terlebih dahulu sebelum melakukan drop table untuk menghindari kegagalan relasi:
```php
Schema::table('catatan_iuran_wargas', function (Blueprint $table) {
    $table->dropForeign(['no_kk']);
    $table->dropForeign(['iuran_type_id']);
    // dst
});
Schema::dropIfExists('catatan_iuran_wargas');
```

---

## 📐 4. Model Design

### A. Model `IuranType`
* **`$fillable`**: `['name', 'description', 'default_nominal', 'type', 'is_active']`
* **`$casts`**: `['default_nominal' => 'decimal:2', 'type' => ContributionType::class, 'is_active' => 'boolean']`
* **Relationships**:
  * `payments()`: `HasMany` ke `CatatanIuranWarga`.
* **Scopes**:
  * `scopeActive($query)`: Memfilter hanya jenis iuran yang aktif.

### B. Model `FinancialTransaction`
* **`$fillable`**: `['transaction_number', 'rt_code', 'transaction_type', 'category', 'amount', 'description', 'transaction_date', 'reference_type', 'reference_id', 'adjusted_transaction_id', 'adjusted_by_user_id', 'adjusted_at', 'created_by_user_id']`
* **`$casts`**: `['amount' => 'decimal:2', 'transaction_type' => TransactionType::class, 'category' => TransactionCategory::class, 'transaction_date' => 'date', 'adjusted_at' => 'datetime']`
* **Relationships**:
  * `reference()`: Polymorphic `MorphTo` (`reference_type`, `reference_id`).
  * `creator()`: `BelongsTo` ke `User` (`created_by_user_id`).
  * `adjuster()`: `BelongsTo` ke `User` (`adjusted_by_user_id`).
  * `originalTransaction()`: `BelongsTo` ke `FinancialTransaction` (`adjusted_transaction_id`).
  * `reversalTransaction()`: `HasOne` ke `FinancialTransaction` (`adjusted_transaction_id` di record sebaliknya).
* **Scopes**:
  * `scopeRw($query)`: Memfilter transaksi kas RW (`rt_code IS NULL`).
  * `scopeRt($query, $rtCode)`: Memfilter transaksi kas RT (`rt_code == $rtCode`).
  * `scopeActive($query)`: Memfilter transaksi yang belum pernah disesuaikan (`adjusted_transaction_id IS NULL`).
* **Accessors**:
  * `getFormattedAmountAttribute()`: Mengembalikan format nominal Rupiah.

### C. Model `CatatanIuranWarga`
* **`$primaryKey`**: `'iuran_id'`
* **`$fillable`**: `['no_kk', 'iuran_type_id', 'nominal', 'periode_bulan', 'periode_tahun', 'tanggal_pembayaran', 'recorded_by_user_id', 'approved_by_user_id', 'approved_at', 'status', 'payment_proof_path', 'rejection_notes']`
* **`$casts`**: `['nominal' => 'decimal:2', 'tanggal_pembayaran' => 'date', 'approved_at' => 'datetime', 'status' => PaymentStatus::class]`
* **Relationships**:
  * `kartuKeluarga()`: `BelongsTo` ke `KartuKeluarga` (`no_kk`).
  * `iuranType()`: `BelongsTo` ke `IuranType` (`iuran_type_id`).
  * `recorder()`: `BelongsTo` ke `User` (`recorded_by_user_id`).
  * `approver()`: `BelongsTo` ke `User` (`approved_by_user_id`).
  * `ledgerEntry()`: Polymorphic `MorphOne` ke `FinancialTransaction` (`reference_type`, `reference_id`).
* **Scopes**:
  * `scopePending($query)`: Filter status pending.
  * `scopeForKk($query, $noKk)`: Filter iuran milik KK tertentu.

---

## 🏷️ 5. Enum Dependency

| Enum Class | Type | Values | Used In Models |
| :--- | :--- | :--- | :--- |
| `TransactionType` | `string` | `INCOME`, `EXPENSE` | `FinancialTransaction` |
| `PaymentStatus` | `string` | `PENDING`, `APPROVED`, `REJECTED` | `CatatanIuranWarga` |
| `ContributionType`| `string` | `WAJIB`, `SUKARELA` | `IuranType` |
| `TransactionCategory` | `string` | `IURAN`, `DONASI`, `OPERASIONAL`, `ADJUSTMENT`, `LAINNYA` | `FinancialTransaction` |

---

## ⚙️ 6. Service Architecture

### A. `LedgerService`
* **Tanggung Jawab:** Manajemen pembukuan kas mutasi (pemasukan/pengeluaran), kalkulasi saldo dinamis, penomoran unik, dan posting transaksi penyesuaian (reversal).
* **Dependencies:** `FinancialTransaction` Model.
* **Method Utama:**
  1. `createTransaction(array $data): FinancialTransaction`:
     * Melakukan validasi nominal > 0.
     * Mengunci baris untuk mengamankan generate `transaction_number` tanpa tabrakan.
     * Membuat entry database.
  2. `createReversal(int $transactionId, string $reason, int $userId): FinancialTransaction`:
     * Dibungkus dalam `DB::transaction()`.
     * Mengambil data transaksi asal menggunakan `lockForUpdate()`.
     * Memastikan transaksi belum pernah di-reverse sebelumnya.
     * Mengisi kolom `adjusted_by_user_id`, `adjusted_at`, dan `adjusted_transaction_id` pada transaksi awal.
     * Membuat transaksi kas baru dengan tipe berlawanan (nominal positif), kategori `ADJUSTMENT`, deskripsi `"Koreksi untuk [TRX_NUMBER] - [Reason]"`, dan mengaitkannya dengan `adjusted_transaction_id` transaksi asal.
  3. `generateTransactionNumber(Carbon $date): string`:
     * Algoritma sekuensial harian: `TRX-[YYYYMMDD]-[4_DIGIT_SEQ]`.
     * Melakukan query `count` transaksi pada hari tersebut untuk menentukan sequence terbaru dengan perlindungan lock.
  4. `getBalance(string $rtCode = null): float`:
     * Menghitung saldo secara dinamis: `SUM(INCOME) - SUM(EXPENSE)`.
     * Mendukung filter cache untuk meminimalisasi beban database.

### B. `PaymentService`
* **Tanggung Jawab:** Siklus persetujuan iuran warga (baik pengajuan mandiri maupun input manual).
* **Dependencies:** `CatatanIuranWarga` Model, `LedgerService`.
* **Method Utama:**
  1. `submitPayment(array $data, UploadedFile $proof): CatatanIuranWarga`:
     * Menyimpan file bukti ke private folder: `receipts/[no_kk]/[filename]`.
     * Membuat data pembayaran dengan status `PENDING`.
  2. `approvePayment(int $paymentId, int $userId): bool`:
     * Dibungkus dalam `DB::transaction()`.
     * Mengunci record pembayaran dengan `lockForUpdate()`.
     * Mengubah status iuran menjadi `APPROVED`.
     * Mengisi metadata `approved_by_user_id` dan `approved_at`.
     * Memanggil `LedgerService@createTransaction` untuk memposting kas masuk (`INCOME`) berkategori `IURAN` dengan reference polimorfik ke objek `CatatanIuranWarga` tersebut.
  3. `rejectPayment(int $paymentId, string $notes, int $userId): bool`:
     * Mengubah status pembayaran menjadi `REJECTED` dan mengisi `rejection_notes`.

---

## 🔐 7. Policy Architecture

Otorisasi dikendalikan oleh tiga class Policy utama:

1. **`IuranTypePolicy`**:
   * `viewAny()`: Mengecek permission `view_finances`.
   * `manageGlobal()`: Membatasi hanya user berkode role `BENDAHARA_RW` yang dapat memicu `create`, `update`, dan `delete` jenis iuran.
2. **`FinancialTransactionPolicy`**:
   * `viewAny()`: Mengecek permission `view_finances`. Mengendalikan scoping di query level (Ketua RT hanya boleh memfilter data RT miliknya sendiri).
   * `create()`: Membatasi agar Ketua RT hanya boleh memposting kas berkode RT miliknya, sedangkan Bendahara RW memposting kas RW (`rt_code = null`).
   * `reverse()`: Hanya memperbolehkan Bendahara RW (global) atau Ketua RT (sesuai wilayah kerjanya) pada transaksi aktif yang belum pernah disesuaikan.
3. **`CatatanIuranWargaPolicy`**:
   * `viewAny()`: Membatasi data scoping per RT untuk pengurus RT.
   * `create()`: Memvalidasi bahwa Ketua RT hanya bisa mencatat manual iuran warga di RT-nya.
   * `verify()`: Membatasi agar hanya Bendahara RW yang bisa menyetujui/menolak antrean transfer.
   * `downloadReceipt()`: Memverifikasi kepemilikan file. Admin RT boleh mengunduh jika KK berasal dari wilayahnya; warga boleh mengunduh jika KK miliknya cocok dengan sesi terverifikasi.

---

## 🎮 8. Controller Design

### A. `FinanceDashboardController`
* *Tanggung Jawab:* Menyajikan statistik saldo, kas masuk, kas keluar, dan grafik arus kas bulanan.
* *Otorisasi:* `viewAny` di `FinancialTransactionPolicy`.
* *Service:* `LedgerService@getBalance`.
* *Response:* Admin Blade View (`admin.finance.dashboard`).

### B. `FinancialTransactionController`
* *Tanggung Jawab:* Menampilkan histori mutasi kas, input kas masuk/keluar manual, dan trigger reversal.
* *Otorisasi:* `view` / `create` / `reverse` di `FinancialTransactionPolicy`.
* *Request Validation:* `StoreTransactionRequest`.
* *Service:* `LedgerService`.
* *Response:* Redirect dengan flash message.

### C. `IuranTypeController`
* *Tanggung Jawab:* CRUD master jenis iuran.
* *Otorisasi:* `manageGlobal` di `IuranTypePolicy`.
* *Request Validation:* `StoreIuranTypeRequest`.
* *Response:* Blade View (`admin.finance.iuran-types`).

### D. `PaymentVerificationController`
* *Tanggung Jawab:* Review antrean pending, approve transfer, dan reject transfer.
* *Otorisasi:* `verify` di `CatatanIuranWargaPolicy`.
* *Request Validation:* `ApprovePaymentRequest`, `RejectPaymentRequest`.
* *Service:* `PaymentService`.
* *Response:* Redirect dengan status sukses/gagal.

### E. `FinancialReceiptController`
* *Tanggung Jawab:* Mengalirkan file bukti transfer secara aman dari private storage.
* *Otorisasi:* `downloadReceipt` di `CatatanIuranWargaPolicy`.
* *Response:* Binary Stream response (`response()->file()`).

### F. `PortalFinanceController`
* *Tanggung Jawab:* Portal warga untuk cek tagihan, transparansi kas bulanan, dan submit bukti bayar mandiri.
* *Otorisasi:* Bypass login internal (validasi NIK/KK via request).
* *Request Validation:* `SubmitPaymentRequest`.
* *Service:* `PaymentService@submitPayment`.
* *Response:* Portal Blade View dengan data JSON tagihan.

---

## 📝 9. Form Request Design

### A. `StoreTransactionRequest`
* *Validation Rules:*
  * `transaction_type` => `required|in:INCOME,EXPENSE`
  * `category` => `required|string|max:50`
  * `amount` => `required|numeric|min:0.01`
  * `description` => `required|string|min:5`
  * `transaction_date` => `required|date|before_or_equal:today`
* *Sanitization:* Mengubah format input desimal koma (`,`) menjadi titik (`.`) untuk komparasi angka.

### B. `SubmitPaymentRequest`
* *Validation Rules:*
  * `no_kk` => `required|string|exists:kartu_keluargas,no_kk`
  * `iuran_type_id` => `required|exists:iuran_types,id`
  * `nominal` => `required|numeric|min:1`
  * `periode_bulan` => `required|integer|between:1,12`
  * `periode_tahun` => `required|integer|min:2026`
  * `payment_proof` => `required|file|image|mimes:jpg,jpeg,png|max:2048`
* *Authorization:* Mencocokkan input NIK/KK dengan data kependudukan sebelum file diproses.

### C. `ApprovePaymentRequest` / `RejectPaymentRequest`
* *Validation Rules:*
  * `payment_id` => `required|exists:catatan_iuran_wargas,iuran_id`
  * `rejection_notes` => `required_if:action,reject|nullable|string|min:5` (untuk reject).

---

## 📢 10. Event Driven Mapping

Berikut visualisasi aliran data asinkron pasca-transaksi utama:

```
[Aksi Database Selesai]
         │
         ▼
 ┌───────────────┐
 │ Trigger Event │
 └───────┬───────┘
         │
         ├──► PaymentSubmitted ──────► NotifyBendaharaNewPayment (Telegram)
         │
         ├──► PaymentApproved  ──────┬► LogActivityApprovedPayment (Log DB)
         │                           └► NotifyCitizenPaymentApproved (Telegram)
         │
         ├──► PaymentRejected  ──────┬► LogActivityRejectedPayment (Log DB)
         │                           └► NotifyCitizenPaymentRejected (Telegram)
         │
         ├──► TransactionCreated ────┬► LogActivityCreatedTransaction (Log DB)
         │                           ├► NotifyRTNewTransaction (Telegram)
         │                           └► UpdateLedgerBalancesCache (Cache Clear)
         │
         └──► AdjustmentCreated  ────┬► LogActivityCreatedAdjustment (Log DB)
                                     └──► LogAuditTrailAdjustment (Secure File Log)
```

---

## 🧱 11. Transaction Boundary

Tindakan-tindakan berikut **wajib** dilingkupi dengan `DB::transaction()` karena menyentuh lebih dari satu baris atau tabel guna mempertahankan integritas ACID:

1. **Approve Pembayaran Iuran (`PaymentService@approvePayment`):**
   * *Alur:* Mengubah status iuran menjadi `APPROVED` $\rightarrow$ generate nomor transaksi $\rightarrow$ mencatat kas masuk (`INCOME`) ke `financial_transactions`.
   * *Alasan:* Jika proses penulisan kas gagal (misal: karena *check constraint* nominal atau masalah koneksi), status iuran tidak boleh terlanjur berubah menjadi "Lunas".
2. **Koreksi Transaksi Kas (`LedgerService@createReversal`):**
   * *Alur:* Mengunci baris awal $\rightarrow$ update kolom adjustment transaksi awal $\rightarrow$ membuat baris penyeimbang baru berkategori `ADJUSTMENT`.
   * *Alasan:* Mencegah kondisi transaksi sepihak (ter-update tapi record koreksi gagal terbit).
3. **Pencatatan Iuran Manual Pengurus (`PaymentService`):**
   * *Alur:* Menyimpan data pembayaran `catatan_iuran_wargas` langsung berstatus `APPROVED` $\rightarrow$ mencatat kas masuk terkait.
   * *Alasan:* Menjamin pembayaran manual pengurus selalu sinkron dengan saldo kas Buku Besar.

---

## 🔒 12. Concurrency Strategy

Untuk menghindari ketidaksesuaian data akibat interaksi bersamaan (*concurrent requests*), strategi Laravel berikut diterapkan:

1. **Double Approve & Simultaneous Verification:**
   * Menerapkan **Pessimistic Locking** di database menggunakan `lockForUpdate()` saat verifikasi pembayaran diproses:
     ```php
     $payment = CatatanIuranWarga::where('iuran_id', $id)->lockForUpdate()->first();
     if ($payment->status !== PaymentStatus::PENDING) {
         throw new Exception("Transaksi ini sudah diproses sebelumnya.");
     }
     ```
2. **Duplicate Transaction Number & Reversal:**
   * Menggunakan kombinasi database `UNIQUE` constraint pada kolom `transaction_number` dan penanganan eksepsi (*exception handling*) dengan mekanisme *retry block* di `LedgerService`.
3. **Double Reversal Prevention:**
   * Sebelum memposting reversal, sistem mengecek ketersediaan data pembanding di tabel `financial_transactions`. Jika `adjusted_transaction_id IS NOT NULL` atau jika ada transaksi lain yang mereferensikan baris tersebut sebagai parent koreksi, proses dihentikan seketika.

---

## 🔢 13. Transaction Number Strategy

Nomor transaksi dibuat menggunakan format: **`TRX-YYYYMMDD-XXXX`**

### Algoritma Pembuatan Sequence:
1. Menentukan tanggal transaksi berjalan (format `YYYYMMDD`).
2. Melakukan query pencarian ke database menggunakan *pessimistic lock*:
   ```php
   $count = FinancialTransaction::whereDate('created_at', Carbon::today())
                                 ->lockForUpdate()
                                 ->count();
   $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
   $transactionNumber = "TRX-" . Carbon::today()->format('Ymd') . "-" . $sequence;
   ```
3. Indeks `UNIQUE` pada kolom database `transaction_number` menjamin jika terjadi tubrukan microsecond pada langkah (2), database akan menolak penyimpanan dan service akan menangkap *query exception* untuk mengulang proses (*retry*) dengan sequence berikutnya.

---

## 🚨 14. Error Recovery Strategy

1. **Database Rollback:**
   * Seluruh blok transaksi dibungkus `try-catch`. Kegagalan operasi database otomatis memicu `DB::rollBack()` dan mengembalikan pesan error yang aman ke pengguna tanpa memaparkan database log mentah.
2. **Queue Failure Handling:**
   * Notifikasi Telegram dan sinkronisasi cache dijalankan menggunakan antrean (*Laravel Queue*). Jika notifikasi gagal karena jaringan Telegram putus, *Queue Worker* akan melakukan *retry* otomatis hingga 3 kali dengan jeda 10 menit sebelum masuk ke tabel `failed_jobs`.
3. **Storage Failures:**
   * Verifikasi pengunggahan berkas bukti bayar dilakukan sebelum data database ditulis. Jika pemindahan file ke private folder gagal, transaksi dibatalkan sebelum menulis record database.

---

## 🛡️ 15. Security Strategy

* **IDOR Prevention:**
  Scoping rute `/finance/receipts/{iuran_id}/download` diamankan oleh `CatatanIuranWargaPolicy@downloadReceipt` yang mengecek apakah NIK/KK pengguna saat ini memiliki hak kepemilikan berkas iuran tersebut.
* **Mass Assignment Protection:**
  Penggunaan Form Request yang ketat memastikan hanya bidang masukan aman yang dapat diproses oleh Eloquent. Kolom sensitif seperti status approval diproteksi dari input luar.
* **Secure File Download (Streaming):**
  Aplikasi dilarang memberikan *symbolic link* langsung ke berkas bukti transfer. File dialirkan via stream:
  ```php
  return response()->file(storage_path('app/private/receipts/' . $payment->payment_proof_path));
  ```

---

## 🧪 16. Testing Strategy

Matriks pengujian diatur secara bertingkat untuk menjamin tidak ada regresi:

| Kategori Tes | Kasus Pengujian (Scenarios) | Hasil yang Diharapkan (Expected Results) |
| :--- | :--- | :--- |
| **Model Test** | Simpan nominal negatif pada `financial_transactions`. | Gagal (memicu pengecekan database constraint / Laravel validation). |
| **Model Test** | Hubungkan polimorfik `FinancialTransaction` dengan `CatatanIuranWarga`. | Relasi mengembalikan data objek asal secara tepat. |
| **Policy Test** | Ketua RT mencoba mengakses mutasi kas RT tetangga atau kas RW secara *write*. | Ditolak (HTTP 403 Forbidden). |
| **Policy Test** | Bendahara RW menyetujui antrean iuran tingkat RW. | Sukses (HTTP 200 / Redirect). |
| **Feature Test** | Warga mengunduh bukti transfer milik KK keluarga lain via URL langsung. | Ditolak (HTTP 403 Forbidden). |
| **Feature Test** | Dua admin melakukan approval bersamaan pada ID iuran pending yang sama. | Salah satu sukses, transaksi satunya ditolak aman dengan pesan informatif tanpa duplikasi data kas. |
| **Feature Test** | Posting transaksi koreksi kas masuk salah nominal. | Transaksi awal ter-update metadata auditnya, transaksi pengeluaran baru berkategori `ADJUSTMENT` terbentuk, saldo kas berkurang aman. |
| **E2E Test** | Warga submit iuran portal $\rightarrow$ Bendahara approve $\rightarrow$ Kas masuk terbit $\rightarrow$ Transparansi ter-update. | Seluruh data mengalir sinkron dari portal ke dashboard admin. |

---

## 📅 17. Sprint Dependency

Pengembangan modular wajib mengikuti urutan dependensi lurus berikut untuk meminimalkan perubahan struktur di tengah jalan:

```
[Sprint 1: Master Jenis Iuran] (iuran_types)
              │
              ▼
[Sprint 2: Buku Kas Ledger] (financial_transactions & LedgerService)
              │
              ▼
[Sprint 3: Iuran Manual Pengurus] (catatan_iuran_wargas & PaymentService)
              │
              ▼
[Sprint 4: Portal Warga & Queue] (Form Warga & Antrean Pending)
              │
              ▼
[Sprint 5: Dashboard Analytics] (Grafik & Kalkulasi Saldo Dinamis)
              │
              ▼
[Sprint 6: Ekspor & Integrasi Final] (Ekspor PDF/Excel & Uji Beban)
```

*Tidak ada ketergantungan melingkar (circular dependency) pada rantai pengerjaan ini.*

---

## ⚠️ 18. Risk Assessment

1. **Risiko Arsitektural Polimorfik (Universal Ledger):**
   * *Risiko:* Integritas relasi polimorfik di database tidak dicek langsung oleh foreign key native MySQL.
   * *Mitigasi:* Logika penghapusan diatur di tingkat Service Layer dengan verifikasi keaktifan data relasi sebelum model asal dihapus.
2. **Risiko Kebocoran Dokumen Sensitif:**
   * *Risiko:* Kebocoran data perbankan warga akibat akses IDOR pada gambar bukti bayar.
   * *Mitigasi:* Mengamankan rute download di balik middleware otorisasi berbasis Policy yang memverifikasi kepemilikan KK/NIK.
3. **Risiko Performa Dasbor:**
   * *Risiko:* Perhitungan saldo dinamis `SUM(amount)` melambat saat transaksi kas menumpuk.
   * *Mitigasi:* Menerapkan compound index `(rt_code, transaction_type, transaction_date)` dan caching dinamis pada total saldo.

---

## 📝 19. Implementation Checklist

Gunakan checklist ini selama masa pengerjaan kode (Sprint 1 - 6):

### Sprint 1: Master Jenis Iuran
- [ ] Buat migrasi tabel `iuran_types` beserta kolom-kolomnya.
- [ ] Buat model `IuranType` beserta `$casts` enum `ContributionType`.
- [ ] Buat `StoreIuranTypeRequest` untuk validasi parameter.
- [ ] Buat `IuranTypePolicy` dan daftarkan pada `AuthServiceProvider`.
- [ ] Buat `IuranTypeController` dan antarmuka kelola jenis iuran di admin panel.
- [ ] Jalankan Unit Test CRUD Jenis Iuran.

### Sprint 2: Buku Kas Ledger
- [ ] Buat migrasi tabel `financial_transactions` dengan check constraint `amount > 0` dan compound index.
- [ ] Buat model `FinancialTransaction` beserta casts enum `TransactionType` dan `TransactionCategory`.
- [ ] Terapkan relasi polimorfik `reference()` di model.
- [ ] Buat `LedgerService` beserta logika `generateTransactionNumber()`, `createTransaction()`, dan `createReversal()`.
- [ ] Buat `FinancialTransactionPolicy` untuk pembatasan data scoping per RT.
- [ ] Buat antarmuka Buku Kas Utama admin dengan filter kategori dan tipe transaksi.
- [ ] Buat tombol Aksi Koreksi di baris transaksi kas dan uji fungsionalitas reversal.

### Sprint 3: Iuran Manual Pengurus
- [ ] Buat migrasi tabel `catatan_iuran_wargas` tanpa kolom foreign key transaksi.
- [ ] Buat model `CatatanIuranWarga` dengan relasi polimorfik `ledgerEntry()`.
- [ ] Buat `PaymentService` beserta logika entry iuran manual terintegrasi `DB::transaction()`.
- [ ] Buat form pencatatan iuran warga manual di admin workspace.
- [ ] Jalankan integrasi testing pembayaran manual memicu penulisan kas masuk.

### Sprint 4: Portal Warga & Antrean Verifikasi
- [ ] Konfigurasi private disk di `config/filesystems.php`.
- [ ] Buat form upload bukti bayar di portal warga (tanpa login, validasi NIK/KK).
- [ ] Buat antrean pending verifikasi di admin workspace.
- [ ] Implementasikan `ApprovePaymentRequest` dan `RejectPaymentRequest`.
- [ ] Buat `FinancialReceiptController` untuk streaming file bukti bayar dari private folder.
- [ ] Terapkan middleware otorisasi di rute pengunduhan berkas.

### Sprint 5: Dashboard Analytics
- [ ] Integrasikan library Chart ke dashboard admin.
- [ ] Buat widget total saldo kas RW dan kas RT ter-filter dinamis.
- [ ] Buat widget tunggakan iuran bulanan berjalan.
- [ ] Tampilkan ringkasan visual kas masuk/keluar di portal transparansi warga.

### Sprint 6: Ekspor & Integrasi Final
- [ ] Integrasikan pustaka DomPDF untuk ekspor PDF laporan mutasi bulanan.
- [ ] Tambahkan ekspor Excel untuk rekapitulasi data pembayaran iuran warga.
- [ ] Lakukan pengujian End-to-End secara menyeluruh.

---

## 🏁 20. Final Readiness Assessment

* **Status Kesiapan:** **READY (SIAP)**
* **Persentase Kesiapan Arsitektur:** **100%**
* **Justifikasi Teknis:** Seluruh ketidakjelasan implementasi telah diuraikan ke dalam spesifikasi folder, skema database final, mapping service layer, rancangan otorisasi, penanganan konkurensi, strategi event, hingga skenario testing. Tidak ada lagi keputusan desain yang menggantung.

**Go / No-Go Decision untuk Sprint 1: GO!**
Pengembangan siap dilanjutkan ke **Phase 4A.1 - Sprint 1 (Master Data Jenis Iuran)**.
