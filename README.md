# Sistem Manajemen Sekolah

Sistem manajemen sekolah berbasis web menggunakan PHP dan MySQL dengan arsitektur yang terorganisir dan profesional.

## Struktur Folder

```
school_management_system/
├── assets/                     # Asset statis
│   ├── css/
│   │   ├── admin/             # CSS khusus admin
│   │   │   └── dashboard.css
│   │   └── public/            # CSS website publik
│   │       └── style.css
│   ├── js/
│   │   ├── admin/             # JavaScript admin
│   │   │   └── dashboard.js
│   │   └── public/            # JavaScript publik
│   │       └── main.js
│   ├── images/                # Gambar dan upload
│   │   └── uploads/
│   │       ├── teachers/
│   │       ├── students/
│   │       └── gallery/
│   └── fonts/                 # Font kustom
├── config/                    # Konfigurasi sistem
│   ├── database.php          # Koneksi database
│   ├── constants.php         # Konstanta aplikasi
│   └── functions.php         # Fungsi umum
├── includes/                  # File include
│   ├── auth-check.php        # Pengecekan autentikasi
│   └── navbar-public.php     # Navigasi publik
├── admin/                     # Panel admin
│   ├── dashboard.php
│   ├── users/
│   │   └── manage.php
│   ├── teachers/
│   │   └── manage.php
│   ├── students/
│   │   └── manage.php
│   ├── classes/
│   │   └── manage.php
│   ├── subjects/
│   │   └── manage.php
│   ├── attendance/
│   │   └── teacher.php
│   ├── gallery/
│   │   └── manage.php
│   └── includes/
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
├── teacher/                   # Dashboard guru
│   ├── dashboard.php
│   ├── profile.php
│   ├── classes/
│   │   └── my-class.php
│   ├── attendance/
│   │   └── student.php
│   ├── grades/
│   │   └── manage.php
│   └── includes/
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
├── public/                    # Website publik
│   ├── about.php
│   ├── gallery.php
│   └── contact.php
├── index.php                  # Homepage utama
├── auth/                      # Autentikasi
│   ├── login.php
│   └── logout.php
├── uploads/                   # Folder upload
│   ├── teachers/
│   ├── students/
│   └── gallery/
└── database/
    └── init.sql              # Schema database
```

## Fitur Utama

### Admin Panel
- **Dashboard**: Statistik dan ringkasan sistem
- **Manajemen User**: CRUD akun admin dan guru
- **Data Guru**: Kelola informasi guru lengkap
- **Data Siswa**: Manajemen data siswa per kelas
- **Kelas**: Pengaturan kelas dan wali kelas
- **Mata Pelajaran**: Manajemen mata pelajaran dan pengampu
- **Absensi Guru**: Pencatatan kehadiran guru
- **Profil Sekolah**: Kelola visi, misi, sejarah
- **Galeri**: Manajemen foto untuk website

### Dashboard Guru
- **Dashboard**: Informasi kelas dan mata pelajaran
- **Profil**: Kelola profil pribadi
- **Data Siswa**: Lihat siswa dari kelas wali
- **Absensi Siswa**: Input kehadiran siswa
- **Nilai Siswa**: Input dan kelola nilai

### Website Publik
- **Beranda**: Informasi utama sekolah
- **Profil**: Visi, misi, sejarah lengkap
- **Galeri**: Dokumentasi kegiatan sekolah
- **Kontak**: Informasi kontak dan lokasi

## Teknologi

- **Backend**: PHP 8.2 dengan PDO
- **Database**: MySQL
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6
- **DataTables**: Tabel interaktif
- **Select2**: Dropdown enhancement

## Instalasi

1. **Setup Environment**
   ```bash
   # Pastikan PHP 8.2+ dan MySQL terinstall
   # Untuk XAMPP/LARAGON, aktifkan Apache dan MySQL
   ```

2. **Clone Project**
   ```bash
   # Download project ke web server directory
   # Contoh: C:\xampp\htdocs\school_management_system
   ```

3. **Setup Database**
   ```sql
   -- Buat database baru
   CREATE DATABASE school_management;
   
   -- Import schema
   -- Jalankan file database/init.sql
   ```

4. **Konfigurasi Database**
   ```php
   // Edit file config/constants.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'school_management');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Sesuaikan password MySQL
   ```

5. **Set Permissions**
   ```bash
   # Pastikan folder uploads/ writable
   chmod 755 uploads/
   chmod 755 uploads/teachers/
   chmod 755 uploads/students/
   chmod 755 uploads/gallery/
   ```

## Login Default

- **Admin**: username `admin`, password `password`
- **Guru**: username `budi.santoso`, password `password`

## Keamanan

- **Authentication**: Role-based access control
- **Authorization**: Pengecekan hak akses per halaman
- **Input Sanitization**: Semua input difilter
- **Password Hashing**: bcrypt encryption
- **Session Management**: Timeout dan security

## Data Dummy

Sistem sudah include data dummy lengkap:
- 6 guru dengan data realistis
- 13 siswa across berbagai kelas
- 9 kelas (X, XI, XII dengan IPA/IPS)
- 12 mata pelajaran
- Sample attendance dan grades
- Profil sekolah lengkap
- Galeri dengan foto sample

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Kontribusi

1. Fork project
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## Lisensi

MIT License - Bebas digunakan untuk keperluan pendidikan dan komersial.

## Support

Untuk pertanyaan dan dukungan teknis, silakan buat issue di repository ini.