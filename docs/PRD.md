# PRD

## 1. Overview

### Product Name

Sistem Informasi Manajemen (SIM) RW 047

### Product Type

Web-Based Community Management System dengan integrasi AI untuk digitalisasi administrasi RW, layanan warga, pengelolaan keuangan, dan manajemen aspirasi warga.

### Problem Statement

Operasional RW saat ini masih menghadapi masalah:

* Data warga tersebar pada banyak file spreadsheet.
* Terjadi duplikasi dan inkonsistensi data.
* Sulit melakukan pencarian data warga.
* Riwayat perubahan data tidak terdokumentasi dengan baik.
* Aspirasi warga tidak terstruktur dan sulit dipantau progresnya.
* Administrasi sangat bergantung pada individu tertentu.

### Product Goals

* Membangun database warga terpusat.
* Meningkatkan efisiensi administrasi RW.
* Meningkatkan transparansi keuangan.
* Menyediakan layanan surat yang terstruktur.
* Menyediakan sistem pelaporan warga yang dapat dilacak.
* Mengurangi pekerjaan manual melalui AI dan workflow automation.

### AI Integration Principles

#### Principle 1 — Laravel as Core System

Laravel merupakan Core System yang mengelola:

* Authentication
* Authorization
* Business Logic
* Data Validation
* Database Access
* API Layer
* Audit Trail

Seluruh proses bisnis utama harus berjalan meskipun AI tidak tersedia.

#### Principle 2 — MySQL as Source of Truth

MySQL adalah satu-satunya sumber data permanen sistem.

Seluruh data operasional harus tersimpan dan dikelola melalui Laravel.

Tidak ada sistem eksternal yang diperbolehkan menjadi sumber data utama.

#### Principle 3 — n8n as Workflow Engine

n8n digunakan sebagai Workflow Engine untuk:

* Workflow automation
* AI orchestration
* Notification workflow
* Background processing

n8n bukan penyimpan data utama.

#### Principle 4 — Gemini as AI Analysis Engine

Gemini digunakan sebagai AI Analysis Engine untuk:

* Text classification
* Urgency analysis
* Report summarization

Gemini tidak memiliki akses langsung ke database.

#### Principle 5 — AI as Enhancement Layer

AI merupakan Enhancement Layer.

AI bertugas membantu pengguna dan pengurus RW dalam:

* Analisis
* Klasifikasi
* Ringkasan informasi

AI tidak boleh menjadi dependency utama sistem.

Jika AI gagal, seluruh fitur utama sistem harus tetap berfungsi.

### Success Metrics

| Metric                 | Target |
| ---------------------- | ------ |
| Data terpusat          | 100%   |
| Tracking laporan       | 100%   |
| Tracking surat         | 100%   |
| Akurasi klasifikasi AI | ≥ 90%  |
| Mobile responsiveness  | 100%   |
| Availability           | 24/7   |

### Out of Scope

* Pembayaran online
* Tanda tangan digital tersertifikasi
* Integrasi Dukcapil
* Integrasi payment gateway
* Mobile App native Android/iOS

---

## 2. User Roles

### Role Hierarchy

```text
Super Admin
│
├── Ketua RW
├── Sekretaris RW
├── Bendahara RW
│
└── Ketua RT
     │
     └── Warga
```

### Warga

Permissions:

* View informasi RW
* View agenda kegiatan
* View transparansi keuangan
* Submit laporan
* View status laporan milik sendiri
* Submit permohonan surat
* View status surat milik sendiri

### Ketua RT

Permissions:

* View warga wilayah RT
* Approve perubahan data warga
* Manage iuran RT
* View laporan wilayah RT
* Update status laporan
* View pengajuan surat warga RT

### Sekretaris RW

Permissions:

* Manage surat
* Manage publikasi informasi
* View seluruh laporan
* Generate dokumen administrasi

### Bendahara RW

Permissions:

* Manage transaksi keuangan
* Manage iuran
* View dashboard keuangan
* Generate laporan keuangan

### Ketua RW

Permissions:

* View seluruh data sistem
* View executive dashboard
* Monitor seluruh laporan
* Approve kebijakan administratif
* View AI summary report

### Super Admin

Permissions:

* Full system access
* Manage roles
* Manage permissions
* Manage system settings
* Manage integrations

---

## 3. Requirements

### FR-01 Authentication & Authorization

Features:

* Login
* Logout
* Password Reset
* Role-Based Access Control (RBAC)
* Permission-Based Access Control

### FR-02 Resident Management

Features:

* Create resident
* Update resident
* Delete resident
* View resident
* Search resident
* Filter resident

Workflow:

```text
Draft
↓
Pending Validation
↓
Approved
↓
Active
```

Validation:

* Semua perubahan data warga harus disetujui Ketua RT.

### FR-03 Family Management

Features:

* Manage KK
* Manage anggota keluarga
* Relasi kepala keluarga

### FR-04 Complaint Management

Features:

* Submit laporan
* View laporan
* Update laporan
* Search laporan
* Filter laporan

