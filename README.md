# HRIS Mini

Aplikasi demo Human Resource Information System (HRIS) berbasis Laravel 10 untuk mengelola absensi, pengajuan cuti, persetujuan dua level, dan rekap kehadiran bulanan. Seluruh antarmuka menggunakan Bahasa Indonesia dengan locale `id` dan zona waktu `Asia/Jakarta`.

## Maksud, tujuan dari pembuatan sistem ini

Sistem ini dibuat sebagai prototipe yang siap didemokan untuk menunjukkan bagaimana proses administrasi kepegawaian harian dapat dilakukan secara terdigitalisasi. Fokus utamanya adalah menyederhanakan pencatatan kehadiran, pengajuan cuti, alur persetujuan berjenjang, dan penyusunan laporan bulanan yang sebelumnya sering dilakukan secara manual.

Tujuan spesifiknya adalah:

- Menyediakan satu tempat bagi karyawan untuk melakukan clock-in/clock-out dan mengajukan cuti.
- Memberikan atasan dan HR alur persetujuan yang jelas, lengkap dengan riwayat keputusan dan notifikasi.
- Menghasilkan rekap kehadiran bulanan yang dapat diekspor ke Excel dan PDF untuk kebutuhan pelaporan.
- Menjaga integritas data melalui validasi, pembatasan hak akses per peran, transaksi database, serta audit log.
- Menjadi contoh implementasi Laravel 10 yang mengikuti praktik baik: Form Request, Policy, Service layer, eager loading, pagination, dan feature test.

## Fitur apa saja yang ada pada sistem ini

| Modul | Fitur utama |
| --- | --- |
| Autentikasi | Login, lupa kata sandi, ubah kata sandi, verifikasi email, profil, dan paksa ganti kata sandi pada akun baru. Registrasi publik dinonaktifkan; akun dibuat oleh Admin HR. |
| Dashboard | Ringkasan jumlah karyawan aktif, departemen, jabatan, hari libur, absensi hari ini, dan pengajuan cuti menunggu sesuai peran. |
| Absensi | Clock-in dan clock-out harian, pencegahan duplikat clock-in, penentuan status Hadir/Terlambat/Pulang Cepat, serta riwayat dengan filter bulan dan status. Admin/manager dapat melihat data seluruh karyawan. |
| Pengajuan cuti | Pengajuan dengan validasi tanggal, alasan, lampiran wajib untuk jenis tertentu, pengecekan sisa kuota, dan penolakan rentang tanggal yang bertabrakan. Karyawan dapat membatalkan pengajuan yang masih menunggu. |
| Persetujuan cuti | Alur dua level: atasan/manager menyetujui level 1, HR/admin menyetujui level 2. Admin dapat bertindak pada kedua level. Penolakan dan catatan approve/reject tercatat dalam `leave_approvals`. |
| Saldo cuti | Kuota per jenis cuti dan tahun dibuat otomatis. Saldo hanya dipotong setelah persetujuan akhir level 2. |
| Rekap bulanan | Rekap per karyawan dengan filter bulan, tahun, departemen, dan karyawan; kartu ringkasan; grafik Chart.js; ekspor Excel dan PDF. |
| Administrasi | CRUD karyawan, departemen, jabatan, hari libur, jenis cuti, pengaturan jam kerja, serta halaman audit log. |
| Notifikasi | Notifikasi masuk untuk pengajuan, persetujuan, penolakan, dan pembatalan; bell badge dan halaman daftar notifikasi. |
| Keamanan | Middleware `role:admin,manager` dan `role:admin`, Policy untuk tiap modul, Form Request, mass-assignment protection, soft delete, dan transaksi database. |
| Antarmuka | Bootstrap 5 responsif dengan sidebar/navbar, badge status, SweetAlert2, flash message, empty state, dan loading state. Tampilan diperbarui dengan sistem desain kartu, mode terang/gelap, akses cepat, filter terstruktur, autofill akun demo, dan toggle kata sandi. |
| Pengujian | Feature test PHPUnit untuk hari kerja, kuota kurang, konflik tanggal, approval dua level, akses admin, dan duplikat clock-in. |

Tumpuan teknologi: Laravel 10.50, PHP 8.1+, MySQL, Blade + Bootstrap 5, Chart.js CDN, SweetAlert2, maatwebsite/excel 3.1, barryvdh/laravel-dompdf 2.2, dan PHPUnit 10.

## Screenshots

Berikut tampilan antarmuka utama aplikasi dengan desain sederhana: palet netral, satu warna aksen, dan tanpa dekorasi berlebih. Halaman selain login menggunakan akun Admin HR pada tampilan desktop lebar 1440px.

