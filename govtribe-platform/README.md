# GovTribe Platform

A private, in-house system for discovering, filtering, scoring, tracking, and packaging federal contract opportunities. Built with pure PHP 8.2 + MySQL 8 + Bootstrap 5, no frameworks, no SaaS, zero vendor lock-in.

## Features

- **Real Data Only**: Strict validation ensures only real, relevant opportunities are stored
- **End-to-End Flow**: Ingest → Triage/Score → Manage Documents → RFQs/Quotes → BOM/CLIN → Proposal Package → Submission Tracking → Award & Invoice Management
- **Hard Guards**: No "fake" data, strict validation, comprehensive audit logging
- **RBAC Security**: Role-based access control with CSRF protection and Argon2id password hashing
- **SAM.gov Integration**: Official API v2 with precise parameters and error handling

## Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- cURL, PDO, JSON, OpenSSL extensions

## Installation

### 1. Clone and Setup

```bash
cd /var/www/html
git clone <repository-url> govtribe-platform
cd govtribe-platform
```

### 2. Database Setup

```bash
# Create database and user
mysql -u root -p
CREATE DATABASE govtribe_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'govtribe'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON govtribe_platform.* TO 'govtribe'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u govtribe -p govtribe_platform < config/database.sql
```

### 3. Environment Configuration

```bash
# Copy environment template
cp .env.example .env

# Edit configuration
nano .env
```

Update the following values in `.env`:

```env
# Database
DB_HOST=localhost
DB_NAME=govtribe_platform
DB_USERNAME=govtribe
DB_PASSWORD=secure_password

# SAM.gov API
SAM_API_KEY=your_sam_api_key_here

# Security (generate new values)
APP_KEY=your_32_character_secret_key_here
SESSION_ENCRYPTION_KEY=your_32_character_session_key_here

# Email (optional)
SMTP_HOST=smtp.yourdomain.com
SMTP_USERNAME=your_email@domain.com
SMTP_PASSWORD=your_email_password
```

### 4. File Permissions

```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 public/
chown -R www-data:www-data storage/
chown -R www-data:www-data public/
```

### 5. Web Server Configuration

#### Apache (.htaccess)

Create `public/.htaccess`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/govtribe-platform/public;
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

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
}
```

### 6. SAM.gov API Key

1. Register at [SAM.gov](https://sam.gov)
2. Navigate to Entity Management → API Access
3. Generate an API key
4. Add the key to your `.env` file

### 7. Cron Jobs

```bash
# Edit crontab
crontab -e

# Add these lines:
# SAM ingestion every 12 hours
0 */12 * * * cd /var/www/html/govtribe-platform && php jobs/sam_ingest.php >> storage/logs/cron.log 2>&1

# Email digest daily at 7:30 AM
30 7 * * * cd /var/www/html/govtribe-platform && php jobs/email_digest.php >> storage/logs/cron.log 2>&1

# Cleanup weekly on Sunday at 2 AM
0 2 * * 0 cd /var/www/html/govtribe-platform && php jobs/cleanup.php >> storage/logs/cron.log 2>&1
```

### 8. Initial Setup

1. Access your application at `http://your-domain.com`
2. Login with default admin credentials:
   - Email: `admin@govtribe.local`
   - Password: `admin123` (CHANGE IMMEDIATELY!)
3. Go to Admin → Settings to configure:
   - SAM API settings
   - NAICS codes
   - Set-aside preferences
   - Email configuration

## Usage

### First Time Setup

1. **Configure API Settings**: Admin → Settings → SAM API
   - Add your SAM.gov API key
   - Set preferred NAICS codes (default: 334111, 541512)
   - Configure set-aside preferences (WOSB, EDWOSB)

2. **Run Initial Sync**: Admin → Ingestion → Sync Now
   - This will fetch recent opportunities from SAM.gov
   - Only valid opportunities meeting your criteria will be stored

3. **Review Opportunities**: Opportunities → Browse
   - Opportunities are automatically scored (0-100)
   - Use filters to find high-priority items