Status Workflow:

```text
Submitted
↓
Classified
↓
In Progress
↓
Resolved
↓
Closed
```

Allowed Status:

* Submitted
* Classified
* In Progress
* Resolved
* Closed

### FR-05 AI Processing

Input:

* Complaint description

Output:

* Category
* Urgency
* Summary

Category Enum:

```text
INFRASTRUCTURE
ADMINISTRATIVE
SECURITY
ENVIRONMENT
UNCATEGORIZED
```

Urgency Enum:

```text
Low
Medium
High
```

Fallback Rule:

Jika AI gagal:

```text
Category = UNCATEGORIZED
Urgency = Medium
```

Sistem tetap harus berjalan.

### AI Use Cases

#### UC-AI-01 Complaint Categorization

Input:

* Laporan warga

Output:

* Kategori laporan

Tujuan:

* Membantu pengurus melakukan pengelompokan laporan secara otomatis.

#### UC-AI-02 Complaint Urgency Analysis

Input:

* Narasi laporan

Output:

* Tingkat urgensi

Tujuan:

* Membantu prioritas penanganan laporan.

#### UC-AI-03 Complaint Summary

Input:

* Kumpulan laporan warga

Output:

* Ringkasan laporan

Tujuan:

* Membantu Ketua RW memahami kondisi lingkungan secara cepat.

#### UC-AI-04 Executive Summary Generation

Input:

* Data laporan periode tertentu

Output:

* Ringkasan eksekutif

Tujuan:

* Membantu monitoring dan pengambilan keputusan.

#### UC-AI-05 Trend Insight Support

Input:

* Data laporan historis

Output:

* Insight kecenderungan masalah dominan

Tujuan:

* Membantu identifikasi masalah lingkungan yang sering terjadi.

### FR-06 Letter Management

Features:

* Submit request
* Approve request

Letter Workflow:

```text
Submitted
↓
RT Review
↓
RW Review
↓
Approved
↓
Completed
```

### FR-07 Financial Management

Contribution Types:

#### Mandatory Contribution

Rp50.000 per KK per bulan

#### Voluntary Contribution

Nominal fleksibel

Features:

* Record income
* Record expense
* Record contribution
* Financial report

### FR-08 Information Management

Features:

* News
* Announcement
* Event

Publication Status:

```text
Draft
Published
Archived
```

### FR-09 Audit Trail

Semua aktivitas penting harus dicatat.

Audit Log Structure:

```text
User
Action
Entity
Old Value
New Value
Timestamp
IP Address
```

### Non Functional Requirements

#### Security

* Password hashing
* Role protection
* Session management

#### Performance

* Dashboard load < 3 detik
* CRUD operation < 2 detik

#### Availability

* 24/7 access

#### Mobile First

* Responsive design wajib

#### Reliability

* AI failure tidak boleh menghentikan sistem

### AI Non Functional Requirements

#### AI-NFR-01 Non Blocking

Seluruh proses AI wajib berjalan secara asynchronous.

Proses AI tidak boleh menghambat:

* Login
* CRUD
* Pengajuan surat
* Pengelolaan data warga
* Pengelolaan keuangan

#### AI-NFR-02 Graceful Degradation

Jika AI tidak tersedia:

* Sistem tetap beroperasi normal.
* Laporan tetap tersimpan.
* Workflow utama tetap berjalan.

#### AI-NFR-03 Data Ownership

AI tidak boleh menyimpan data permanen.

Seluruh data tetap dimiliki dan dikelola Laravel.

#### AI-NFR-04 Controlled Access

AI tidak boleh melakukan:

* Direct database write
* Direct database delete
* Direct database update

Seluruh perubahan data harus melalui Laravel.

#### AI-NFR-05 Observability

Seluruh request AI wajib dapat dilacak melalui log sistem.

Minimal mencatat:

* Request timestamp
* Response timestamp
* Processing duration
* Success/failure status

---

## 4. Core Features

### F01 Resident Management

Modules:

* Data Warga
* Data KK
* Data RT
* Data RW

Dashboard Metrics:

* Total Warga
* Total KK
* Warga Aktif
* Warga Non Aktif

### F02 Complaint Management

Modules:

* Submit Complaint
* Complaint Tracking
* Complaint Resolution

Dashboard Metrics:

* Total Laporan
* Laporan Baru
* Laporan Diproses
* Laporan Selesai

### F03 AI Smart Processing

Modules:

* Smart Categorization
* Urgency Analysis
* Automatic Summary
* Executive Summary Generation
* Trend Insight Support

### F04 Letter Management

Modules:

* Request
* Approval

Dashboard Metrics:

* Surat Menunggu
* Surat Diproses
* Surat Selesai

### F05 Financial Management

Modules:

* Income
* Expense
* Contribution

Dashboard Metrics:

* Total Pemasukan
* Total Pengeluaran
* Saldo Kas

### F06 Information Management

Modules:

* News
* Announcement
* Event

### F07 Executive Dashboard

