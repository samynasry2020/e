# GovTribe Platform - Deployment Guide

## Quick Deployment

### 1. System Requirements
- **PHP 8.2+** with extensions: pdo_mysql, curl, json, mbstring
- **MySQL 8.0+** or MariaDB 10.4+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Disk Space**: Minimum 2GB for files and logs
- **Memory**: 512MB RAM minimum, 2GB recommended

### 2. Installation Steps

```bash
# 1. Clone or copy the project files
# 2. Set up web server to point to public/ directory
# 3. Configure environment
cp .env.example .env
# Edit .env with your settings

# 4. Run setup script
php scripts/setup.php

# 5. Set up cron jobs (as root or with sudo)
crontab -e
# Add these lines:
0 */12 * * * /usr/bin/php /path/to/govtribe-platform/jobs/sam_ingest.php
30 7 * * * /usr/bin/php /path/to/govtribe-platform/jobs/email_digest.php
0 2 * * 0 /usr/bin/php /path/to/govtribe-platform/jobs/cleanup.php
```

### 3. Web Server Configuration

#### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName govtribe.yourdomain.com
    DocumentRoot /path/to/govtribe-platform/public
    
    <Directory /path/to/govtribe-platform/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security - deny access to sensitive directories
    <DirectoryMatch "(storage|database|scripts|jobs)">
        Require all denied
    </DirectoryMatch>
    
    ErrorLog ${APACHE_LOG_DIR}/govtribe_error.log
    CustomLog ${APACHE_LOG_DIR}/govtribe_access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name govtribe.yourdomain.com;
    root /path/to/govtribe-platform/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security
    location ~ /\.(env|git) {
        deny all;
    }
    
    location ~ ^/(storage|database|scripts|jobs)/ {
        deny all;
    }
}
```

### 4. SSL Certificate (Recommended)
```bash
# Using Let's Encrypt
sudo certbot --apache -d govtribe.yourdomain.com
# or for Nginx
sudo certbot --nginx -d govtribe.yourdomain.com
```

### 5. Database Setup

```sql
-- Create database and user
CREATE DATABASE govtribe_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'govtribe_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON govtribe_platform.* TO 'govtribe_user'@'localhost';
FLUSH PRIVILEGES;
```

### 6. Environment Configuration

Key settings in `.env`:
```env
# Database
DB_HOST=localhost
DB_NAME=govtribe_platform
DB_USER=govtribe_user
DB_PASS=your_secure_password

# SAM API (CRITICAL)
SAM_API_KEY=your_sam_gov_api_key
SAM_API_ENVIRONMENT=prod

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://govtribe.yourdomain.com

# Security
SESSION_LIFETIME=7200
LOGIN_MAX_ATTEMPTS=5

# Email (for digests)
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
```

## Post-Deployment Checklist

### Security
- [ ] Change default admin password
- [ ] Configure SAM.gov API key
- [ ] Set up SSL certificate
- [ ] Configure firewall rules
- [ ] Set up regular backups
- [ ] Configure email notifications

### Functionality
- [ ] Test SAM API connection
- [ ] Run manual sync to verify ingestion
- [ ] Test user login/logout
- [ ] Verify file uploads work
- [ ] Check email digest functionality
- [ ] Test all user roles

### Monitoring
- [ ] Set up log monitoring
- [ ] Configure health check endpoint
- [ ] Set up database monitoring
- [ ] Monitor disk space usage
- [ ] Set up backup verification

## Maintenance

### Regular Tasks
- **Daily**: Check logs for errors
- **Weekly**: Verify backup integrity
- **Monthly**: Review user access and permissions
- **Quarterly**: Update SAM API key if needed

### Backup Strategy
```bash
# Database backup script
#!/bin/bash
mysqldump -u govtribe_user -p govtribe_platform > /path/to/backups/db_$(date +%Y%m%d).sql
tar -czf /path/to/backups/files_$(date +%Y%m%d).tar.gz /path/to/govtribe-platform/uploads/

# Keep last 30 days
find /path/to/backups -name "*.sql" -mtime +30 -delete
find /path/to/backups -name "*.tar.gz" -mtime +30 -delete
```

### Performance Optimization
- Enable PHP OPcache
- Configure MySQL query cache
- Set up Redis for session storage (optional)
- Monitor and optimize slow queries

## Troubleshooting

### Common Issues

**SAM API Connection Failed**
- Verify API key is correct and active
- Check date range parameters (max 365 days)
- Ensure NAICS codes are in allowlist
- Check firewall rules for outbound HTTPS

**Database Connection Issues**
- Verify credentials in .env file
- Check MySQL service status
- Confirm database exists and user has permissions
- Check network connectivity

**File Upload Problems**
- Verify directory permissions (755 for directories, 644 for files)
- Check upload size limits in PHP configuration
- Confirm MIME type allowlist
- Check disk space availability

**Email Digest Not Working**
- Verify SMTP credentials
- Check firewall rules for SMTP ports
- Confirm email addresses are valid
- Check spam folders for test emails

### Log Locations
- Application logs: `/storage/logs/app-YYYY-MM-DD.log`
- Web server logs: `/var/log/apache2/` or `/var/log/nginx/`
- PHP logs: `/var/log/php8.2-fpm.log`
- MySQL logs: `/var/log/mysql/error.log`

### Health Check
Access `/health.php` to verify system status:
```bash
curl https://govtribe.yourdomain.com/health.php
```

Expected response:
```json
{
    "status": "ok",
    "timestamp": "2025-01-27T10:30:00+00:00",
    "version": "1.0.0",
    "checks": {
        "database": {"status": "ok", "message": "Connected"},
        "sam_api": {"status": "ok", "message": "API connection successful"},
        "filesystem": {"status": "ok", "upload_path_writable": true, "log_path_writable": true}
    }
}
```

## Support

For technical support:
1. Check the logs first
2. Verify configuration settings
3. Test individual components
4. Contact your system administrator

Remember: This is a private system with no external support channels.