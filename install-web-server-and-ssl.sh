#!/bin/bash

# Script instalasi Nginx + PHP-FPM + MariaDB dengan SSL self-signed

set -e 

echo "=== Memulai Instalasi Nginx + PHP-FPM + MariaDB dengan SSL ==="

echo "Mengupdate daftar paket..."
sudo apt update

# Install paket Nginx, PHP-FPM, MariaDB, dan dependensi PHP
echo "Menginstal Nginx, PHP-FPM, MariaDB, dan dependensi PHP..."
sudo apt install -y nginx mariadb-server php-fpm php-mysql \
    php-mysqli php-pear php-phpseclib php-mbstring php-zip \
    php-gd php-curl php-common php-imagick php-gmp php-intl php-apcu

echo "Menginstal OpenSSL..."
sudo apt install -y openssl

echo "Membuat direktori untuk sertifikat SSL..."
sudo mkdir -p /etc/nginx/ssl

echo "Membuat sertifikat SSL self-signed..."
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/nginx-selfsigned.key \
    -out /etc/nginx/ssl/nginx-selfsigned.crt \
    -subj "/C=ID/ST=Jakarta/L=Jakarta/O=Company/OU=IT/CN=example.com"

echo "Mengatur permission file sertifikat..."
sudo chmod 600 /etc/nginx/ssl/nginx-selfsigned.*

echo "Membackup konfigurasi default Nginx..."
sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.bak 2>/dev/null || true

echo "Membuat konfigurasi virtual host Nginx dengan SSL..."
SSL_CONF="/etc/nginx/sites-available/example-ssl"

sudo tee "$SSL_CONF" > /dev/null << 'EOF'
# Redirect HTTP ke HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name example.com www.example.com;
    return 301 https://$server_name$request_uri;
}

# Server block HTTPS
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name example.com www.example.com;
    
    root /var/www/html;
    index index.php index.html index.htm;
    
    # SSL Configuration
    ssl_certificate /etc/nginx/ssl/nginx-selfsigned.crt;
    ssl_certificate_key /etc/nginx/ssl/nginx-selfsigned.key;
    
    # SSL Protocols dan Cipher
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin" always;
    
    # Logging
    access_log /var/log/nginx/example-access.log;
    error_log /var/log/nginx/example-error.log;
    
    # Konfigurasi untuk PHP-FPM
    location / {
        try_files $uri $uri/ =404;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Deny access to sensitive files
    location ~ \.(htaccess|htpasswd|ini|log|conf)$ {
        deny all;
    }
}
EOF

echo "Mengaktifkan konfigurasi site SSL..."
sudo ln -sf "$SSL_CONF" /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true

PHP_FPM_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.3")
PHP_FPM_SOCKET="/var/run/php/php${PHP_FPM_VERSION}-fpm.sock"

echo "PHP-FPM version terdeteksi: $PHP_FPM_VERSION"
echo "Mengupdate socket path di konfigurasi..."

if [ ! -e "$PHP_FPM_SOCKET" ]; then
    PHP_FPM_SOCKET=$(find /var/run/php -name "php*-fpm.sock" 2>/dev/null | head -1)
    if [ -z "$PHP_FPM_SOCKET" ]; then
        PHP_FPM_SOCKET="/var/run/php/php-fpm.sock"
    fi
fi

sudo sed -i "s|fastcgi_pass unix:/var/run/php/php-fpm.sock;|fastcgi_pass unix:$PHP_FPM_SOCKET;|g" "$SSL_CONF"

echo "Menguji konfigurasi Nginx..."
if sudo nginx -t; then
    echo "Konfigurasi valid, mereload Nginx..."
    sudo systemctl reload nginx
    
    sudo systemctl enable nginx
    sudo systemctl enable mariadb
    sudo systemctl enable php${PHP_FPM_VERSION}-fpm 2>/dev/null || sudo systemctl enable php-fpm
    
    echo "Merestart PHP-FPM..."
    sudo systemctl restart php${PHP_FPM_VERSION}-fpm 2>/dev/null || sudo systemctl restart php-fpm
    
    echo "=== Instalasi Selesai ==="
    echo ""
    echo "Informasi:"
    echo "1. Nginx berjalan dengan SSL pada port 443 (dan redirect HTTP->HTTPS)"
    echo "2. PHP-FPM sudah terintegrasi dengan Nginx"
    echo "3. Sertifikat self-signed telah dibuat di:"
    echo "   - /etc/nginx/ssl/nginx-selfsigned.crt"
    echo "   - /etc/nginx/ssl/nginx-selfsigned.key"
    echo "4. Konfigurasi SSL ada di: /etc/nginx/sites-available/example-ssl"
    echo "5. Document root: /var/www/html"
    echo "6. Socket PHP-FPM: $PHP_FPM_SOCKET"
    echo ""
    echo "Cek status service:"
    sudo systemctl status nginx --no-pager | head -5
    sudo systemctl status php${PHP_FPM_VERSION}-fpm --no-pager 2>/dev/null || sudo systemctl status php-fpm --no-pager | head -5
    echo ""
    echo "Perhatian: Browser akan memperingatkan tentang sertifikat self-signed."
    echo "Untuk produksi, gunakan sertifikat dari CA terpercaya (Let's Encrypt)."
    echo ""
    echo "Selanjutnya:"
    echo "- Jalankan 'sudo mysql_secure_installation' untuk mengamankan MariaDB"
    echo "- Buat file info.php di /var/www/html untuk test PHP:"
    echo "  echo '<?php phpinfo(); ?>' | sudo tee /var/www/html/info.php"
    echo "- Akses: https://example.com/info.php (atau pakai IP server)"
else
    echo "Error: Konfigurasi Nginx tidak valid!"
    echo "Cek error dengan: sudo nginx -t"
    exit 1
fi