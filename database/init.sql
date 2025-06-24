-- School Management System Database Schema

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'teacher')),
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers table
CREATE TABLE IF NOT EXISTS teachers (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    hire_date DATE,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes table
CREATE TABLE IF NOT EXISTS classes (
    id SERIAL PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL,
    class_level VARCHAR(20) NOT NULL,
    teacher_id INTEGER REFERENCES teachers(id) ON DELETE SET NULL,
    academic_year VARCHAR(10) NOT NULL,
    capacity INTEGER DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id SERIAL PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    class_id INTEGER REFERENCES classes(id) ON DELETE SET NULL,
    birth_date DATE,
    gender VARCHAR(10) CHECK (gender IN ('male', 'female')),
    phone VARCHAR(20),
    address TEXT,
    parent_name VARCHAR(100),
    parent_phone VARCHAR(20),
    enrollment_date DATE DEFAULT CURRENT_DATE,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'graduated')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id SERIAL PRIMARY KEY,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    teacher_id INTEGER REFERENCES teachers(id) ON DELETE SET NULL,
    class_id INTEGER REFERENCES classes(id) ON DELETE CASCADE,
    credit_hours INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teacher Attendance table
CREATE TABLE IF NOT EXISTS attendances_teacher (
    id SERIAL PRIMARY KEY,
    teacher_id INTEGER REFERENCES teachers(id) ON DELETE CASCADE,
    attendance_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('present', 'absent', 'late', 'sick_leave', 'permission')),
    notes TEXT,
    recorded_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(teacher_id, attendance_date)
);

-- Student Attendance table
CREATE TABLE IF NOT EXISTS attendances_student (
    id SERIAL PRIMARY KEY,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    subject_id INTEGER REFERENCES subjects(id) ON DELETE CASCADE,
    attendance_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL CHECK (status IN ('present', 'absent', 'late', 'sick_leave', 'permission')),
    notes TEXT,
    recorded_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Grades table
CREATE TABLE IF NOT EXISTS grades (
    id SERIAL PRIMARY KEY,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    subject_id INTEGER REFERENCES subjects(id) ON DELETE CASCADE,
    exam_type VARCHAR(50) NOT NULL,
    score DECIMAL(5,2) NOT NULL CHECK (score >= 0 AND score <= 100),
    max_score DECIMAL(5,2) DEFAULT 100,
    exam_date DATE NOT NULL,
    semester VARCHAR(20) NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    recorded_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- School Profile table
CREATE TABLE IF NOT EXISTS school_profile (
    id SERIAL PRIMARY KEY,
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, email, password, role, full_name) VALUES 
('admin', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator')
ON CONFLICT (username) DO NOTHING;

-- Insert default school profile
INSERT INTO school_profile (school_name, vision, mission, history, address, phone, email, principal_name) VALUES 
('SMA Negeri 1 Jakarta', 
'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.', 
'Menyelenggarakan pendidikan yang berkualitas, mengembangkan potensi siswa secara optimal, dan membentuk karakter yang berakhlak mulia.', 
'SMA Negeri 1 Jakarta didirikan pada tahun 1950 dan telah menjadi salah satu sekolah terbaik di Jakarta dengan berbagai prestasi akademik dan non-akademik.', 
'Jl. Pendidikan No. 123, Jakarta Pusat, DKI Jakarta 10110', 
'(021) 1234567', 
'info@sman1jakarta.sch.id', 
'Dr. Siti Aminah, M.Pd.')
ON CONFLICT DO NOTHING;