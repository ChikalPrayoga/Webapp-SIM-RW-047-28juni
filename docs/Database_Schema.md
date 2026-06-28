# DATABASE_SCHEMA.md

# 1. Overview

## Purpose

Database SIM RW 047 dirancang sebagai pusat data (single source of truth) untuk seluruh operasional administrasi lingkungan RW 047.

Database mendukung:

* Manajemen pengguna dan RBAC
* Manajemen struktur organisasi RT dan RW
* Manajemen data kependudukan
* Manajemen kartu keluarga
* Validasi perubahan data warga
* Pengelolaan surat pengantar
* Pengelolaan aspirasi dan laporan warga
* Integrasi AI untuk klasifikasi laporan
* Pengelolaan iuran dan keuangan
* Publikasi informasi RW
* Audit aktivitas sistem
* Notifikasi sistem

---

## Design Principles

* Laravel merupakan satu-satunya aplikasi yang dapat mengubah data utama.
* MySQL menjadi source of truth seluruh data.
* AI tidak boleh melakukan write langsung ke database.
* Workflow AI bersifat asynchronous.
* Seluruh aktivitas penting wajib dapat diaudit.
* Seluruh status bisnis memiliki histori.
* Seluruh relasi mengikuti prinsip normalisasi minimal Third Normal Form (3NF).
* Tidak ada data yang bergantung pada string bebas apabila dapat direpresentasikan sebagai enum bisnis.

---

## Architectural Notes

Database ini dirancang untuk:

* Laravel 10
* PHP 8.1
* MySQL 8
* Eloquent ORM

Dokumen ini merupakan desain data konseptual dan logis.

Dokumen ini bukan SQL Schema.

---

# 2. Entities

---

# 2.1 Roles

Master data role sistem.

### Attributes

* role_id
* role_name
* description
* is_active

---

# 2.2 Permissions

Master data permission.

### Attributes

* permission_id
* permission_name
* description
* is_active

---

# 2.3 RolePermissions

Mapping role dan permission.

### Attributes

* role_permission_id
* role_id
* permission_id

---

# 2.4 Users

Akun internal sistem.

### Attributes

* user_id
* role_id
* username
* email
* password_hash
* full_name
* phone_number
* status
* last_login_at
* created_at
* updated_at
* deleted_at

---

# 2.5 OrganizationalPositions

Representasi seluruh jabatan organisasi.

Menggantikan model terpisah PengurusRT dan PengurusRW.

### Attributes

* position_id
* user_id
* position_type
* area_code
* start_date
* end_date
* is_active

### Position Types

* KETUA_RT
* KETUA_RW
* SEKRETARIS_RW
* BENDAHARA_RW
* SUPER_ADMIN

### Notes

area_code digunakan untuk:

* RT001
* RT002
* RT003
* RW047

Satu user hanya boleh memiliki satu jabatan aktif.

---

# 2.6 KartuKeluarga

Data keluarga.

### Attributes

* no_kk
* rt_code
* alamat_lengkap
* blok
* nomor_rumah
* status_kepemilikan_rumah
* created_at
* updated_at
* deleted_at

---

# 2.7 AnggotaKeluarga

Data individu warga.

### Attributes

* nik
* no_kk
* nama_lengkap
* jenis_kelamin
* tempat_lahir
* tanggal_lahir
* pekerjaan
* nomor_hp
* status_hubungan_keluarga
* status_sosio_ekonomi
* status_warga
* created_at
* updated_at
* deleted_at

---

# 2.8 ResidentChangeRequests

Permintaan perubahan data warga.

### Attributes

* request_id
* nik
* field_name
* old_value
* new_value
* current_status
* submitted_at

---

# 2.9 ResidentChangeHistories

Histori validasi perubahan data warga.

### Attributes

* history_id
* request_id
* actor_user_id
* previous_status
* new_status
* notes
* changed_at

---

# 2.10 PengajuanSurat

Data utama pengajuan surat.

### Attributes

* pengajuan_id
* nik
* nomor_surat
* jenis_surat
* keperluan
* current_status
* tanggal_pengajuan
* tanggal_selesai
* generated_document_path

---

# 2.11 LetterStatusHistories

Histori approval surat.

### Attributes

* history_id
* pengajuan_id
* actor_user_id
* previous_status
* new_status
* notes
* changed_at

---

# 2.12 LogLaporanAspirasi

Data utama laporan warga.

### Attributes

* aspirasi_id
* nik
* kanal_laporan
* teks_keluhan

* ai_category
* ai_priority
* ai_summary
* ai_confidence

* current_status
* submitted_at
* resolved_at

### Notes
* Seluruh field AI bersifat nullable.
* Laporan tetap dapat dibuat walaupun proses AI gagal.
* Data AI hanya merupakan hasil analisis tambahan dan bukan data utama sistem.

---

# 2.13 ComplaintAssignments

Penugasan laporan.

### Attributes

* assignment_id
* aspirasi_id
* assigned_by_user_id
* assigned_to_user_id
* assigned_at
* notes

