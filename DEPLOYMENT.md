# 🚀 Panduan Deployment - Sistem KTA

Panduan lengkap untuk deploy aplikasi Sistem KTA ke production server.

---

## 📋 Pre-Deployment Checklist

### Server Requirements
- [x] VPS/Cloud Server (DigitalOcean, AWS, Vultr, dll)
- [x] Ubuntu 22.04 LTS atau CentOS 8+
- [x] Minimal 4GB RAM, 2 CPU cores
- [x] Domain name (contoh: kta.aabi.or.id)
- [x] SSL Certificate (Let's Encrypt recommended)

### Local Requirements
- [x] Git installed
- [x] SSH key untuk akses server
- [x] Database backup dari development

---

## 🖥️ Setup Server

### 1. Initial Server Setup

```bash
# Connect ke server
ssh root@your-server-ip

# Update system
apt update && apt upgrade -y

# Create deploy user
adduser deploy
usermod -aG sudo deploy

# Setup SSH key untuk deploy user
su - deploy
mkdir ~/.ssh
chmod 700 ~/.ssh
nano ~/.ssh/authorized_keys
# Paste your public key
chmod 600 ~/.ssh/authorized_keys
```

### 2. Install Required Software

```bash
# Install Nginx
sudo apt install nginx -y

# Install PHP 8.2 dan extensions
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml \
    php8.2-bcmath php8.2-intl -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Composer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Install Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y

# Install Git
sudo apt install git -y
```

### 3. Configure MySQL

```bash
# Login ke MySQL
sudo mysql

# Create database dan user
CREATE DATABASE kta_production;
CREATE USER 'kta_user'@'localhost' IDENTIFIED BY 'strong-password-here';
GRANT ALL PRIVILEGES ON kta_production.* TO 'kta_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📦 Deploy Application

### 1. Clone Repository

```bash
# Navigate ke web directory
cd /var/www

# Clone repository
sudo git clone https://github.com/JonathanZefanya/KTA-Revisi-Ke-1.git kta
sudo chown -R deploy:deploy /var/www/kta
cd kta
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install
npm run build
```

### 3. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit environment
nano .env
```

**`.env` Configuration:**
```env
APP_NAME="Sistem KTA AABI"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://kta.aabi.or.id

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kta_production
DB_USERNAME=kta_user
DB_PASSWORD=strong-password-here

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aabi.or.id
MAIL_FROM_NAME="${APP_NAME}"

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=database
```

### 4. Setup Database

```bash
# Run migrations
php artisan migrate --force

# Run seeders (optional, untuk data awal)
php artisan db:seed --class=AdminSeeder --force

# Create storage link
php artisan storage:link
```

### 5. Set Permissions

```bash
# Set ownership
sudo chown -R deploy:www-data /var/www/kta

# Set directory permissions
sudo find /var/www/kta -type d -exec chmod 755 {} \;
sudo find /var/www/kta -type f -exec chmod 644 {} \;

# Set writable directories
sudo chmod -R 775 /var/www/kta/storage
sudo chmod -R 775 /var/www/kta/bootstrap/cache
```

### 6. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## 🌐 Configure Nginx

### 1. Create Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/kta
```

**Configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name kta.aabi.or.id www.kta.aabi.or.id;
    
    root /var/www/kta/public;
    index index.php index.html;

    # Logging
    access_log /var/log/nginx/kta-access.log;
    error_log /var/log/nginx/kta-error.log;

    # Max upload size
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Disable access to sensitive files
    location ~ /\.env {
        deny all;
    }

    location ~ /\.git {
        deny all;
    }
}
```

### 2. Enable Site

```bash
# Create symlink
sudo ln -s /etc/nginx/sites-available/kta /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## 🔒 Setup SSL (Let's Encrypt)

### 1. Install Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 2. Obtain SSL Certificate

```bash
# Get certificate
sudo certbot --nginx -d kta.aabi.or.id -d www.kta.aabi.or.id

# Follow prompts:
# - Enter email address
# - Agree to terms
# - Choose to redirect HTTP to HTTPS (option 2)
```

### 3. Auto-Renewal

```bash
# Test renewal
sudo certbot renew --dry-run

# Renewal cron job already added by certbot
# Check with:
sudo systemctl status certbot.timer
```

---

## 🔄 Setup Supervisor (For Queue Workers)

### 1. Install Supervisor

```bash
sudo apt install supervisor -y
```

### 2. Create Worker Configuration

```bash
sudo nano /etc/supervisor/conf.d/kta-worker.conf
```

**Configuration:**
```ini
[program:kta-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kta/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/kta/storage/logs/worker.log
stopwaitsecs=3600
```

### 3. Start Worker

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Start worker
sudo supervisorctl start kta-worker:*

# Check status
sudo supervisorctl status
```

---

## 📅 Setup Cron Jobs

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/kta && php artisan schedule:run >> /dev/null 2>&1

# Add backup job (daily at 2 AM)
0 2 * * * cd /var/www/kta && php artisan backup:run >> /var/www/kta/storage/logs/backup.log 2>&1
```

---

## 🔍 Monitoring & Logging

### 1. Setup Log Rotation

```bash
sudo nano /etc/logrotate.d/kta
```

**Configuration:**
```
/var/www/kta/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 deploy www-data
    sharedscripts
}
```

### 2. Monitor Application

```bash
# Check Nginx logs
sudo tail -f /var/log/nginx/kta-error.log

# Check Laravel logs
tail -f /var/www/kta/storage/logs/laravel.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Check system resources
htop
```

---

## 🔄 Update & Maintenance

### Deploy Updates

```bash
# Navigate to project
cd /var/www/kta

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run migrations (if any)
php artisan migrate --force

# Clear and cache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart kta-worker:*
```

### Backup Strategy

**Database Backup:**
```bash
# Create backup script
nano ~/backup-db.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/deploy/backups"
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u kta_user -p'your-password' kta_production > $BACKUP_DIR/db_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completed: db_$DATE.sql.gz"
```

```bash
# Make executable
chmod +x ~/backup-db.sh

# Add to cron (daily at 2 AM)
0 2 * * * /home/deploy/backup-db.sh >> /home/deploy/backup.log 2>&1
```

**Files Backup:**
```bash
# Backup storage files
tar -czf storage_$(date +%Y%m%d).tar.gz storage/app/public/
```

---

## 🔐 Security Hardening

### 1. Firewall Setup

```bash
# Install UFW
sudo apt install ufw -y

# Allow SSH, HTTP, HTTPS
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

### 2. Fail2Ban Setup

```bash
# Install Fail2Ban
sudo apt install fail2ban -y

# Configure
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true
```

```bash
# Restart Fail2Ban
sudo systemctl restart fail2ban
```

### 3. Additional Security

```bash
# Disable directory listing in Nginx
# Already handled in nginx config

# Hide PHP version
sudo nano /etc/php/8.2/fpm/php.ini
# Set: expose_php = Off

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 🧪 Post-Deployment Testing

### Checklist
- [ ] Homepage loads correctly
- [ ] SSL certificate is valid
- [ ] User registration works
- [ ] Admin login works
- [ ] File uploads work
- [ ] Email notifications work
- [ ] KTA generation works
- [ ] QR code verification works
- [ ] Import/Export Excel works
- [ ] All routes accessible
- [ ] No 404 or 500 errors in logs

### Test Commands

```bash
# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Test email
php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('test@example.com')->subject('Test'); });"