### Daily Workflow

1. **Check Dashboard**: Review new opportunities and due dates
2. **Score Opportunities**: Update scores based on business criteria
3. **Move Through Pipeline**: New → Review → Pursue → No-Bid/Awarded/Lost
4. **Manage Documents**: Upload RFPs, compliance matrices, proposals
5. **Track Submissions**: Log submission confirmations and evidence

### Roles & Permissions

- **Admin**: Full access, user management, system configuration
- **Capture Manager**: Opportunity management, pipeline control, scoring
- **Proposal Manager**: Document management, compliance tracking
- **Sales Engineer**: BOM/CLIN management, pricing
- **Vendor Manager**: Supplier management, RFQ tracking
- **Accountant**: Award tracking, invoice management
- **Viewer**: Read-only access

## Security Features

- **Authentication**: Argon2id password hashing, session management
- **CSRF Protection**: All forms protected with tokens
- **XSS Prevention**: Input sanitization and output escaping
- **SQL Injection Prevention**: PDO prepared statements only
- **File Security**: SHA-256 verification, MIME type validation
- **Audit Logging**: Complete trail of all system changes
- **Rate Limiting**: Login attempt protection

## Data Integrity

The system implements strict "hard guards" per the PRD:

1. **Date Validation**: Only opportunities within 365 days
2. **Type Filtering**: Only Solicitation and Combined notices
3. **NAICS Validation**: Only whitelisted NAICS codes
4. **Set-Aside Filtering**: Optional WOSB/EDWOSB filtering
5. **Active Status**: Rejects archived opportunities
6. **Deduplication**: Unique constraint on source + external_id
7. **No Fake Data**: Never creates placeholder descriptions or phantom files

## API Integration

### SAM.gov API v2

- **Base URL**: `https://api.sam.gov/opportunities/v2/search`
- **Authentication**: API key in query parameter
- **Required Parameters**: `postedFrom`, `postedTo` (max 365 days)
- **Filters**: `ptype` (o,k), `ncode` (NAICS), `typeOfSetAside`
- **Error Handling**: Exponential backoff, retry logic
- **Rate Limiting**: Respects API limits

## Backup & Maintenance

### Automated Backups

The system includes automated maintenance:

- **Daily**: Database backup to `storage/backups/`
- **Weekly**: Log rotation and cleanup
- **Monthly**: Archive old completed opportunities

### Manual Backup

```bash
# Database backup
mysqldump -u govtribe -p govtribe_platform > backup_$(date +%Y%m%d).sql

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz storage/uploads/
```

## Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Verify SAM API key in Admin → Settings
   - Check API key permissions on SAM.gov
   - Test connection using Admin → Ingestion → Test Connection

2. **No Opportunities Found**
   - Check NAICS code filters
   - Verify date range settings
   - Ensure API key has proper permissions

3. **Login Issues**
   - Clear browser cache and cookies
   - Check session configuration
   - Verify database connection

4. **File Upload Errors**
   - Check file permissions on `storage/uploads/`
   - Verify PHP upload limits
   - Check file size and type restrictions

### Logs

Check logs in `storage/logs/`:
- `app.log`: Application logs
- `cron.log`: Scheduled job logs

### Health Check

Visit `/health` endpoint to check system status.

## Development

### Code Structure

```
src/
├── Controllers/     # HTTP request handlers
├── Services/        # Business logic
├── Models/          # Data models
├── Repositories/    # Data access layer
├── Utils/           # Utility classes
└── Views/           # Templates

jobs/                # CLI scripts
config/              # Configuration files
storage/             # File storage and logs
public/              # Web root
```

### Adding New Features

1. Create model in `src/Models/`
2. Add service logic in `src/Services/`
3. Create controller in `src/Controllers/`
4. Add routes in `config/routes.php`
5. Create views in `src/Views/`

## License

Proprietary - All rights reserved.

## Support

For technical support, contact your system administrator.