---

# 2.14 ComplaintStatusHistories

Histori perubahan status laporan.

### Attributes

* history_id
* aspirasi_id
* actor_user_id
* previous_status
* new_status
* notes
* changed_at

---

# 2.15 ComplaintAttachments

Lampiran laporan.

### Attributes

* attachment_id
* aspirasi_id
* file_name
* file_path
* file_type
* uploaded_at

---

# 2.16 AIProcessingLogs

Log seluruh aktivitas AI.

### Attributes

- ai_log_id

- related_entity_type
- related_entity_id

- ai_feature

- request_timestamp
- response_timestamp
- processing_duration_ms

- ai_process_status

- ai_category
- ai_priority
- ai_summary
- ai_confidence

- model_name

- request_payload
- response_payload

- error_message

- created_at

### AI Features

- COMPLAINT_CATEGORIZATION
- COMPLAINT_PRIORITY
- COMPLAINT_SUMMARY
- EXECUTIVE_SUMMARY
- TREND_INSIGHT

### AI Process Status

- PENDING
- PROCESSING
- SUCCESS
- FAILED

### Notes

- Digunakan untuk observability.
- Tidak digunakan sebagai source of truth bisnis.
- Data utama tetap tersimpan pada entitas bisnis terkait.


---

# 2.17 CatatanIuranWarga

Catatan pembayaran iuran.

### Attributes

* iuran_id
* no_kk
* recorded_by_user_id
* jenis_iuran
* nominal
* alokasi
* periode_bulan
* periode_tahun
* tanggal_pembayaran
* financial_transaction_id

### Notes

Setiap pembayaran iuran wajib menghasilkan transaksi keuangan.

---

# 2.18 FinancialTransactions

Buku kas utama RW.

### Attributes

* transaction_id
* transaction_type
* category
* amount
* description
* transaction_date
* created_by_user_id

### Transaction Types

* INCOME
* EXPENSE

---

# 2.19 Pengumuman

Informasi publik.

### Attributes

* pengumuman_id
* created_by_user_id
* title
* content
* category
* publication_status
* published_at

---

# 2.20 AgendaKegiatan

Agenda kegiatan RW.

### Attributes

* agenda_id
* created_by_user_id
* title
* description
* location
* start_datetime
* end_datetime
* status

---

# 2.21 Notifications

Notifikasi sistem.

### Attributes

* notification_id
* target_type
* target_id
* channel
* title
* message
* status
* sent_at

---

# 2.22 AuditLogs

Audit aktivitas sistem.

### Attributes

- audit_id

- user_id

- entity_type
- entity_id

- action

- old_value
- new_value

- ip_address
- user_agent

- source

- created_at

### Source Enum

- WEB
- TELEGRAM
- SYSTEM
- AI

### Notes

- Source digunakan untuk mengetahui asal perubahan data.
- Walaupun AI tidak boleh mengubah data secara langsung,
- hasil AI yang diterapkan melalui Laravel tetap harus tercatat.

---

# 2.23 SystemSettings

Konfigurasi sistem.

### Attributes

* setting_id
* setting_key
* setting_value
* description
* updated_at

---

# 2.24 ActivityLogs

Log aktivitas operasional sistem.

Digunakan untuk monitoring aktivitas pengguna dan workflow.

### Attributes

- activity_id

- user_id

- activity_type

- entity_type
- entity_id

- description

- ip_address

- created_at

### Examples
- LOGIN
- LOGOUT
- CREATE_COMPLAINT
- UPDATE_COMPLAINT
- SUBMIT_LETTER
- APPROVE_LETTER
- AI_REQUEST_SENT
- AI_RESPONSE_RECEIVED

### Notes

- Berbeda dengan AuditLogs.
- ActivityLogs digunakan untuk monitoring aktivitas.
- AuditLogs digunakan untuk pelacakan perubahan data.

---

# 3. Relationships

## RBAC

Roles → Users (1:N)

Roles → RolePermissions (1:N)

Permissions → RolePermissions (1:N)

---

## Organisasi

Users → OrganizationalPositions (1:1 aktif)

---

## Kependudukan

KartuKeluarga → AnggotaKeluarga (1:N)

AnggotaKeluarga → ResidentChangeRequests (1:N)

ResidentChangeRequests → ResidentChangeHistories (1:N)

---

## Surat

AnggotaKeluarga → PengajuanSurat (1:N)

PengajuanSurat → LetterStatusHistories (1:N)

Users → LetterStatusHistories (1:N)

---

## Aspirasi

AnggotaKeluarga → LogLaporanAspirasi (1:N)

LogLaporanAspirasi → ComplaintAssignments (1:N)

LogLaporanAspirasi → ComplaintStatusHistories (1:N)

LogLaporanAspirasi → ComplaintAttachments (1:N)

LogLaporanAspirasi → AIProcessingLogs (1:N)

Users → ComplaintAssignments (1:N)