# Check storage link
ls -la /var/www/kta/public/storage

# Test queue
php artisan queue:work --once
```

---

## 📞 Troubleshooting

### Common Issues

**Issue: 502 Bad Gateway**
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check Nginx error log
sudo tail -f /var/log/nginx/kta-error.log

# Restart services
sudo systemctl restart php8.2-fpm nginx
```

**Issue: Permission Denied**
```bash
# Fix permissions
sudo chown -R deploy:www-data /var/www/kta
sudo chmod -R 775 /var/www/kta/storage
sudo chmod -R 775 /var/www/kta/bootstrap/cache
```

**Issue: Database Connection Failed**
```bash
# Check MySQL status
sudo systemctl status mysql

# Test connection
mysql -u kta_user -p kta_production
```

**Issue: Email Not Sending**
```bash
# Check logs
tail -f /var/www/kta/storage/logs/laravel.log

# Test SMTP connection
telnet smtp.gmail.com 587
```

---

## 📊 Performance Optimization

### 1. Enable OPcache

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 2. Configure PHP-FPM

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
pm.max_requests = 500
```

### 3. Nginx Optimization

```nginx
# In nginx.conf
worker_processes auto;
worker_connections 1024;

# Enable gzip compression
gzip on;
gzip_types text/plain text/css application/json application/javascript;
gzip_min_length 1000;
```

---

## ✅ Deployment Checklist

**Pre-Deployment:**
- [ ] Backup current production data
- [ ] Test on staging environment
- [ ] Update documentation
- [ ] Inform users about maintenance

**Deployment:**
- [ ] Pull latest code
- [ ] Update dependencies
- [ ] Run migrations
- [ ] Clear and cache configs
- [ ] Restart services

**Post-Deployment:**
- [ ] Test critical features
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Update changelog

---

<div align="center">
🚀 Happy Deploying!
</div>
