# FINANCE_POLICY_MATRIX.md
# Matriks Kebijakan Otorisasi Keuangan - SIM RW 047

Dokumen ini memetakan setiap aksi (*Action*) pada Modul Keuangan ke izin (*Permission*), metode *Policy* Laravel, peranan (*Role*), dan batasan cakupan data (*Scope*). Matriks ini disusun berdasarkan **RBAC_FINAL_PERMISSION_MATRIX.md** dan **AUTHORIZATION_ARCHITECTURE.md**.

---

## 📊 1. Matriks Otorisasi Utama

### A. Model `FinancialTransaction` (Buku Kas / Ledger Utama)

| Aksi (Controller Action) | Laravel Policy Method | Permission | Peranan yang Diizinkan (Roles) | Cakupan Data & Batasan (Scope) |
| :--- | :--- | :--- | :--- | :--- |
| `index` | `viewAny` | `view_finances` | `BENDAHARA_RW`<br>`KETUA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW / KETUA_RW**: Global (Kas RW & seluruh Kas RT).<br>• **KETUA_RT**: Terbatas pada transaksi milik RT-nya sendiri (`rt_code == user->rt_code`) & Kas RW (Read-Only). |
| `show` | `view` | `view_finances` | `BENDAHARA_RW`<br>`KETUA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW / KETUA_RW**: Semua transaksi.<br>• **KETUA_RT**: Hanya transaksi milik RT-nya sendiri atau transaksi Kas RW. |
| `create`, `store` | `create` | `manage_finances` | `BENDAHARA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW**: Dapat membuat transaksi Kas RW (`rt_code = null`).<br>• **KETUA_RT**: Hanya dapat membuat transaksi Kas RT-nya (`rt_code == user->rt_code`). |
| `reverse` (Koreksi) | `reverse` | `manage_finances` | `BENDAHARA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW**: Dapat mengoreksi transaksi kas RW & RT.<br>• **KETUA_RT**: Hanya transaksi RT miliknya.<br>• **Aturan Bisnis**: Transaksi hanya bisa dikoreksi jika `adjusted_transaction_id` bernilai NULL dan belum pernah dikoreksi sebelumnya. |
| `edit`, `update` | `update` | *N/A (Disabled)* | *Tidak ada* | **DILARANG MUTLAK** (Return `false`). Buku Kas bersifat *immutable* untuk menjaga keabsahan audit. |
| `destroy` | `delete` | *N/A (Disabled)* | *Tidak ada* | **DILARANG MUTLAK** (Return `false`). Tidak boleh ada penghapusan fisik berkas kas. |

---

### B. Model `CatatanIuranWarga` (Transaksi Iuran Warga)

| Aksi (Controller Action) | Laravel Policy Method | Permission | Peranan yang Diizinkan (Roles) | Cakupan Data & Batasan (Scope) |
| :--- | :--- | :--- | :--- | :--- |
| `index` | `viewAny` | `view_finances` | `BENDAHARA_RW`<br>`KETUA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW / KETUA_RW**: Melihat seluruh riwayat iuran warga.<br>• **KETUA_RT**: Hanya melihat iuran warga yang berada di RT-nya sendiri (`kartu_keluargas.rt_code == user->rt_code`). |
| `show` | `view` | `view_finances` | `BENDAHARA_RW`<br>`KETUA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW / KETUA_RW**: Semua catatan iuran.<br>• **KETUA_RT**: Hanya milik warga di RT-nya. |
| `create`, `store` (Manual) | `create` | `manage_finances` | `BENDAHARA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW**: Mencatat iuran untuk semua KK.<br>• **KETUA_RT**: Hanya mencatat iuran KK yang terdaftar di RT-nya. |
| `approve`, `reject` | `verify` | `manage_finances` | `BENDAHARA_RW` | • **BENDAHARA_RW** (Terpusat): Memiliki hak eksklusif memverifikasi antrean transfer warga di bank utama RW.<br>• **KETUA_RT**: Dilarang melakukan approval transfer iuran mandiri. |
| `downloadReceipt` | `downloadReceipt` | `view_finances` | `BENDAHARA_RW`<br>`KETUA_RW`<br>`KETUA_RT` | • **BENDAHARA_RW / KETUA_RW**: Mengunduh bukti transfer apa saja.<br>• **KETUA_RT**: Hanya bukti transfer milik warga di RT-nya.<br>• **WARGA**: Diizinkan mengunduh bukti transfer miliknya sendiri setelah lolos verifikasi NIK/KK pada Portal Warga (dikelola di Controller via session). |
| `edit`, `update` | `update` | *N/A (Disabled)* | *Tidak ada* | **DILARANG MUTLAK** (Return `false`). Data pembayaran yang disetujui bersifat final. |
| `destroy` | `delete` | *N/A (Disabled)* | *Tidak ada* | **DILARANG MUTLAK** (Return `false`). |

---

### C. Model `IuranType` (Master Data Jenis Iuran)

| Aksi (Controller Action) | Laravel Policy Method | Permission | Peranan yang Diizinkan (Roles) | Cakupan Data & Batasan (Scope) |
| :--- | :--- | :--- | :--- | :--- |
| `index`, `show` | `viewAny` | `view_finances` | `BENDAHARA_RW`<br>`KETUA_RW`<br>`KETUA_RT` | Global (Semua pengurus berhak melihat jenis iuran aktif). |
| `create`, `store` | `manageGlobal` | `manage_finances` | `BENDAHARA_RW` | • **BENDAHARA_RW** (Terpusat): Mengonfigurasi iuran tingkat RW.<br>• **KETUA_RT**: Dilarang membuat jenis iuran baru. |
| `edit`, `update` | `manageGlobal` | `manage_finances` | `BENDAHARA_RW` | • **BENDAHARA_RW**: Mengubah parameter iuran.<br>• **KETUA_RT**: Dilarang. |
| `destroy` | `manageGlobal` | `manage_finances` | `BENDAHARA_RW` | • **BENDAHARA_RW**: Menonaktifkan jenis iuran (`is_active = false`). Pengapusan fisik dilarang jika iuran sudah direferensikan oleh pembayaran warga. |

---

## 🔒 2. Aturan Keamanan Tambahan

1. **Bypass Middleware Otorisasi Portal Warga:**
   * Warga mengakses portal tanpa login akun internal. Validasi kepemilikan data iuran dilakukan melalui Form Request khusus yang mencocokkan `no_kk` dan `nik` kepala keluarga yang diinput dengan database `kartu_keluargas` dan `penduduks`.
2. **Strict File Streaming:**
   * Kontroler pengunduhan bukti transfer dilarang melakukan redirect langsung ke URL file statis. File wajib dibaca dari direktori privat `/storage/app/private/receipts/` dan dikirim sebagai response stream dengan header `Content-Type` yang tepat setelah lolos pengecekan `downloadReceipt` Policy.
3. **Pemberlakuan mass-assignment protection:**
   * Model `FinancialTransaction` dan `CatatanIuranWarga` harus mengecualikan kolom `amount`, `nominal`, `transaction_number`, `status`, `reference_type`, dan `reference_id` dari `$fillable` masukan mentah warga, dan hanya diisi secara internal melalui `LedgerService` dan `PaymentService`.
