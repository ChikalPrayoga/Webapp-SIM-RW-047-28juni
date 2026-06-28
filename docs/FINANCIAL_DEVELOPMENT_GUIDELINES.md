# FINANCIAL_DEVELOPMENT_GUIDELINES.md
# Pedoman Pengembangan & Standar Kode Modul Keuangan - SIM RW 047

Dokumen ini adalah pedoman implementasi resmi bagi pengembang selama pengerjaan **Sprint 1 sampai Sprint 6** pada Modul Keuangan SIM RW 047. Seluruh kode yang ditulis wajib mematuhi standar dan aturan yang ditetapkan dalam dokumen ini demi mencegah utang teknis (*technical debt*) dan menjaga integritas *Core System*.

---

## 🎯 1. Purpose

Tujuan dari dokumen ini adalah menyediakan aturan pengodean (*coding guidelines*) yang praktis, konkret, dan mudah diterapkan di dalam Laravel 10. Dokumen ini memastikan:
* Keseragaman arsitektur pengodean di seluruh komponen Modul Keuangan.
* Penerapan prinsip *Clean Code* dan pemisahan tanggung jawab (*separation of concerns*).
* Perlindungan keamanan data warga dan konsistensi ledger keuangan.

---

## 🏛️ 2. Project Principles

Semua kode wajib ditulis dengan bersandar pada delapan prinsip utama berikut:

1. **Core System Freeze:** Tidak boleh memodifikasi skema tabel inti, model inti, atau controller inti yang telah didefinisikan pada Phase 3.
2. **Authorization Freeze:** Otorisasi harus menggunakan permission yang sudah terdaftar (`view_finances` dan `manage_finances`). Pendaftaran permission baru di database dilarang.
3. **Laravel Source of Truth:** Manfaatkan fitur-fitur bawaan Laravel (Eloquent, Form Requests, Policies, Backed Enums, Events/Listeners) secara maksimal daripada menulis pustaka kustom.
4. **Service Layer First:** Logika bisnis murni dipisahkan ke dalam kelas Service khusus. Controller dilarang memanipulasi database secara langsung.
5. **Thin Controller:** Controller hanya bertindak sebagai pengatur (*orchestrator*) aliran data masuk dan keluar.
6. **Policy Based Authorization:** Validasi akses dilakukan di level Policy menggunakan metode otorisasi berbasis model.
7. **Event Driven:** Dampak sekunder dari transaksi (seperti pencatatan log aktivitas, pengiriman notifikasi Telegram, dan penghapusan cache) ditangani oleh Listeners secara asinkron.
8. **No Business Logic in Blade:** Blade hanya menampilkan data. Dilarang melakukan query database, pengecekan nominal uang, atau manipulasi logika bisnis di dalam file view.

---

## 💻 3. Coding Standards

### Naming Conventions:
* **Controllers:** CamelCase + akhiran `Controller` (contoh: `FinancialTransactionController`).
* **Services:** CamelCase + akhiran `Service` (contoh: `LedgerService`).
* **Models:** Singular CamelCase (contoh: `CatatanIuranWarga`).
* **Policies:** ModelName + akhiran `Policy` (contoh: `IuranTypePolicy`).
* **Form Requests:** VerbModelName + akhiran `Request` (contoh: `StoreTransactionRequest`).
* **Enums:** CamelCase (contoh: `TransactionType`).
* **Migrations:** snake_case dengan format `create_[table_name]_table` atau `add_[column_name]_to_[table_name]_table`.
* **Blade Views:** kebab-case, dikelompokkan berdasarkan folder entitas (contoh: `resources/views/admin/finance/transactions/index.blade.php`).

---

## 📁 4. Folder Convention

