## 1. Mengubah Role Pengguna Menjadi Admin

**Purpose:**  
Memperbarui role pengguna tertentu menjadi `admin` berdasarkan nama pengguna (username).

**Syntax:**
```sql
UPDATE users 
SET 
    role = 'admin',
    updated_at = NOW()
WHERE username = 'nama_pengguna';
```

**Example:**
```sql
UPDATE users 
SET 
    role = 'admin',
    updated_at = NOW()
WHERE username = 'rical';
```

**Notes:**
- Kolom `updated_at` akan diisi dengan waktu saat query dieksekusi.
- Pastikan username yang dituju ada dalam tabel `users` untuk menghindari tidak ada baris yang terpengaruh.

## 2. Mengganti Password User Database

**Purpose:**  
Mengubah password untuk user database tertentu pada host lokal, kemudian memuat ulang hak akses dan keluar dari sesi database.

**Syntax:**
```sql
ALTER USER 'nama_user'@'localhost' IDENTIFIED BY 'password_baru';
FLUSH PRIVILEGES;
EXIT;
```

**Example:**
```sql
ALTER USER 'admin'@'localhost' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
EXIT;
```

**Notes:**
- `ALTER USER` digunakan untuk mengubah password (berlaku untuk MySQL/MariaDB versi modern).
- `FLUSH PRIVILEGES` diperlukan agar perubahan hak akses segera diterapkan tanpa merestart database.
- `EXIT` digunakan untuk keluar dari CLI database (seperti mysql command-line).