# GovTribe Platform Deployment Guide

## Production Deployment Checklist

### Pre-Deployment

- [ ] SAM.gov API key obtained and tested
- [ ] MySQL 8.0+ server configured
- [ ] PHP 8.2+ with required extensions installed
- [ ] SSL certificate configured
- [ ] Backup strategy implemented
- [ ] Monitoring tools configured

### Server Requirements

**Minimum Specifications:**
- 2 vCPU cores
- 4 GB RAM
- 20 GB storage (plus growth for files)
- PHP 8.2+ with extensions: pdo, curl, json, mbstring, zip
- MySQL 8.0+
- Web server (Apache/Nginx)

**Recommended Specifications:**
- 4 vCPU cores
- 8 GB RAM
- 100 GB SSD storage
- Load balancer for high availability

### Step-by-Step Deployment

#### 1. Server Preparation (Ubuntu 22.04)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-json \
    php8.2-mbstring php8.2-zip php8.2-xml php8.2-gd -y

# Install MySQL 8.0
sudo apt install mysql-server-8.0 -y
sudo mysql_secure_installation

# Install Nginx
sudo apt install nginx -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for any frontend builds)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

#### 2. Application Setup

```bash
# Create application directory
sudo mkdir -p /var/www/govtribe-platform
cd /var/www/govtribe-platform

# Clone application (replace with your repository)
sudo git clone <repository-url> .

# Install dependencies
sudo composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data /var/www/govtribe-platform
sudo chmod -R 755 /var/www/govtribe-platform
sudo chmod -R 775 storage/
sudo chmod +x jobs/*.php
```

#### 3. Database Configuration

```bash
# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE govtribe_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'govtribe_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON govtribe_platform.* TO 'govtribe_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Import database schema
mysql -u govtribe_user -p govtribe_platform < config/database.sql
```

#### 4. Environment Configuration

```bash
# Copy environment template
sudo cp .env.example .env.php

# Edit configuration
sudo nano .env.php
```

**Production .env.php example:**

```php
<?php
return [
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'govtribe_platform',
        'user' => 'govtribe_user',
        'pass' => 'your_secure_database_password',
    ],
    'sam' => [
        'api_key' => 'your_sam_api_key_here',
        'endpoint' => 'https://api.sam.gov/opportunities/v2/search',
        'use_alpha' => false,
    ],
    'app' => [
        'env' => 'production',
        'debug' => false,
        'timezone' => 'America/Los_Angeles',
        'url' => 'https://your-domain.com',
    ],
    'security' => [
        'session_lifetime' => 7200,
        'csrf_token_name' => 'csrf_token',
        'password_hash_algo' => 'PASSWORD_ARGON2ID',
    ],
    'files' => [
        'root' => '/var/www/govtribe-platform/storage/uploads',
        'max_size' => 104857600, // 100MB
        'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
    ],
    'email' => [
        'host' => 'smtp.your-domain.com',
        'port' => 587,
        'username' => 'noreply@your-domain.com',
        'password' => 'your_smtp_password',
        'from_email' => 'noreply@your-domain.com',
        'from_name' => 'GovTribe Platform',
    ],
];
```

#### 5. Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/govtribe-platform
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    
    root /var/www/govtribe-platform/public;
    index index.php;
    
    # SSL Configuration
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # File upload size
    client_max_body_size 100M;
    
    # Main location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security
        fastcgi_param PHP_VALUE "expose_php=off";
        fastcgi_read_timeout 300;
    }
    
    # Deny access to sensitive files
    location ~ /\.(env|git|svn) {
        deny all;
        return 404;
    }
    
    location ~ /(storage|config|src|jobs|tests|vendor)/ {
        deny all;
        return 404;
    }
    
    # Static file caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }
    
    # Logs
    access_log /var/log/nginx/govtribe-access.log;
    error_log /var/log/nginx/govtribe-error.log;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/govtribe-platform /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 6. PHP-FPM Configuration

```bash
sudo nano /etc/php/8.2/fpm/pool.d/govtribe.conf
```

```ini
[govtribe]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm-govtribe.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

; Security
php_admin_value[expose_php] = off
php_admin_value[allow_url_fopen] = off
php_admin_value[allow_url_include] = off

; Performance
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 300
php_admin_value[upload_max_filesize] = 100M
php_admin_value[post_max_size] = 100M
```

```bash
sudo systemctl restart php8.2-fpm
```

#### 7. Cron Jobs

```bash
sudo crontab -e
```

