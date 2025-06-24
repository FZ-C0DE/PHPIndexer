-- School Management System Database Schema for MySQL
-- Create database first: CREATE DATABASE school_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Teachers table
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    hire_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Classes table
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL,
    class_level VARCHAR(20) NOT NULL,
    teacher_id INT,
    academic_year VARCHAR(10) NOT NULL,
    capacity INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    class_id INT,
    birth_date DATE,
    gender ENUM('male', 'female'),
    phone VARCHAR(20),
    address TEXT,
    parent_name VARCHAR(100),
    parent_phone VARCHAR(20),
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    teacher_id INT,
    class_id INT,
    credit_hours INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Teacher Attendance table
CREATE TABLE IF NOT EXISTS attendances_teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'sick_leave', 'permission') NOT NULL,
    notes TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id),
    UNIQUE KEY unique_teacher_date (teacher_id, attendance_date)
);

-- Student Attendance table
CREATE TABLE IF NOT EXISTS attendances_student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'sick_leave', 'permission') NOT NULL,
    notes TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id)
);

-- Grades table
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    exam_type VARCHAR(50) NOT NULL,
    score DECIMAL(5,2) NOT NULL CHECK (score >= 0 AND score <= 100),
    max_score DECIMAL(5,2) DEFAULT 100,
    exam_date DATE NOT NULL,
    semester VARCHAR(20) NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id)
);

-- School Profile table
CREATE TABLE IF NOT EXISTS school_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(200) NOT NULL,
    vision TEXT,
    mission TEXT,
    history TEXT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(100),
    principal_name VARCHAR(100),
    logo_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: 'password')
