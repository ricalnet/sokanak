## Prosedur Migrasi dan Backup Database `posyandu_db`

### 1. Backup Database

Perintah berikut digunakan untuk melakukan dump database `posyandu_db` menjadi file SQL. Ganti `admin` dengan username database yang sesuai, dan masukkan password saat diminta.

```bash
mysqldump -u admin -p posyandu_db > sokanak_db_backup_prod.sql
```

**Keterangan:**
- `mysqldump`: Utilitas untuk melakukan backup database MySQL.
- `-u admin`: Menentukan user database.
- `-p`: Meminta input password secara interaktif.
- `posyandu_db`: Nama database sumber.
- `>`: Mengarahkan output dump ke file.
- `sokanak_db_backup_prod.sql`: Nama file hasil backup.

### 2. Mengunduh File Backup dari Cloud

Untuk menyalin file backup dari instance cloud ke mesin lokal, gunakan perintah `scp`. Pastikan file kunci privat (`.pem`) tersedia dan memiliki permission yang sesuai.

```bash
scp -i ~/Downloads/key.pem ubuntu@hostname:/home/ubuntu/sokanak_db_backup_prod.sql .
```

**Keterangan:**
- `scp`: Perintah untuk menyalin file secara aman melalui SSH.
- `-i ~/Downloads/key.pem`: Menentukan file kunci privat untuk otentikasi.
- `ubuntu@hostname`: Format `user@host` dari instance cloud.
- `/home/ubuntu/sokanak_db_backup_prod.sql`: Path sumber file di cloud.
- `.`: Direktori tujuan di mesin lokal (direktori saat ini).

### 3. Restore Database

Perintah berikut digunakan untuk mengembalikan (restore) database dari file SQL yang telah diunduh.

```bash
mysql -u admin -p posyandu_db < sokanak_db_backup_prod.sql
```

**Keterangan:**
- `mysql`: Klien command-line MySQL.
- `-u admin`: Menentukan user database.
- `-p`: Meminta input password secara interaktif.
- `posyandu_db`: Nama database target.
- `<`: Mengarahkan isi file SQL sebagai input.
- `sokanak_db_backup_prod.sql`: File dump yang akan direstore.

### 4. Mengunggah File Backup ke Cloud

Untuk mengunggah file backup dari mesin lokal ke instance cloud, gunakan perintah `scp` berikut:

```bash
scp -i ~/Downloads/key.pem sokanak_db_backup.sql ubuntu@hostname:/home/ubuntu/
```

**Keterangan:**
- `sokanak_db_backup.sql`: File sumber di mesin lokal.
- `ubuntu@hostname:/home/ubuntu/`: Tujuan path di instance cloud.