Struktur folder wajib mengikuti rancangan yang tertulis di `FINANCIAL_TECHNICAL_SPECIFICATION.md`:
* **Enums:** Ditempatkan di `app/Enums/`.
* **Services:** Ditempatkan di `app/Services/`.
* **Policies:** Ditempatkan di `app/Policies/`.
* **Form Requests:** Ditempatkan di `app/Http/Requests/`.
* **Events & Listeners:** Ditempatkan di `app/Events/` dan `app/Listeners/`.
* **Controllers:** Ditempatkan di `app/Http/Controllers/Finance/` (untuk panel admin) dan `app/Http/Controllers/Portal/` (untuk portal warga).

*Pengembang dilarang membuat folder tingkat tinggi baru di luar struktur Laravel standar.*

---

## 🎮 5. Controller Rules

Setiap method di Controller wajib mematuhi template fungsional berikut:
1. **Otorisasi:** Panggil `$this->authorize()` menggunakan Policy.
2. **Validasi:** Gunakan injeksi *Form Request* pada parameter method.
3. **Panggil Service:** Teruskan data ter-validasi ke kelas Service.
4. **Response:** Kembalikan View (untuk `GET`) atau Redirect dengan flash message (untuk `POST`/`PUT`/`DELETE`).

*Dilarang menggunakan DB::transaction, Eloquent::create, Log::info, atau Http::post di dalam Controller.*

---

## ⚙️ 6. Service Rules

* **Logika Bisnis:** Semua perhitungan saldo, pembuatan format nomor transaksi, dan penyimpanan model dilakukan di dalam Service.
* **DB Transaction:** Operasi multi-row atau multi-table wajib dibungkus `DB::transaction()`.
* **Event Dispatching:** Event dipicu dari dalam Service setelah transaksi database berhasil disimpan menggunakan `event(new EventClass($model))`.
* **Views Isolation:** Service hanya boleh mengembalikan model, array data, atau boolean. Service dilarang memanggil fungsi `view()`, `redirect()`, atau `response()`.

---

## 🔐 7. Policy Rules

* **Permission-Based:** Gunakan izin `view_finances` untuk gerbang read, dan `manage_finances` untuk gerbang write.
* **No Hardcoded Roles:** Otorisasi di tingkat Policy tidak boleh memeriksa nama peran secara langsung, melainkan harus menggunakan hak izin (permission) yang terdaftar, kecuali untuk fungsi global terpusat yang dibatasi oleh kepemilikan area (`rt_code`).
* **Scoping RT:** Policy wajib membandingkan properti `rt_code` dari model yang diakses dengan `rt_code` milik pengguna yang sedang login:
  ```php
  return $user->hasPermissionTo('view_finances') && $transaction->rt_code === $user->rt_code;
  ```

---

## 🗄️ 8. Model Rules

Model dilarang memproses query data berat secara manual. Isi Model dibatasi pada:
* **Relationships:** Definisi relasi (`belongsTo`, `hasMany`, `morphTo`, `morphOne`).
* **Accessors & Mutators:** Pemformatan data untuk tampilan (misal: Rupiah) atau pemformatan input (misal: nominal desimal).
* **Scopes:** Pemfilteran kueri yang sering digunakan (`scopeActive`, `scopePending`).
* **Casts:** Casting atribut kolom ke tipe data primitif atau kelas Enum.

---

## 🔨 9. Migration Rules

* **FK Ordering:** Migration yang direferensikan (seperti `iuran_types`) harus dibuat dan dimigrasikan sebelum tabel yang mereferensikannya (`catatan_iuran_wargas`).
* **Check Constraints:** Pasang constraint nominal positif di skema migrasi:
  ```php
  DB::statement('ALTER TABLE financial_transactions ADD CONSTRAINT chk_amount_positive CHECK (amount > 0)');
  ```
* **Indexes:** Definisikan compound index `(rt_code, transaction_type, transaction_date)` secara eksplisit untuk optimalisasi kueri saldo.
* **Down Migration:** Metode `down()` wajib menghapus foreign key terlebih dahulu sebelum menghapus tabel secara fisik.

