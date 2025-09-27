# GovTribe Platform

A private, self-hosted platform for discovering, managing, and tracking federal contract opportunities. Built with PHP 8.2, MySQL 8, and Bootstrap 5 with strict no-fake data policies.

## Features

- **SAM.gov Integration**: Real-time opportunity ingestion with strict validation
- **Smart Scoring**: Automated opportunity scoring based on NAICS, set-asides, and preferences
- **Pipeline Management**: Kanban-style opportunity tracking through the entire lifecycle
- **Proposal Management**: Document management, BOM builder, and proposal packaging
- **Supplier Management**: RFQ creation, quote tracking, and supplier database
- **Award Tracking**: Contract awards, CLIN management, and invoice tracking
- **Role-Based Access**: Granular permissions for different user types
- **Audit Trail**: Complete activity logging and change tracking
- **Automated Jobs**: Scheduled ingestion, email digests, and system cleanup

## Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- Composer for dependencies
- SAM.gov API key

## Installation

### 1. Clone and Setup

```bash
git clone <repository-url> govtribe-platform
cd govtribe-platform
composer install --no-dev --optimize-autoloader
```

### 2. Environment Configuration

```bash
cp .env.example .env.php
```

Edit `.env.php` with your configuration:

```php
<?php
return [
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'govtribe_platform',
        'user' => 'your_db_user',
        'pass' => 'your_db_password',
    ],
    'sam' => [
        'api_key' => 'your_sam_api_key',
        'endpoint' => 'https://api.sam.gov/opportunities/v2/search',
        'use_alpha' => false,
    ],
    'app' => [
        'env' => 'production',
        'debug' => false,
        'timezone' => 'America/Los_Angeles',
        'url' => 'https://your-domain.com',
    ],
    // ... other configuration
];
```

### 3. Database Setup

```bash
mysql -u root -p < config/database.sql
```

### 4. File Permissions

```bash
mkdir -p storage/{logs,uploads,backups}
chmod -R 755 storage/
chown -R www-data:www-data storage/
```

### 5. Web Server Configuration

#### Apache (.htaccess in public/)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### 6. Cron Jobs

Add to your crontab:

```cron
# SAM.gov ingestion every 12 hours
0 */12 * * * /usr/bin/php /path/to/govtribe-platform/jobs/sam_ingest.php

# Daily email digest at 7:30 AM
30 7 * * * /usr/bin/php /path/to/govtribe-platform/jobs/email_digest.php

# Weekly cleanup on Sunday at 2:00 AM
0 2 * * 0 /usr/bin/php /path/to/govtribe-platform/jobs/cleanup.php
```

### 7. First Login

Default admin account:
- Email: `admin@example.com`
- Password: `admin123`

**⚠️ IMPORTANT**: Change this password immediately after first login!

## Configuration

### SAM.gov API Setup

1. Register at [SAM.gov](https://sam.gov/content/api)
2. Obtain your API key
3. Add the key to your settings via Admin → Settings
4. Test the connection using Admin → Data Ingestion → Test API

### Data Ingestion Policies

The system enforces strict "no-fake data" policies:

- **Date Validation**: `postedFrom`/`postedTo` are required and must be within 365 days
- **Procurement Types**: Only Solicitation (`o`) and Combined (`k`) types are allowed
- **NAICS Filtering**: Only whitelisted NAICS codes are accepted
- **Description Handling**: Missing descriptions are marked as such, never fabricated
- **Attachment Policy**: Only actual resourceLinks are processed, no phantom documents

### User Roles

- **Admin**: Full system access, settings, user management
- **Capture Manager**: Opportunity search, scoring, and pipeline management
- **Proposal Manager**: Proposal creation, document management, submissions
- **Sales Engineer**: BOM creation, pricing, quote management
- **Vendor Manager**: Supplier management, RFQ creation
- **Accountant**: Award tracking, invoice management
- **Viewer**: Read-only access for auditing

## Security Features

- **Password Security**: Argon2id hashing with configurable parameters
- **Session Management**: Secure sessions with regeneration and timeout
- **CSRF Protection**: All state-changing operations require CSRF tokens
- **Input Validation**: Comprehensive sanitization and validation
- **File Security**: SHA-256 fingerprinting, MIME validation, size limits
- **Audit Logging**: Complete activity tracking with IP addresses
- **Role-Based Access**: Server-side permission enforcement

## API Integration Details

### SAM.gov API v2 Requirements

The system strictly follows SAM.gov API requirements:

- **Mandatory Parameters**: `api_key`, `postedFrom`, `postedTo`
- **Date Format**: MM/dd/yyyy format required
- **Date Range Limit**: Maximum 365 days between dates
- **Procurement Types**: `ptype=o` (Solicitation) and `ptype=k` (Combined)
- **NAICS Codes**: `ncode` parameter for filtering
- **Rate Limiting**: Implements exponential backoff for 429/5xx responses
- **Error Handling**: Proper handling of authentication and API errors

### Data Validation Guards

1. **Title Presence**: Opportunities must have non-empty titles
2. **Valid Types**: Only allowed procurement types are accepted
3. **NAICS Whitelist**: NAICS codes must be in configured allowlist
4. **Active Status**: Only active opportunities are processed
5. **Deduplication**: Unique constraint on (source, external_id)
6. **Description Policy**: Missing descriptions are flagged, never fabricated
7. **Attachment Policy**: Only actual resourceLinks are processed

## Monitoring and Maintenance

### Health Checks

Access `/health` for system status:

```json
{
  "status": "ok",
  "timestamp": "2025-09-27T12:00:00-07:00",
  "checks": {
    "database": "ok",
    "files": "ok",
    "sam_api": "ok"
  }
}
```

### Log Files

- **Application**: `storage/logs/app.log`
- **Jobs**: `storage/logs/jobs.log`
- **Ingestion**: `storage/logs/ingestion.log`

### Backup Strategy

- **Database**: Automated nightly backups
- **Files**: Backup `storage/uploads` directory
- **Configuration**: Backup `.env.php` and custom configurations
- **Retention**: Configurable retention period (default: 30 days)

## Troubleshooting

### Common Issues

1. **SAM API Authentication Errors**
   - Verify API key in Admin → Settings
   - Check API key validity at sam.gov
   - Ensure proper date format (MM/dd/yyyy)

2. **File Upload Issues**
   - Check `storage/uploads` permissions
   - Verify `files_max_size` setting
   - Ensure disk space availability

3. **Email Issues**
   - Configure SMTP settings in Admin → Settings
   - Test with system notifications
   - Check firewall rules for SMTP ports

4. **Performance Issues**
   - Review MySQL slow query log
   - Check available disk space
   - Monitor PHP memory limits

### Support

For technical support:
1. Check system logs in `storage/logs/`
2. Review audit log in Admin → Audit Log
3. Verify system health at `/health`
4. Check cron job execution logs

## License

Proprietary - Internal Use Only

## Security Notice

This system handles sensitive government contracting data. Ensure proper security measures:

- Use HTTPS in production
- Regular security updates
- Strong password policies
- Network access controls
- Regular backups and testing
- Audit log monitoring