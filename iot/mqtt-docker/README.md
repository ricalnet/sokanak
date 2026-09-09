## Struktur Direktori Proyek

Buat direktori dan file yang diperlukan:

```bash
mkdir -p config data log
touch config/pwfile
```

## Deployment MQTT Broker

Jalankan perintah berikut untuk memulai broker MQTT di latar belakang:

```bash
docker compose up -d
```

## Manajemen Pengguna MQTT

Tambahkan dua user sensor:
- `hx711` – untuk sensor berat
- `ultrasonic` – untuk sensor jarak/tinggi

Ikuti panduan berikut:

🔗 [Panduan Lengkap Instalasi MQTT Broker dengan Docker dan Mosquitto untuk IoT – Manajemen Pengguna dan Autentikasi](https://ricaldocs.github.io/posts/panduan-lengkap-instalasi-mqtt-broker-dengan-docker-dan-mosquitto-untuk-iot/#manajemen-pengguna-dan-autentikasi)

## Instalasi Service Systemd untuk Sensor

### Menyesuaikan Parameter Service

Sebelum menyalin file, pastikan file `mosquitto-sensor-berat.service` dan `mosquitto-sensor-tinggi.service` di dalam direktori `systemd/` telah disesuaikan parameter berikut:

- `User` – nama user Linux yang akan menjalankan service
- `ExecStart` – perintah lengkap untuk menjalankan script sensor

### Menyalin File ke Direktori Systemd

```bash
sudo cp systemd/mosquitto-sensor-tinggi.service systemd/mosquitto-sensor-berat.service /etc/systemd/system/
```

### Memuat Ulang Konfigurasi Systemd

```bash
sudo systemctl daemon-reload
```

## Menjalankan Layanan Sensor

Gunakan script berikut untuk memulai semua service sensor:

```bash
systemd/./start-sensors.sh
```

## Monitoring Log Real-time

Untuk melihat log langsung dari sensor tertentu, jalankan:

```bash
systemd/./show-sensor-nama_sensor.sh
```

Ganti `nama_sensor` dengan `berat` atau `tinggi` sesuai script yang tersedia.