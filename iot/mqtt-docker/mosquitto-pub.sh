#!/bin/bash

echo "========================================="
echo "MQTT Publisher - mosquitto_pub"
echo "========================================="

read -p "Masukkan IP/Domain Broker: " HOST
read -p "Masukkan Topic: " TOPIC
read -p "Masukkan Username: " USERNAME
read -s -p "Masukkan Password: " PASSWORD
echo ""
read -p "Masukkan Pesan yang akan dikirim: " MESSAGE

read -p "Masukkan Port (default 1883): " PORT
PORT=${PORT:-1883}

read -p "Masukkan QoS (0/1/2, default 0): " QOS
QOS=${QOS:-0}

read -p "Enable Retain? (y/n, default n): " RETAIN_INPUT
if [[ "$RETAIN_INPUT" == "y" || "$RETAIN_INPUT" == "Y" ]]; then
    RETAIN="-r"
else
    RETAIN=""
fi

echo ""
echo "========================================="
echo "Ringkasan konfigurasi:"
echo "Host     : $HOST"
echo "Topic    : $TOPIC"
echo "Username : $USERNAME"
echo "Port     : $PORT"
echo "QoS      : $QOS"
echo "Retain   : ${RETAIN:-no}"
echo "Pesan    : $MESSAGE"
echo "========================================="

read -p "Lanjutkan publish? (y/n): " CONFIRM

if [[ "$CONFIRM" == "y" || "$CONFIRM" == "Y" ]]; then
    echo ""
    echo "Mempublish pesan..."
    
    mosquitto_pub -h "$HOST" \
                  -t "$TOPIC" \
                  -u "$USERNAME" \
                  -P "$PASSWORD" \
                  -m "$MESSAGE" \
                  -p "$PORT" \
                  -q "$QOS" \
                  $RETAIN
    
    if [ $? -eq 0 ]; then
        echo "✓ Berhasil! Pesan terkirim ke topic '$TOPIC'"
    else
        echo "✗ Gagal! Periksa koneksi dan parameter Anda"
        exit 1
    fi
else
    echo "Publish dibatalkan"
    exit 0
fi