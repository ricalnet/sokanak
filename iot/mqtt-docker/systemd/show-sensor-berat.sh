#!/bin/bash

echo "Menampilkan log subscriber sensor berat (CTRL+C untuk keluar)..."
echo "Log file: /var/log/mosquitto-sensor-berat.log"
echo "========================================="
tail -f /var/log/mosquitto-sensor-berat.log
