# PROJECT_RULES.md

# Project Rules

Dokumen ini merupakan aturan permanen yang wajib diikuti oleh seluruh AI Coding Agent dan developer selama pengembangan Sistem Informasi Manajemen (SIM) RW 047.

Dokumen ini melengkapi:

* PRD.md
* DATABASE_SCHEMA.md

Prioritas implementasi:

1. Security
2. Data Integrity
3. Maintainability
4. Readability
5. Scalability
6. Performance

Jika terdapat konflik:

```text
PROJECT_RULES.md
↓
PRD.md
↓
DATABASE_SCHEMA.md
↓
Implementasi
```

---

# 1. General Rules

## 1.1 Core Architecture Principle

Arsitektur resmi sistem:

```text
User
↓
Laravel (Core System)
↓
MySQL

Laravel
↓
n8n (Workflow Engine)
↓
Gemini (AI Analysis Engine)
↓
Laravel
↓
MySQL
```

Laravel merupakan:

* Core System
* Business Logic Layer
* Integration Control Layer
* API Layer
* Audit Layer

Seluruh proses bisnis utama wajib berjalan melalui Laravel.

Tidak ada sistem lain yang diperbolehkan menjadi pusat kontrol aplikasi.

---

## 1.2 Source of Truth Principle

MySQL adalah satu-satunya source of truth sistem.

Aturan:

* Data permanen hanya boleh disimpan di MySQL.
* Laravel adalah satu-satunya aplikasi yang boleh mengubah data utama.
* n8n tidak boleh menyimpan data permanen.
* Gemini tidak boleh menyimpan data permanen.
* Tidak ada service eksternal yang boleh menjadi sumber data utama.

---

## 1.3 AI Optional Principle

AI adalah Enhancement Layer.

AI bukan dependency utama sistem.

Seluruh fitur berikut wajib tetap berfungsi walaupun:

* Gemini tidak tersedia.
* n8n tidak tersedia.
* AI menghasilkan error.

Fitur yang wajib tetap berjalan:

* Login
* Manajemen Warga
* Manajemen KK
* Pengajuan Surat
* Approval Surat
* Laporan Warga
* Keuangan
* Pengumuman
* Agenda
* RBAC

AI hanya meningkatkan kualitas informasi.

AI tidak boleh menjadi syarat berjalannya proses bisnis.

---

## 1.4 Human Review Principle

AI tidak boleh mengambil keputusan bisnis secara otomatis.

AI hanya boleh:

* Memberikan rekomendasi
* Memberikan klasifikasi
* Memberikan prioritas
* Memberikan ringkasan
* Memberikan insight

Keputusan akhir tetap dilakukan oleh manusia.

Termasuk:

* Approval Surat
* Approval Perubahan Data
* Penentuan Tindakan Laporan
* Keputusan Administratif

---

## 1.5 Failure Isolation Principle

Kegagalan AI tidak boleh menyebabkan:

* Request gagal
* Transaksi gagal
* Penyimpanan data gagal
* Workflow utama gagal

Jika AI gagal:

* Data utama tetap tersimpan
* Workflow utama tetap berjalan
* Error dicatat ke log
* AIProcessingLogs diperbarui

---

## 1.6 Event Driven Integration Principle

Integrasi AI wajib berbasis event.

Contoh:

```text
Complaint Created
↓
Store Database
↓
Dispatch Event
↓
Queue Job
↓
Trigger n8n Workflow
↓
Gemini Analysis
↓
Laravel Process Result
↓
Database Update
```

Dilarang:

```text
Controller
↓
Gemini API
```

atau

```text
Controller
↓
n8n
```

secara langsung.

---

## 1.7 External Service Resilience Principle

Seluruh service eksternal dianggap tidak stabil.

Termasuk:

* Gemini
* Telegram
* n8n

Wajib:

* Timeout
* Retry
* Exception Handling
* Logging

Tidak boleh ada fitur utama yang bergantung penuh pada service eksternal.

---

## 1.8 Development Principles

Wajib:

* Mengutamakan readability.
* Mengutamakan maintainability.
* Mengutamakan keamanan.
* Menggunakan solusi Laravel standar jika memungkinkan.
* Menghindari duplikasi kode.
* Menghindari over-engineering.

Dilarang:

* Hardcoded credential
* Hardcoded API key
* Hardcoded environment URL
* Business logic di Blade

---

## 1.9 Project Structure

```text
app/
├── Enums
├── Http
├── Models
├── Services
├── Repositories
├── Jobs
├── Events
├── Listeners
├── Policies
├── Observers
├── DTOs
├── Support
└── Exceptions
```

---

# 2. Tech Stack Rules

## 2.1 Backend

Wajib:

* Laravel 10
* PHP 8.1

---

## 2.2 Database

Wajib:

* MySQL 8

Aturan:

* Seluruh perubahan schema menggunakan Migration.
* Seluruh relasi menggunakan foreign key.
* Seluruh perubahan database terdokumentasi.

---

## 2.3 Frontend

Wajib:

* Blade
* Tailwind CSS
* Vite

Pendekatan:

* Mobile First
* Responsive Design

---

## 2.4 Workflow Engine

Wajib menggunakan:

* n8n

Tanggung jawab:

* Workflow Automation
* AI Orchestration
* Background Workflow
* Notification Workflow

n8n tidak boleh:

* Menjadi source of truth
* Menyimpan data permanen
* Mengubah database secara langsung

---

## 2.5 AI Analysis Engine

Wajib menggunakan:

* Google Gemini API

Tanggung jawab:

* Categorization
* Priority Analysis
* Summarization
* Executive Summary
* Trend Insight

Gemini tidak boleh:

* Menulis ke database
* Menghapus data
* Mengubah data
* Menjalankan business logic

---

## 2.6 Notification

Wajib menggunakan:

* Telegram Bot API

Seluruh integrasi Telegram harus melalui Service Layer.

---

# 3. Coding Standards

## 3.1 Controller Rules

Controller hanya boleh:

* Menerima Request
* Memanggil Service
* Mengembalikan Response

Controller tidak boleh:

* Menulis business logic
* Menulis query kompleks
* Memanggil Gemini langsung
* Memanggil n8n langsung
* Memanggil Telegram langsung

---

## 3.2 Service Layer Rules

Seluruh business logic wajib berada pada Service Layer.

Service bertanggung jawab:

* Workflow bisnis
* Validasi bisnis
* Orkestrasi proses

---

## 3.3 Repository Rules

Repository digunakan untuk:

* Query kompleks
* Query reusable
* Reporting query
* Dashboard query

CRUD sederhana boleh menggunakan Eloquent melalui Service.

---

## 3.4 Validation Rules

Wajib menggunakan:

* Form Request Validation

---

## 3.5 Authorization Rules

Gunakan:

* Policy sebagai mekanisme utama
* Gate untuk kasus khusus

Dilarang:

```php
if ($user->role === 'SUPER_ADMIN')
```

---

## 3.6 Enum Rules

Seluruh status bisnis wajib menggunakan PHP Enum.

Lokasi:

```text
app/Enums
```

---

## 3.7 Queue Rules

Wajib menggunakan Queue untuk:

* AI Processing
* Telegram Notification
* Executive Summary Generation
* Trend Insight Generation

AI tidak boleh berjalan secara synchronous.

---

## 3.8 Event Rules

Gunakan Event + Listener untuk:

* Audit Logging
* Activity Logging
* AI Trigger
* Notification Trigger

---

## 3.9 Observability Rules

Seluruh proses AI wajib menghasilkan:

* AIProcessingLogs
* ActivityLogs
* Error Logs (jika gagal)

---

## 3.10 Testing Rules

Minimal:

* Feature Test untuk fitur utama
* Unit Test untuk business logic kritis

---

## 3.11 External Integration Rule

Seluruh integrasi eksternal wajib diakses melalui Integration Service.

Contoh:

app/Services/Integrations/

- GeminiService
- N8nService
- TelegramService

Controller, Job, Event, dan Listener tidak boleh melakukan HTTP request langsung ke service eksternal.

---

# 4. Database Rules

## 4.1 Database Ownership

Seluruh data utama dimiliki Laravel.

n8n tidak boleh melakukan:

* INSERT langsung
* UPDATE langsung
* DELETE langsung

Gemini tidak boleh mengakses database.

---

## 4.2 Audit Logging

Audit log wajib untuk:

* Warga
* KK
* Surat
* Laporan
* Keuangan
* Role
* Permission
* System Settings
* AI Result Application

---

## 4.3 Activity Logging

ActivityLogs digunakan untuk:

* Monitoring aktivitas pengguna
* Monitoring workflow
* Monitoring AI workflow

---

## 4.4 AI Processing Logs

Setiap request AI wajib menghasilkan AIProcessingLogs.

Minimal:

* Request Timestamp
* Response Timestamp
* Duration
* Status
* Model
* Error Message

---

## 4.5 Workflow Status

Status bisnis wajib mengikuti DATABASE_SCHEMA.md.

Dilarang membuat status baru tanpa perubahan dokumentasi.

---

## 4.6 Database Transaction

Gunakan transaction untuk:

* Approval Surat
* Approval Perubahan Data
* Pembayaran Iuran
* Operasi Multi Tabel

---

# 5. UI Rules

## 5.1 Design Principles

Prioritas:

* Mobile First
* Consistency
* Readability
* Accessibility

## 5.2 Dashboard Rules

Dashboard tidak boleh:

* Mengakses Gemini secara langsung
* Mengakses n8n secara langsung

Dashboard hanya menampilkan data yang sudah tersimpan di database.

## 5.3 AI UI Rules

Seluruh hasil AI harus diberi label yang jelas.

Contoh:

```text
AI Recommendation
AI Summary
AI Priority
```

Pengguna harus dapat membedakan:

* Data Sistem
* Hasil Analisis AI

---

# 6. Security Rules

## 6.1 Controlled Access Principle

AI tidak boleh:

* Direct Database Access
* Direct Database Write
* Direct Database Delete
* Direct Database Update

Seluruh perubahan data harus melalui Laravel.

---

## 6.2 Authentication

Gunakan:

* Laravel Authentication
* Password Hashing
* Session Management
* CSRF Protection

---

## 6.3 Authorization

Seluruh endpoint non-public wajib menggunakan authorization.

---

## 6.4 AI Output Security

Output AI dianggap sebagai data tidak terpercaya.

Wajib:

* Divalidasi
* Disanitasi
* Diverifikasi sebelum digunakan

---

# 7. Git Rules

## 7.1 Branch Strategy

```text
main
develop
feature/*
fix/*
```

## 7.2 Commit Message

```text
feat:
fix:
refactor:
docs:
test:
```

## 7.3 Pull Request Rules

Sebelum merge:

* Migration berhasil
* Test berhasil
* Authorization berjalan
* Audit Log berjalan
* Tidak ada error kritis

## 7.4 Definition of Done

Fitur dianggap selesai apabila:

* Requirement PRD terpenuhi
* Validation berjalan
* Authorization berjalan
* Audit Log berjalan
* Activity Log berjalan
* Migration tersedia
* Test lulus
* Mobile responsive
* Tidak ada bug kritis
* Mengikuti PROJECT_RULES.md
* Tetap berjalan saat AI tidak tersedia

---