```cron
# GovTribe Platform Jobs
# SAM.gov ingestion every 12 hours
0 */12 * * * /usr/bin/php /var/www/govtribe-platform/jobs/sam_ingest.php >> /var/log/govtribe-cron.log 2>&1

# Daily email digest at 7:30 AM
30 7 * * * /usr/bin/php /var/www/govtribe-platform/jobs/email_digest.php >> /var/log/govtribe-cron.log 2>&1

# Weekly cleanup on Sunday at 2:00 AM
0 2 * * 0 /usr/bin/php /var/www/govtribe-platform/jobs/cleanup.php >> /var/log/govtribe-cron.log 2>&1

# Database backup daily at 1:00 AM
0 1 * * * /usr/local/bin/backup-govtribe.sh >> /var/log/govtribe-backup.log 2>&1
```

#### 8. Backup Script

```bash
sudo nano /usr/local/bin/backup-govtribe.sh
```

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/govtribe"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="govtribe_platform"
DB_USER="govtribe_user"
DB_PASS="your_database_password"
APP_DIR="/var/www/govtribe-platform"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage/uploads

# Configuration backup
tar -czf $BACKUP_DIR/config_$DATE.tar.gz -C $APP_DIR .env.php

# Remove old backups (keep 30 days)
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-govtribe.sh
```

#### 9. Monitoring Setup

```bash
# Install monitoring tools
sudo apt install htop iotop nethogs -y

# Setup log rotation
sudo nano /etc/logrotate.d/govtribe-platform
```

```
/var/www/govtribe-platform/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
```

#### 10. Security Hardening

```bash
# Install fail2ban
sudo apt install fail2ban -y

# Configure fail2ban for Nginx
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/govtribe-error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/govtribe-error.log
maxretry = 10

[php-url-fopen]
enabled = true
port = http,https
logpath = /var/log/nginx/govtribe-error.log
maxretry = 3
```

```bash
sudo systemctl enable fail2ban
sudo systemctl start fail2ban

# Setup firewall
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

### Post-Deployment

#### 1. Initial Setup

1. Access `https://your-domain.com/login`
2. Login with default admin credentials:
   - Email: `admin@example.com`
   - Password: `admin123`
3. **IMMEDIATELY** change the admin password
4. Go to Admin → Settings and configure:
   - SAM.gov API key
   - NAICS codes
   - Email settings
   - Preferred agencies

#### 2. Testing

```bash
# Test API connection
curl -k https://your-domain.com/health

# Test SAM API (after configuration)
# Login as admin and go to Admin → Data Ingestion → Test API

# Test cron jobs
sudo -u www-data php /var/www/govtribe-platform/jobs/sam_ingest.php --dry-run
```

#### 3. Monitoring

- Monitor `/var/log/nginx/govtribe-error.log`
- Check `/var/www/govtribe-platform/storage/logs/`
- Monitor disk space and database growth
- Set up external monitoring for uptime
- Configure alerts for failed ingestion jobs

### Maintenance

#### Regular Tasks

- **Daily**: Check error logs, verify backups
- **Weekly**: Review audit logs, update system packages
- **Monthly**: Review user accounts, clean old data
- **Quarterly**: Security audit, performance review

#### Updates

```bash
# Application updates
cd /var/www/govtribe-platform
sudo git pull
sudo composer install --no-dev --optimize-autoloader
sudo chown -R www-data:www-data .

# System updates
sudo apt update && sudo apt upgrade -y
sudo systemctl restart nginx php8.2-fpm
```

### Troubleshooting

#### Common Issues

1. **502 Bad Gateway**
   - Check PHP-FPM status: `sudo systemctl status php8.2-fpm`
   - Check socket permissions: `ls -la /var/run/php/`

2. **Database Connection Issues**
   - Verify MySQL is running: `sudo systemctl status mysql`
   - Test connection: `mysql -u govtribe_user -p govtribe_platform`

3. **File Upload Issues**
   - Check directory permissions: `ls -la storage/uploads/`
   - Verify PHP settings: `php -i | grep upload_max_filesize`

4. **SAM API Issues**
   - Test API key at sam.gov
   - Check firewall rules for outbound HTTPS
   - Review ingestion logs

#### Emergency Procedures

1. **Site Down**
   - Check Nginx: `sudo systemctl status nginx`
   - Check PHP-FPM: `sudo systemctl status php8.2-fpm`
   - Check disk space: `df -h`

2. **Database Issues**
   - Check MySQL: `sudo systemctl status mysql`
   - Review MySQL logs: `sudo tail -f /var/log/mysql/error.log`

3. **Restore from Backup**
   ```bash
   # Database restore
   gunzip < /var/backups/govtribe/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u govtribe_user -p govtribe_platform
   
   # Files restore
   cd /var/www/govtribe-platform
   tar -xzf /var/backups/govtribe/files_YYYYMMDD_HHMMSS.tar.gz
   ```

### Support Contacts

- **System Administrator**: [admin@your-domain.com]
- **Technical Support**: [support@your-domain.com]
- **Emergency Contact**: [emergency@your-domain.com]