#!/bin/bash

echo "Menjalankan service mosquitto-sensor-tinggi..."
sudo systemctl start mosquitto-sensor-tinggi

if [ $? -eq 0 ]; then
    echo "✓ Service mosquitto-sensor-tinggi berhasil dijalankan"
else
    echo "✗ Gagal menjalankan service mosquitto-sensor-tinggi"
    exit 1
fi

echo "Menjalankan service mosquitto-sensor-berat..."
sudo systemctl start mosquitto-sensor-berat

if [ $? -eq 0 ]; then
    echo "✓ Service mosquitto-sensor-berat berhasil dijalankan"
else
    echo "✗ Gagal menjalankan service mosquitto-sensor-berat"
    exit 1
fi

echo ""
echo "Semua service berhasil dijalankan!"

echo ""
echo "Status service:"
sudo systemctl status mosquitto-sensor-tinggi --no-pager -l
sudo systemctl status mosquitto-sensor-berat --no-pager -l