Modules:

* Resident Statistics
* Complaint Statistics
* Financial Statistics
* AI Summary

---

## 5. User Flow

### Complaint Flow

```text
Citizen
↓
Web App / Telegram Bot
↓
Laravel Backend
↓
Database
↓
Trigger n8n Workflow
↓
Gemini Analysis
↓
Return Result to Laravel
↓
Database Update
↓
Dashboard
```

### Letter Flow

```text
Citizen
↓
Submit Request
↓
RT Review
↓
RW Review
```

### Resident Update Flow

```text
Citizen
↓
Request Change
↓
RT Validation
↓
Approved
↓
Central Database Update
```

---

## 6. Architecture Overview

### Architecture Pattern

Hybrid Architecture

### System Components

#### Laravel (Core System)

Responsibilities:

* Authentication
* Authorization
* Business Logic
* REST API
* Database Access
* Audit Logging
* Integration Control

#### MySQL (Source of Truth)

Responsibilities:

* Persistent Storage
* Master Data Storage
* Transaction Storage

#### Telegram Bot

Responsibilities:

* Complaint Channel
* Notification Channel

#### n8n (Workflow Engine)

Responsibilities:

* Workflow Automation
* AI Orchestration
* Notification Workflow
* Background Processing

#### Gemini API (AI Analysis Engine)

Responsibilities:

* NLP Classification
* Urgency Analysis
* Summarization
* Insight Generation

### Architecture Rules

* Laravel adalah Core System.
* Laravel adalah satu-satunya jalur akses ke database.
* MySQL adalah source of truth.
* n8n adalah workflow engine.
* Gemini adalah AI analysis engine.
* AI merupakan enhancement layer.
* n8n tidak menyimpan data permanen.
* AI tidak memiliki akses database langsung.
* AI hanya memberikan rekomendasi.
* Semua keputusan tetap dilakukan manusia.

---

## 7. Database Overview

### Core Tables

#### roles

* id
* name

#### permissions

* id
* name

#### role_permissions

* role_id
* permission_id

#### users

* id
* role_id
* name
* email
* password

#### residents

* id
* nik
* kk_number
* full_name
* gender
* birth_date
* occupation
* phone
* address
* economic_status

#### complaints

* id
* resident_id
* category
* urgency
* status
* description
* ai_summary

#### complaint_histories

* id
* complaint_id
* status
* note

#### letters

* id
* resident_id
* letter_number
* purpose
* status

#### contributions

* id
* resident_id
* amount
* contribution_type
* period

#### financial_transactions

* id
* type
* amount
* description
* transaction_date

#### announcements

* id
* title
* content
* status

#### events

* id
* title
* description
* event_date

#### audit_logs

* id
* user_id
* action
* entity
* old_value
* new_value
* timestamp

### Required Relationships

```text
Role
└── Users

Resident
├── Complaints
├── Letters
└── Contributions

Complaint
└── ComplaintHistories
```

---

## 8. Tech Stack

### Backend

* Laravel 10
* PHP 8.1

### Database

* MySQL 8.0

### Frontend

* Blade
* Tailwind CSS
* Vite

### Infrastructure

* Nginx
* Laragon

### AI

* Google Gemini API

### Workflow

* n8n

### Communication

* Telegram Bot API

---

## 9. Project Rules Summary

### Development Rules

* Laravel wajib menjadi Core System.
* Laravel wajib menjadi source of control seluruh integrasi.
* Semua data utama disimpan di MySQL.
* AI tidak boleh melakukan write langsung ke database.
* Semua proses AI harus melalui Laravel.
* n8n wajib digunakan sebagai workflow engine.
* Gemini wajib digunakan sebagai AI analysis engine.
* Semua workflow AI harus asynchronous.
* AI hanya merupakan enhancement layer.
* Sistem harus tetap berjalan walaupun AI gagal.
* Semua modul wajib menggunakan RBAC.
* Semua perubahan data penting wajib tercatat pada audit log.
* Semua dashboard wajib mobile responsive.
* Semua status harus menggunakan enum yang telah didefinisikan.
* Tidak boleh ada hardcoded role.
* Tidak boleh ada hardcoded permission.
* Semua integrasi eksternal harus menggunakan service layer.

### Coding Rules

* Gunakan Laravel Service Layer.
* Gunakan Repository Pattern untuk data access.
* Gunakan Form Request Validation.
* Gunakan Policy untuk authorization.
* Gunakan Queue untuk proses AI.
* Gunakan Migration untuk seluruh perubahan database.
* Gunakan Seeder untuk data role dan permission awal.

### Definition of Done

Fitur dianggap selesai apabila:

* CRUD berjalan.
* Validation berjalan.
* Authorization berjalan.
* Audit log tercatat.
* Responsive pada mobile.
* Unit test minimum lulus.
* Tidak menghasilkan error kritis.
* Workflow sesuai status yang telah didefinisikan.
* Integrasi AI berhasil berjalan.
* Dashboard menampilkan data aktual.