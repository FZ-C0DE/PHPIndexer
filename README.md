# Sistem Manajemen Sekolah

Sistem manajemen sekolah berbasis web menggunakan PHP dan MySQL dengan dua peran pengguna yaitu Admin dan Guru.

## Fitur Utama

### Admin Panel
- **Dashboard Admin**: Statistik lengkap sekolah
- **Manajemen Pengguna**: Kelola akun admin dan guru
- **Data Guru**: CRUD lengkap data guru
- **Data Siswa**: CRUD lengkap data siswa  
- **Data Kelas**: Kelola kelas dan wali kelas
- **Mata Pelajaran**: Kelola mata pelajaran dan pengampu
- **Absensi Guru**: Pencatatan kehadiran guru
- **Profil Sekolah**: Kelola informasi sekolah (visi, misi, sejarah, kontak)
- **Galeri**: Kelola foto untuk website publik

### Dashboard Guru
- **Dashboard Guru**: Informasi kelas dan mata pelajaran yang diampu
- **Data Siswa**: Lihat siswa berdasarkan kelas wali
- **Absensi Siswa**: Input kehadiran siswa
- **Nilai Siswa**: Input dan kelola nilai berdasarkan mata pelajaran
- **Profil**: Kelola profil pribadi

### Website Publik
- **Halaman Utama**: Informasi sekolah yang menarik
- **Profil Sekolah**: Visi, misi, dan sejarah
- **Galeri**: Dokumentasi kegiatan sekolah
- **Kontak**: Informasi kontak dan lokasi
- **Login**: Akses ke sistem manajemen

## Teknologi

- **Backend**: PHP 8.2 dengan PDO
- **Database**: MySQL
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6
- **DataTables**: Untuk tabel interaktif
- **Select2**: Untuk dropdown yang lebih baik

## Struktur Database

### Tabel Utama
- `users` - Akun pengguna (admin/guru)
- `teachers` - Data detail guru
- `students` - Data siswa
- `classes` - Data kelas dan wali kelas
- `subjects` - Mata pelajaran
- `attendances_teacher` - Absensi guru
- `attendances_student` - Absensi siswa
- `grades` - Nilai siswa
- `school_profile` - Profil sekolah
- `gallery` - Galeri foto

## Instalasi

1. **Persiapkan Environment**
   ```bash
   # Pastikan PHP 8.2+ dan MySQL terinstall
   # Untuk XAMPP/LARAGON, aktifkan Apache dan MySQL
   ```

2. **Clone/Download Project**
   ```bash
   # Download atau clone project ke folder web server
   # Contoh: C:\xampp\htdocs\school-management
   ```

3. **Setup Database**
   ```sql
   -- Buat database baru
   CREATE DATABASE school_management;
   
   -- Import struktur database
   -- Jalankan file database/init.sql
   ```

4. **Konfigurasi Database**
   ```php
   // Edit file config/database.php
   $host = 'localhost';
   $port = '3306';
   $dbname = 'school_management';
   $username = 'root';
   $password = ''; // Sesuaikan dengan password MySQL Anda
   ```

5. **Akses Website**
   ```
   http://localhost/school-management/
   ```

## Login Default

### Admin
- **Username**: `admin`
- **Password**: `password`

### Membuat Akun Guru
Akun guru hanya dapat dibuat melalui admin panel.

## Struktur Folder

```
school-management/
├── admin/                 # Admin panel
│   ├── includes/         # Header, sidebar, footer admin
│   ├── dashboard.php     # Dashboard admin
│   ├── teachers.php      # Manajemen guru
│   ├── students.php      # Manajemen siswa
│   ├── classes.php       # Manajemen kelas
│   ├── subjects.php      # Manajemen mata pelajaran
│   ├── school_profile.php # Profil sekolah
│   └── gallery.php       # Galeri
├── teacher/              # Dashboard guru
│   ├── includes/         # Header, sidebar, footer guru
│   └── dashboard.php     # Dashboard guru
├── assets/               # CSS, JS, gambar
│   └── css/
│       └── style.css     # Style utama
├── config/               # Konfigurasi
│   └── database.php      # Koneksi database
├── includes/             # Fungsi umum
│   └── functions.php     # Helper functions
├── database/             # Database schema
│   └── init.sql          # Struktur dan data awal
├── index.php             # Halaman utama publik
├── login.php             # Halaman login
└── README.md             # Dokumentasi
```

## Fitur Keamanan

- **Autentikasi**: Sistem login dengan session
- **Otorisasi**: Role-based access control (Admin/Guru)
- **Sanitasi Input**: Semua input difilter untuk mencegah XSS
- **Password Hashing**: Password di-hash menggunakan PHP password_hash()
- **SQL Injection Prevention**: Menggunakan prepared statements

## Desain

- **Tema**: Modern, elegant, professional
- **Warna**: Putih-merah sebagai warna utama
- **Responsive**: Mendukung desktop, tablet, dan mobile
- **UX/UI**: Interface yang intuitif dan mudah digunakan

## Kontribusi

Sistem ini dikembangkan sebagai solusi manajemen sekolah yang sederhana, aman, dan mudah digunakan. Cocok untuk SMA/SMK yang membutuhkan sistem digitalisasi data sekolah.

## Lisensi

MIT License - Bebas digunakan untuk keperluan pendidikan dan komersial.