-- ============================================
-- DATABASE: posyandu_db
-- ============================================

CREATE DATABASE IF NOT EXISTS posyandu_db;
USE posyandu_db;

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    desa VARCHAR(100) NOT NULL,
    kecamatan VARCHAR(100) NOT NULL,
    role ENUM('kader', 'admin') DEFAULT 'kader',
    last_login TIMESTAMP NULL DEFAULT NULL,
    login_attempts INT DEFAULT 0,
    last_attempt TIMESTAMP NULL DEFAULT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    must_change_password BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_username (username),
    INDEX idx_desa (desa),
    INDEX idx_kecamatan (kecamatan),
    INDEX idx_role (role),
    INDEX idx_last_login (last_login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: anak
-- ============================================
CREATE TABLE IF NOT EXISTS anak (
    id INT PRIMARY KEY AUTO_INCREMENT,
    -- Data Identitas Anak
    tgl_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    nomor_KK VARCHAR(20) NOT NULL,
    NIK VARCHAR(16) UNIQUE NOT NULL,
    nama_anak VARCHAR(100) NOT NULL,
    
    -- Data Pengukuran Terakhir
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    panjang_badan DECIMAL(5,2) DEFAULT NULL,
    lingkar_kepala DECIMAL(5,2) DEFAULT NULL,
    lingkar_lengan DECIMAL(5,2) DEFAULT NULL,
    
    -- Data Orang Tua/Wali
    nama_ortu VARCHAR(100) NOT NULL,
    nik_ortu VARCHAR(16) NOT NULL,
    hp_ortu VARCHAR(15) NOT NULL,
    nama_wali VARCHAR(100) DEFAULT NULL,
    hp_wali VARCHAR(15) DEFAULT NULL,
    
    -- Alamat
    rw VARCHAR(5) NOT NULL,
    desa VARCHAR(100) NOT NULL,
    kecamatan VARCHAR(100) NOT NULL,
    
    -- Foto
    foto_pengukuran VARCHAR(255) DEFAULT NULL,
    
    -- Metadata
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_nik (NIK),
    INDEX idx_nama_anak (nama_anak),
    INDEX idx_desa (desa),
    INDEX idx_kecamatan (kecamatan),
    INDEX idx_rw (rw),
    INDEX idx_created_at (created_at),
    INDEX idx_tgl_lahir (tgl_lahir),
    INDEX idx_jenis_kelamin (jenis_kelamin),
    INDEX idx_nomor_kk (nomor_KK),
    INDEX idx_nik_ortu (nik_ortu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: pengukuran
-- ============================================
CREATE TABLE IF NOT EXISTS pengukuran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    anak_id INT NOT NULL,
    tanggal_pengukuran DATE NOT NULL,
    
    -- Hasil Pengukuran
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    panjang_badan DECIMAL(5,2) DEFAULT NULL,
    lingkar_kepala DECIMAL(5,2) DEFAULT NULL,
    lingkar_lengan DECIMAL(5,2) DEFAULT NULL,
    
    -- Dokumentasi
    foto_pengukuran VARCHAR(255) DEFAULT NULL,
    catatan TEXT DEFAULT NULL,
    
    -- Metadata
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (anak_id) REFERENCES anak(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_anak_id (anak_id),
    INDEX idx_tanggal_pengukuran (tanggal_pengukuran),
    INDEX idx_created_by (created_by),
    INDEX idx_created_at (created_at),
    UNIQUE KEY unique_anak_tanggal (anak_id, tanggal_pengukuran)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: logs
-- ============================================
CREATE TABLE IF NOT EXISTS logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    old_data JSON DEFAULT NULL,
    new_data JSON DEFAULT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: settings
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: wilayah
-- ============================================
CREATE TABLE IF NOT EXISTS wilayah (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_wilayah VARCHAR(20) UNIQUE NOT NULL,
    nama_wilayah VARCHAR(100) NOT NULL,
    jenis ENUM('provinsi', 'kabupaten', 'kecamatan', 'desa', 'dusun') NOT NULL,
    parent_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_kode_wilayah (kode_wilayah),
    INDEX idx_jenis (jenis),
    INDEX idx_parent_id (parent_id),
    FOREIGN KEY (parent_id) REFERENCES wilayah(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: backup_history
-- ============================================
CREATE TABLE IF NOT EXISTS backup_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    file_size BIGINT,
    backup_type ENUM('full', 'partial') DEFAULT 'full',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_created_at (created_at),
    INDEX idx_backup_type (backup_type),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: sessions
-- ============================================
CREATE TABLE IF NOT EXISTS sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_data TEXT,
    
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER DATABASE
-- ============================================

-- Create database user jika belum ada (ganti kredensial bawaan)
CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'changeme';
GRANT ALL PRIVILEGES ON posyandu_db.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;