Users → ComplaintStatusHistories (1:N)

---

## Keuangan

KartuKeluarga → CatatanIuranWarga (1:N)

CatatanIuranWarga → FinancialTransactions (1:1)

Users → FinancialTransactions (1:N)

---

## Informasi

Users → Pengumuman (1:N)

Users → AgendaKegiatan (1:N)

---

## Audit

Users → AuditLogs (1:N)

---

## Notifikasi

Notifications dapat berelasi ke:

* PengajuanSurat
* LogLaporanAspirasi
* AnggotaKeluarga
* Users

melalui target_type dan target_id.

---

## AI

LogLaporanAspirasi
→ AIProcessingLogs (1:N)

Users
→ ActivityLogs (1:N)

Users
→ AuditLogs (1:N)

---

# 4. Constraints

## User Constraints

* Username harus unik.
* Email harus unik.
* Satu user hanya boleh memiliki satu jabatan aktif.
* Satu user hanya boleh memiliki satu role.

---

## Resident Constraints

* NIK harus unik.
* Nomor KK harus unik.
* Setiap warga harus terhubung ke satu KK.
* Satu KK minimal memiliki satu anggota keluarga.

---

## Change Request Constraints

* Perubahan data tidak boleh langsung mengubah data warga.
* Perubahan data harus melalui proses validasi.

---

## Letter Constraints

* Nomor surat harus unik.
* Surat wajib memiliki pemohon.
* Surat wajib memiliki histori status.
* Surat tidak boleh Completed tanpa Approved.

---

## Complaint Constraints

* Laporan wajib memiliki pelapor.
* Status awal laporan = SUBMITTED.
* Laporan wajib memiliki histori status.
* Assignment bersifat opsional.
* AI gagal tidak boleh menyebabkan laporan gagal tersimpan.

---

## AI Constraints

- Seluruh field AI bersifat opsional (nullable).
- Kegagalan proses AI tidak boleh menggagalkan penyimpanan laporan.
- AI tidak boleh membuat data utama.
- AI tidak boleh menghapus data utama.
- AI tidak boleh mengubah data utama secara langsung.
- Hasil AI hanya boleh diterapkan melalui Laravel.
- Setiap request AI wajib memiliki AIProcessingLogs.
- Setiap hasil AI yang diterapkan ke data utama wajib menghasilkan AuditLogs.

---

## Financial Constraints

* Nominal harus lebih dari nol.
* Setiap iuran menghasilkan satu transaksi kas.
* Transaksi kas tidak boleh dihapus secara fisik.
* Pengeluaran tidak boleh menghapus histori.

---

## Audit Constraints

* Audit log bersifat immutable.
* Audit log tidak boleh diubah.
* Audit log tidak boleh dihapus.

---

## Soft Delete Constraints

Soft delete direkomendasikan untuk:

* Users
* KartuKeluarga
* AnggotaKeluarga
* Pengumuman
* AgendaKegiatan

---

# 5. Enumerations

## UserStatus

* ACTIVE
* INACTIVE

---

## LetterStatus

* SUBMITTED
* RT_REVIEW
* RW_REVIEW
* APPROVED
* COMPLETED
* REJECTED

---

## ComplaintStatus

* SUBMITTED
* CLASSIFIED
* IN_PROGRESS
* RESOLVED
* CLOSED

---

## ValidationStatus

* PENDING
* APPROVED
* REJECTED

---

## PublicationStatus

* DRAFT
* PUBLISHED
* ARCHIVED

---

## NotificationStatus

* PENDING
* SENT
* FAILED

---

## TransactionType

* INCOME
* EXPENSE

---

## ContributionType

* WAJIB
* SUKARELA

---

## UrgencyLevel

* LOW
* MEDIUM
* HIGH

---

## ComplaintCategory

- INFRASTRUCTURE
- ADMINISTRATIVE
- SECURITY
- ENVIRONMENT
- UNCATEGORIZED

---

## AIPriority

- LOW
- MEDIUM
- HIGH

---

## AIProcessStatus

- PENDING
- PROCESSING
- SUCCESS
- FAILED

---

## ActivityType

- LOGIN
- LOGOUT

- CREATE_RESIDENT
- UPDATE_RESIDENT

- CREATE_COMPLAINT
- UPDATE_COMPLAINT

- SUBMIT_LETTER
- APPROVE_LETTER

- AI_REQUEST_SENT
- AI_RESPONSE_RECEIVED

- SYSTEM_EVENT

---

# 6. Future Tables

Tabel berikut tidak wajib pada MVP tetapi telah diantisipasi oleh desain saat ini.

## SocialAidPrograms

Program bantuan sosial warga.

---

## SocialAidRecipients

Penerima bantuan sosial.

---

## RTPerformanceMetrics

KPI pelayanan RT.

---

## AIModelConfigurations

Konfigurasi model AI.

---

## TelegramBotSessions

Riwayat interaksi Telegram Bot.

---

## DataExports

Riwayat ekspor data dan laporan.