---

## 🏷️ 10. Enum Rules

Semua kolom status dan tipe wajib dikaitkan dengan Backed Enum PHP 8.1:
* Dilarang menulis string mentah (*magic string*) di database query atau di perbandingan logika bisnis.
* Gunakan: `PaymentStatus::PENDING->value` atau `TransactionType::INCOME` secara konsisten.

---

## 📝 11. Validation Rules

* **Form Requests:** Semua parameter request dari form (termasuk unggahan file) wajib divalidasi melalui Form Request Class khusus.
* Dilarang menggunakan `$request->validate([...])` di dalam metode Controller.
* Lakukan sanitasi data nominal desimal (mengubah koma menjadi titik) di dalam metode `prepareForValidation()` milik Form Request.

---

## 🧱 12. Database Transaction Rules

Strategi penguncian transaksi database wajib diterapkan pada kasus-kasus berikut:

1. **Persetujuan Pembayaran Iuran (`PaymentService@approvePayment`):**
   * Wajib membungkus pembaruan status iuran dan pencatatan Buku Kas dalam satu blok `DB::transaction()`.
   * Wajib mengunci baris iuran menggunakan `lockForUpdate()` untuk mencegah double approval dari dua operator.
2. **Koreksi Transaksi Kas (`LedgerService@createReversal`):**
   * Wajib membungkus pembaruan tanda koreksi pada transaksi awal dan penulisan transaksi koreksi penyeimbang baru dalam satu transaksi database.
3. **Mekanisme Retry:**
   * Logika pembuatan nomor transaksi `TRX-YYYYMMDD-XXXX` dibungkus dengan blok `try-catch` database transaction yang akan mengulang proses (*retry*) sebanyak 3 kali jika database melempar pengecekan pelanggaran indeks unik (*unique index violation*).

---

## 📢 13. Event Rules

* **Kapan Menggunakan Event:** Gunakan event untuk memicu tugas-tugas non-transaksional sekunder seperti logging aktivitas admin, audit trail khusus, pengosongan cache saldo keuangan, dan notifikasi Telegram pengurus/warga.
* **Kapan DILARANG Menggunakan Event:** Dilarang menggunakan event untuk menulis entri Buku Kas utama (`financial_transactions`) saat iuran disetujui. Penulisan Buku Kas wajib bersifat **sinkron** di dalam database transaction utama untuk menjaga integritas data keuangan.

---

## 📝 14. Activity Log Rules

* Semua operasi keuangan yang dipicu oleh pengurus (`BENDAHARA_RW`, `KETUA_RT`) dan warga wajib dicatat ke dalam database `activity_logs`.
* Skema penulisan deskripsi log wajib mematuhi format string terstandarisasi yang tertuang pada `FINANCIAL_TECHNICAL_SPECIFICATION.md` agar konsisten dengan modul Surat dan Pengaduan.

---

## 🧪 15. Testing Rules

Pengembang wajib menulis test suite dengan cakupan minimal:
* **Model Test:** Memastikan relasi polimorfik, casting enum, dan check constraint database berfungsi tepat.
* **Policy Test:** Menguji batasan data scoping. Pastikan Ketua RT diblokir saat mencoba mengakses atau memanipulasi kas RT lain.
* **Feature Test:** Menguji seluruh siklus hidup iuran warga (dari submit portal, pending queue, verifikasi, hingga tercatat di Buku Kas).
* **Regression Test:** Memastikan penambahan modul keuangan tidak merusak otorisasi modul inti lainnya (seperti Surat atau Pengaduan).

---

## 🛡️ 16. Security Checklist