1. Halaman masuk dengan akun demo dan toggle kata sandi

   ![Halaman Masuk](docs/screenshots/00-login-desktop.png)

2. Dashboard

   ![Dashboard HRIS Mini](docs/screenshots/01-dashboard-desktop.png)

3. Riwayat absensi

   ![Riwayat Absensi](docs/screenshots/02-absensi-desktop.png)

4. Pengajuan cuti

   ![Pengajuan Cuti](docs/screenshots/03-pengajuan-cuti-desktop.png)

5. Persetujuan cuti

   ![Persetujuan Cuti](docs/screenshots/04-approval-desktop.png)

6. Rekap bulanan dan grafik kehadiran

   ![Rekap Bulanan](docs/screenshots/05-rekap-bulanan-desktop.png)

7. Administrasi karyawan

   ![Administrasi Karyawan](docs/screenshots/06-admin-karyawan-desktop.png)

## Diagram sistem

### Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ ATTENDANCES : mencatat
    USERS ||--o{ LEAVE_REQUESTS : mengajukan
    USERS ||--o{ LEAVE_BALANCES : memiliki
    USERS ||--o{ LEAVE_APPROVALS : memproses
    USERS }o--o{ USERS : "manager_id (atasan)"
    DEPARTMENTS ||--o{ USERS : berisi
    DEPARTMENTS ||--o{ POSITIONS : memiliki
    POSITIONS ||--o{ USERS : ditempati
    LEAVE_TYPES ||--o{ LEAVE_BALANCES : menentukan_kuota
    LEAVE_TYPES ||--o{ LEAVE_REQUESTS : mengklasifikasikan
    LEAVE_REQUESTS ||--o{ LEAVE_APPROVALS : memiliki_riwayat
    HOLIDAYS ||--o{ ATTENDANCES : "menandai hari libur"
    WORK_SETTINGS ||--o{ ATTENDANCES : "menentukan jam kerja"

    USERS {
        bigint id PK
        string nik UK
        string name
        string email UK
        enum role
        bigint department_id FK
        bigint position_id FK
        bigint manager_id FK
        boolean is_active
        boolean must_change_password
    }
    DEPARTMENTS {
        bigint id PK
        string name UK
        string code UK
    }
    POSITIONS {
        bigint id PK
        bigint department_id FK
        string name
    }
    LEAVE_TYPES {
        bigint id PK
        string name UK
        int quota_days
        boolean deducts_balance
        boolean requires_attachment
    }
    LEAVE_BALANCES {
        bigint id PK
        bigint user_id FK
        bigint leave_type_id FK
        smallint year
        int quota
        int used
    }
    LEAVE_REQUESTS {
        bigint id PK
        bigint user_id FK
        bigint leave_type_id FK
        date start_date
        date end_date
        int total_days
        enum status
    }
    LEAVE_APPROVALS {
        bigint id PK
        bigint leave_request_id FK
        bigint approver_id FK
        tinyint level
        enum action
        timestamp acted_at
    }
    ATTENDANCES {
        bigint id PK
        bigint user_id FK
        date date
        time clock_in
        time clock_out
        enum status
        boolean is_manual
    }
    HOLIDAYS {
        bigint id PK
        date date UK
        string description
    }
    WORK_SETTINGS {
        bigint id PK
        time check_in
        time check_out
        int late_tolerance_minutes
        json working_days
    }
```

### Alur persetujuan cuti dua level

```mermaid
flowchart TD
    A[Karyawan mengajukan cuti] --> B{Validasi pengajuan}
    B -->|Kuota kurang / tanggal bentrok / lampiran kurang| C[Pengajuan ditolak]
    B -->|Valid| D[Status: Menunggu Atasan]
    D --> E{Atasan / Manager}
    E -->|Tolak| F[Status: Ditolak + notifikasi]
    E -->|Setujui level 1| G[Status: Menunggu HR]
    G --> H{HR / Admin}
    H -->|Tolak| F
    H -->|Setujui level 2| I[Status: Disetujui]
    I --> J[Potong saldo cuti tahunan]
    J --> K[Catatan approval tersimpan + notifikasi]
    C --> L[Karyawan perbaiki pengajuan]
```

Keterangan:

- Manager hanya dapat memproses level 1.
- Admin dapat memproses level 1 maupun level 2.
- Saldo cuti dipotong hanya ketika level 2 disetujui.
- Pembatalan hanya tersedia untuk pengajuan yang belum selesai diproses.
