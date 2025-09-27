# GovTribe Platform - Private Contract Management System

A comprehensive, from-scratch PHP platform for managing federal contract opportunities, built with strict validation and no external dependencies beyond official government APIs.

## Features

### Core Functionality
- **Real-time Opportunity Ingestion**: Automated fetching from SAM.gov API with strict validation
- **Advanced Filtering & Scoring**: Intelligent opportunity scoring based on NAICS codes, set-asides, and agency preferences
- **Role-Based Access Control**: Comprehensive RBAC system with 7 distinct user roles
- **Complete Pipeline Management**: From opportunity discovery to proposal submission and award tracking

### Security & Compliance
- **No Fake Data Policy**: Strict validation guards prevent storage of invalid or placeholder data
- **CSRF Protection**: All forms protected with CSRF tokens
- **Password Security**: Argon2ID hashing with configurable parameters
- **Session Management**: Secure session handling with database storage
- **Audit Logging**: Comprehensive audit trail for all system changes

### Technical Architecture
- **Pure PHP 8.2**: No frameworks, minimal dependencies
- **MySQL 8**: Optimized database schema with proper indexing
- **Bootstrap 5**: Modern, responsive user interface
- **RESTful API**: Clean API endpoints for integration
- **Background Jobs**: Automated ingestion, email digests, and cleanup

## Installation

### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- cURL extension
- PDO MySQL extension

### Quick Start

1. **Clone and Setup**
   ```bash
   git clone <repository-url>
   cd govtribe-platform
   cp .env.example .env
   ```

2. **Configure Environment**
   Edit `.env` file with your database credentials and SAM API key:
   ```env
   DB_HOST=localhost
   DB_NAME=govtribe_platform
   DB_USER=your_username
   DB_PASS=your_password
   SAM_API_KEY=your_sam_api_key
   ```

3. **Run Setup Script**
   ```bash
   php scripts/setup.php
   ```

4. **Configure Web Server**
   Point your web server document root to the `public/` directory.

5. **Access the Platform**
   Navigate to your configured URL and login with:
   - Email: `admin@govtribe.local`
   - Password: `admin123` (CHANGE IMMEDIATELY!)

## Configuration

### SAM.gov API Setup
1. Obtain your API key from [SAM.gov](https://sam.gov)
2. Configure in admin panel or `.env` file
3. Test connection in admin dashboard

### User Roles & Permissions
- **Admin**: Full system access, user management, settings
- **Capture Manager**: Opportunity management, scoring, pipeline control
- **Proposal Manager**: Proposal creation, compliance tracking
- **Sales Engineer**: BOM building, pricing, technical proposals
- **Vendor Manager**: Supplier management, RFQ tracking
- **Accountant**: Award tracking, invoice management
- **Viewer**: Read-only access to all data

### Automated Jobs
Set up these cron jobs for optimal operation:

```bash
# SAM.gov ingestion (every 12 hours)
0 */12 * * * /usr/bin/php /path/to/jobs/sam_ingest.php

# Daily email digest (7:30 AM)
30 7 * * * /usr/bin/php /path/to/jobs/email_digest.php

# Weekly cleanup (Sundays 2:00 AM)
0 2 * * 0 /usr/bin/php /path/to/jobs/cleanup.php
```

## API Reference

### SAM.gov Integration
The platform uses the official SAM.gov Opportunities API v2 with strict validation:

- **Base URL**: `https://api.sam.gov/opportunities/v2/search`
- **Authentication**: API key via query parameter
- **Rate Limiting**: Built-in exponential backoff
- **Validation**: Hard guards prevent invalid data storage

### Internal API Endpoints
- `GET /api/opportunities` - List opportunities with filtering
- `POST /api/opportunities/sync` - Trigger manual sync

## Database Schema

### Core Tables
- `opportunities` - Main opportunity records
- `agencies` - Government agencies
- `users` - User accounts and roles
- `files` - File storage with deduplication
- `documents` - Opportunity attachments

### Pipeline Tables
- `proposals` - Proposal versions and packages
- `boms` - Bill of materials
- `suppliers` - Vendor directory
- `awards` - Contract awards
- `invoices` - Invoice tracking

## Security Features

### Data Validation
- **Hard Guards**: Strict validation prevents fake data storage
- **Date Validation**: Enforces 365-day API limit
- **NAICS Filtering**: Only allowed codes accepted
- **Procurement Type**: Limited to Solicitation and Combined only

### Authentication
- **Password Hashing**: Argon2ID with secure parameters
- **Session Security**: Database-stored sessions with IP tracking
- **Login Protection**: Brute force prevention with lockouts
- **MFA Support**: Optional two-factor authentication

### File Security
- **SHA-256 Deduplication**: Prevents duplicate file storage
- **MIME Validation**: Strict file type checking
- **Size Limits**: Configurable upload restrictions
- **Virus Scanning**: Hook for external scanning services

## Development

### Project Structure
```
├── public/                 # Web-accessible files
│   └── index.php          # Main entry point
├── src/                   # Application source code
│   ├── Controllers/       # Request handlers
│   ├── Models/           # Data models
│   ├── Services/         # Business logic
│   └── Utils/            # Utility classes
├── views/                # PHP templates
├── jobs/                 # Background scripts
├── scripts/              # Setup and maintenance
├── database/             # SQL schema files
└── storage/              # Logs and cache
```

### Adding New Features
1. Create model in `src/Models/`
2. Add controller in `src/Controllers/`
3. Create views in `views/`
4. Update routes in `public/index.php`
5. Add database migrations as needed

## Monitoring & Maintenance

### Logging
- **Application Logs**: `/storage/logs/app-YYYY-MM-DD.log`
- **Ingestion Logs**: Detailed API interaction logs
- **Error Tracking**: Comprehensive error logging with stack traces

### Health Checks
- Database connectivity monitoring
- SAM API status checking
- File system health verification
- User session cleanup

### Backup Strategy
- **Database Backups**: Automated nightly backups
- **File Backups**: Upload directory synchronization
- **Retention**: Configurable retention periods
- **Restore**: Simple restore procedures

## Troubleshooting

### Common Issues

**SAM API Errors**
- Verify API key is correct and active
- Check date range (max 365 days)
- Ensure NAICS codes are in allowlist

**Database Connection**
- Verify credentials in `.env` file
- Check MySQL service status
- Confirm database exists

**File Upload Issues**
- Check directory permissions
- Verify upload size limits
- Confirm MIME type allowlist

### Support
For technical support or feature requests, please contact your system administrator.

## License

This is a private, internal system. All rights reserved.

## Version History

- **v1.0.0** - Initial release with core functionality
- Complete SAM.gov integration
- Role-based access control
- Automated ingestion pipeline
- Comprehensive audit logging