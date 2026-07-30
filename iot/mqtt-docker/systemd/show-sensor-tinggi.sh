#!/bin/bash

echo "Menampilkan log subscriber sensor berat (CTRL+C untuk keluar)..."
echo "Log file: /var/log/mosquitto-sensor-tinggi.log"
echo "========================================="
tail -f /var/log/mosquitto-sensor-tinggi.log