INSERT IGNORE INTO users (username, email, password, role, full_name) VALUES 
('admin', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator');

-- Insert sample teachers and users
INSERT IGNORE INTO users (username, email, password, role, full_name) VALUES 
('budi.santoso', 'budi.santoso@sman1jakarta.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Budi Santoso, S.Pd'),
('siti.rahayu', 'siti.rahayu@sman1jakarta.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Siti Rahayu, M.Pd'),
('ahmad.wijaya', 'ahmad.wijaya@sman1jakarta.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Ahmad Wijaya, S.Si'),
('rina.kartika', 'rina.kartika@sman1jakarta.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Rina Kartika, S.Sos'),
('dedi.kurniawan', 'dedi.kurniawan@sman1jakarta.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Dedi Kurniawan, S.Pd'),
('maya.sari', 'maya.sari@sman1jakarta.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Maya Sari, M.Pd');

INSERT IGNORE INTO teachers (user_id, teacher_id, phone, address, hire_date, status) VALUES 
(2, 'T001', '081234567890', 'Jl. Mawar No. 15, Jakarta Selatan', '2020-07-15', 'active'),
(3, 'T002', '081234567891', 'Jl. Melati No. 22, Jakarta Timur', '2019-08-10', 'active'),
(4, 'T003', '081234567892', 'Jl. Anggrek No. 8, Jakarta Barat', '2021-01-20', 'active'),
(5, 'T004', '081234567893', 'Jl. Dahlia No. 35, Jakarta Utara', '2020-03-12', 'active'),
(6, 'T005', '081234567894', 'Jl. Kenanga No. 18, Jakarta Pusat', '2018-09-05', 'active'),
(7, 'T006', '081234567895', 'Jl. Cempaka No. 27, Jakarta Selatan', '2022-02-14', 'active');

-- Insert sample classes
INSERT IGNORE INTO classes (class_name, class_level, teacher_id, academic_year, capacity) VALUES 
('IPA-1', 'X', 1, '2024/2025', 32),
('IPA-2', 'X', 2, '2024/2025', 30),
('IPS-1', 'X', 3, '2024/2025', 28),
('IPA-1', 'XI', 4, '2024/2025', 30),
('IPA-2', 'XI', 5, '2024/2025', 29),
('IPS-1', 'XI', 6, '2024/2025', 31),
('IPA-1', 'XII', 1, '2024/2025', 28),
('IPA-2', 'XII', 2, '2024/2025', 27),
('IPS-1', 'XII', 3, '2024/2025', 25);

-- Insert sample students
INSERT IGNORE INTO students (student_id, full_name, class_id, birth_date, gender, phone, address, parent_name, parent_phone, enrollment_date, status) VALUES 
('2024001', 'Andi Pratama', 1, '2008-03-15', 'male', '085123456789', 'Jl. Sudirman No. 45, Jakarta', 'Bapak Surya Pratama', '081987654321', '2024-07-15', 'active'),
('2024002', 'Sari Dewi', 1, '2008-05-22', 'female', '085123456790', 'Jl. Thamrin No. 12, Jakarta', 'Ibu Lestari Dewi', '081987654322', '2024-07-15', 'active'),
('2024003', 'Rizki Firmansyah', 1, '2008-01-10', 'male', '085123456791', 'Jl. Gatot Subroto No. 88, Jakarta', 'Bapak Edi Firmansyah', '081987654323', '2024-07-15', 'active'),
('2024004', 'Indira Salsabila', 2, '2008-07-03', 'female', '085123456792', 'Jl. Kuningan No. 67, Jakarta', 'Ibu Ratna Sari', '081987654324', '2024-07-15', 'active'),
('2024005', 'Fajar Nugroho', 2, '2008-09-18', 'male', '085123456793', 'Jl. Senayan No. 34, Jakarta', 'Bapak Agus Nugroho', '081987654325', '2024-07-15', 'active'),
('2024006', 'Putri Maharani', 3, '2008-11-25', 'female', '085123456794', 'Jl. Kemang No. 56, Jakarta', 'Ibu Dewi Maharani', '081987654326', '2024-07-15', 'active'),
('2023001', 'Dimas Prasetyo', 4, '2007-04-12', 'male', '085123456795', 'Jl. Pondok Indah No. 78, Jakarta', 'Bapak Wahyu Prasetyo', '081987654327', '2023-07-15', 'active'),
('2023002', 'Laila Kusuma', 4, '2007-06-28', 'female', '085123456796', 'Jl. Menteng No. 23, Jakarta', 'Ibu Siti Kusuma', '081987654328', '2023-07-15', 'active'),
('2023003', 'Arif Rahman', 5, '2007-02-14', 'male', '085123456797', 'Jl. Cikini No. 41, Jakarta', 'Bapak Abdul Rahman', '081987654329', '2023-07-15', 'active'),
('2023004', 'Citra Ayu', 6, '2007-08-07', 'female', '085123456798', 'Jl. Pancoran No. 19, Jakarta', 'Ibu Maya Ayu', '081987654330', '2023-07-15', 'active'),
('2022001', 'Bagas Setiawan', 7, '2006-05-30', 'male', '085123456799', 'Jl. Pasar Minggu No. 52, Jakarta', 'Bapak Joko Setiawan', '081987654331', '2022-07-15', 'active'),
('2022002', 'Nadia Putri', 8, '2006-12-16', 'female', '085123456800', 'Jl. Tebet No. 37, Jakarta', 'Ibu Lina Putri', '081987654332', '2022-07-15', 'active'),
('2022003', 'Yoga Pratama', 9, '2006-10-04', 'male', '085123456801', 'Jl. Kalibata No. 65, Jakarta', 'Bapak Hendra Pratama', '081987654333', '2022-07-15', 'active');

-- Insert sample subjects
INSERT IGNORE INTO subjects (subject_code, subject_name, teacher_id, class_id, credit_hours) VALUES 
('MAT001', 'Matematika', 1, 1, 4),
('IPA001', 'Fisika', 2, 1, 3),
('IPA002', 'Kimia', 3, 1, 3),
('IPA003', 'Biologi', 4, 1, 3),
('BHS001', 'Bahasa Indonesia', 5, 1, 4),
('BHS002', 'Bahasa Inggris', 6, 1, 3),
('MAT002', 'Matematika', 1, 2, 4),
('IPA004', 'Fisika', 2, 2, 3),
('IPS001', 'Sejarah', 3, 3, 3),
('IPS002', 'Geografi', 4, 3, 3),
('IPS003', 'Ekonomi', 5, 3, 3),
('IPS004', 'Sosiologi', 6, 3, 2);

-- Insert sample teacher attendance
INSERT IGNORE INTO attendances_teacher (teacher_id, attendance_date, status, notes, recorded_by) VALUES 
(1, '2024-06-20', 'present', NULL, 1),
(2, '2024-06-20', 'present', NULL, 1),
(3, '2024-06-20', 'late', 'Terlambat 15 menit', 1),
(4, '2024-06-20', 'present', NULL, 1),
(5, '2024-06-20', 'sick_leave', 'Sakit demam', 1),
(6, '2024-06-20', 'present', NULL, 1),
(1, '2024-06-21', 'present', NULL, 1),
(2, '2024-06-21', 'present', NULL, 1),
(3, '2024-06-21', 'present', NULL, 1),
(4, '2024-06-21', 'absent', 'Tidak hadir tanpa keterangan', 1);

-- Insert sample student attendance
INSERT IGNORE INTO attendances_student (student_id, subject_id, attendance_date, status, notes, recorded_by) VALUES 
(1, 1, '2024-06-20', 'present', NULL, 2),
(2, 1, '2024-06-20', 'present', NULL, 2),
(3, 1, '2024-06-20', 'late', 'Terlambat masuk kelas', 2),
(4, 2, '2024-06-20', 'present', NULL, 2),
(5, 2, '2024-06-20', 'absent', 'Sakit', 2),
(6, 3, '2024-06-20', 'present', NULL, 2),
(7, 4, '2024-06-20', 'present', NULL, 2),
(8, 4, '2024-06-20', 'present', NULL, 2);

-- Insert sample grades
INSERT IGNORE INTO grades (student_id, subject_id, exam_type, score, max_score, exam_date, semester, academic_year, recorded_by) VALUES 
(1, 1, 'UTS', 85.00, 100.00, '2024-06-15', 'Ganjil', '2024/2025', 2),
(1, 2, 'UTS', 78.00, 100.00, '2024-06-16', 'Ganjil', '2024/2025', 3),
(1, 3, 'Quiz', 92.00, 100.00, '2024-06-10', 'Ganjil', '2024/2025', 4),
(2, 1, 'UTS', 88.00, 100.00, '2024-06-15', 'Ganjil', '2024/2025', 2),
(2, 2, 'UTS', 82.00, 100.00, '2024-06-16', 'Ganjil', '2024/2025', 3),
(3, 1, 'UTS', 75.00, 100.00, '2024-06-15', 'Ganjil', '2024/2025', 2),
(4, 7, 'UTS', 90.00, 100.00, '2024-06-15', 'Ganjil', '2024/2025', 2),
(5, 8, 'UTS', 86.00, 100.00, '2024-06-16', 'Ganjil', '2024/2025', 3);

-- Insert default school profile
INSERT IGNORE INTO school_profile (school_name, vision, mission, history, address, phone, email, website, principal_name) VALUES 
('SMA Negeri 1 Jakarta', 
'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global dengan mengedepankan nilai-nilai Pancasila dan budaya Indonesia.', 
'1. Menyelenggarakan pendidikan yang berkualitas dengan kurikulum yang relevan dan inovatif\n2. Mengembangkan potensi siswa secara optimal melalui kegiatan akademik dan non-akademik\n3. Membentuk karakter siswa yang berakhlak mulia, mandiri, dan bertanggung jawab\n4. Menciptakan lingkungan belajar yang kondusif, aman, dan menyenangkan\n5. Menjalin kerjasama dengan berbagai pihak untuk meningkatkan kualitas pendidikan', 
'SMA Negeri 1 Jakarta didirikan pada tahun 1950 dengan nama SMA Negeri Jakarta. Sekolah ini merupakan salah satu sekolah menengah atas tertua dan terpandang di Jakarta. Sejak berdirinya, sekolah ini telah menghasilkan ribuan alumni yang berkontribusi di berbagai bidang, baik di tingkat nasional maupun internasional.\n\nPada tahun 1975, sekolah ini resmi berganti nama menjadi SMA Negeri 1 Jakarta dan menjadi sekolah percontohan untuk wilayah DKI Jakarta. Sekolah ini telah meraih berbagai prestasi akademik dan non-akademik, serta memiliki fasilitas yang lengkap dan modern untuk mendukung proses pembelajaran yang berkualitas.', 
'Jl. Pendidikan Raya No. 123, Menteng, Jakarta Pusat, DKI Jakarta 10110', 
'(021) 3914567', 
'info@sman1jakarta.sch.id', 
'https://www.sman1jakarta.sch.id',
'Dr. Siti Aminah, M.Pd.');

-- Insert sample gallery
INSERT IGNORE INTO gallery (title, description, image_url, category, is_featured) VALUES 
('Kegiatan Pembelajaran di Laboratorium', 'Siswa sedang melakukan praktikum kimia di laboratorium yang modern dan lengkap', 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=500', 'facilities', 1),
('Upacara Bendera Hari Senin', 'Kegiatan rutin upacara bendera setiap hari Senin untuk menumbuhkan jiwa nasionalisme', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500', 'activities', 1),
('Perpustakaan Modern', 'Fasilitas perpustakaan dengan koleksi buku lengkap dan akses internet untuk mendukung pembelajaran', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=500', 'facilities', 1),
('Lomba Karya Ilmiah Siswa', 'Siswa mempresentasikan hasil penelitian dalam lomba karya ilmiah tingkat sekolah', 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=500', 'achievements', 1),
('Gedung Sekolah', 'Gedung sekolah yang megah dengan arsitektur modern dan fasilitas lengkap', 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=500', 'facilities', 1),
('Kegiatan Ekstrakurikuler', 'Berbagai kegiatan ekstrakurikuler untuk mengembangkan bakat dan minat siswa', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=500', 'activities', 1),
('Penerimaan Siswa Baru', 'Kegiatan orientasi dan penerimaan siswa baru tahun ajaran 2024/2025', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=500', 'events', 0),
('Prestasi Olimpiade', 'Siswa peraih medali emas olimpiade matematika tingkat nasional', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=500', 'achievements', 0);