Pengembang wajib menandai daftar checklist keamanan berikut sebelum mempublikasikan kode:
- [ ] **IDOR Protection:** Rute download bukti transfer dilindungi policy berdasarkan KK/NIK.
- [ ] **No Public Storage:** Bukti transfer disimpan di `storage/app/private/receipts` dan disajikan via stream response.
- [ ] **Mass Assignment Protection:** Semua kolom sensitif (`status`, `amount`, `transaction_number`) dikecualikan dari properti `$fillable` model.
- [ ] **Sanitization:** Input teks deskripsi kas dibersihkan dari potensi injeksi skrip (XSS) menggunakan helper `strip_tags` atau `htmlspecialchars`.

---

## 🚀 17. Performance Checklist

Daftar optimasi performa query database:
- [ ] **Compound Indexing:** Pastikan migrasi menambahkan index `(rt_code, transaction_type, transaction_date)`.
- [ ] **N+1 Query Prevention:** Kueri index iuran warga wajib menggunakan eager loading: `CatatanIuranWarga::with(['iuranType', 'kartuKeluarga'])`.
- [ ] **Pagination:** Halaman Buku Kas dan Verifikasi wajib menggunakan pagination (`paginate(15)`), dilarang menggunakan `get()` secara polos.
- [ ] **Cache Clearing:** Listener `UpdateLedgerBalancesCache` wajib menghapus cache saldo saat transaksi kas baru ditambahkan atau dikoreksi.

---

## 🔍 18. Code Review Checklist

Sebelum kode digabungkan (*merged*) ke cabang utama (*main branch*), pemeriksa kode (*code reviewer*) harus memastikan:
1. Apakah logika bisnis berada di Service Layer, bukan di Controller?
2. Apakah file bukti bayar disimpan di direktori privat?
3. Apakah file migrasi menyertakan down migration yang aman dengan drop foreign key?
4. Apakah ada role check hardcoded seperti `role_id == 2` atau `role_name === 'BENDAHARA_RW'` di Controller atau Blade? (Wajib menggunakan `@can` dan Policy).
5. Apakah transaksi multi-tabel menggunakan `DB::transaction()`?

---

## 🏁 19. Definition of Done (DoD)

Sebuah Sprint dinyatakan selesai jika dan hanya jika memenuhi kriteria berikut:

- [ ] **Coding Selesai:** Seluruh kode fungsional ditulis tanpa meninggalkan komentar `TODO` atau fungsi debugging (`dd()`, `dump()`).
- [ ] **Migration Sukses:** File migrasi berhasil dieksekusi (`migrate`) dan dapat dibatalkan (`migrate:rollback`) tanpa eror.
- [ ] **Seeder Berjalan:** Seeder data iuran awal berhasil diisi ke database.
- [ ] **Otorisasi Lolos:** Otorisasi diuji menggunakan berbagai akun (Bendahara RW, Ketua RT, Ketua RW, Warga) dan mematuhi matriks kebijakan.
- [ ] **Unit & Feature Test Lolos:** Semua pengujian unit dan fitur berhasil dijalankan dengan status sukses (hijau).
- [ ] **Tidak ada Hardcoded Role/Permission:** Validasi murni bersandar pada permission Matrix dan ABAC Policy.
- [ ] **Dokumentasi Diperbarui:** Kode program didokumentasikan di internal docstrings.

---

## 🏃‍♂️ 20. Sprint Execution Rules

1. Pengerjaan Modul Keuangan dilakukan secara berurutan sesuai urutan Sprint 1 sampai Sprint 6 pada Roadmap.
2. Pengembang dilarang melompati Sprint atau mengerjakan tugas secara paralel yang melanggar urutan dependensi (misal: mengerjakan Sprint 4 sebelum Sprint 2 selesai).
3. Setiap akhir Sprint wajib diverifikasi menggunakan test suite terkait sebelum melanjutkan ke Sprint berikutnya.
4. Jika ditemukan bug desain pada blueprint di tengah jalannya implementasi, pengembang wajib menghentikan sementara proses coding dan mendaftarkannya pada laporan inkonsistensi arsitektur untuk ditinjau ulang.
