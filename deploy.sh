#!/bin/bash

echo "=== Deploy Aplikasi Sok!Anak ==="

echo "1. Menghapus versi lama..."
sudo rm -rf /var/www/html/*

echo "2. Menyalin aplikasi baru..."
sudo cp -r ../sokanak/* /var/www/html/

echo "3. Menghapus file sensitif..."
sudo rm -r /var/www/html/deploy.sh /var/www/html/docs /var/www/html/install-web-server-and-ssl.sh /var/www/html/README.md

echo "4. Permission granted..."
sudo chown -R www-data:www-data /var/www/html && sudo chmod -R 755 /var/www/html && sudo mkdir -p uploads/pengukuran && sudo chmod 777 uploads/pengukuran

echo "5. Restart Nginx..."
sudo systemctl restart nginx

echo "=== Deploy selesai ==="
echo "Status Nginx:"
systemctl status nginx --no-pager -l
