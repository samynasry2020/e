#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Setup Script for GovTribe Platform
 * 
 * This script initializes the database and creates the default admin user
 */

require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Models\User;
use App\Utils\Config;
use App\Utils\Database;
use App\Utils\Security;

echo "GovTribe Platform Setup\n";
echo "======================\n\n";

try {
    // Test database connection
    echo "Testing database connection...\n";
    if (!Database::testConnection()) {
        throw new Exception('Database connection failed. Please check your configuration.');
    }
    echo "✓ Database connection successful\n\n";

    // Create database tables
    echo "Creating database tables...\n";
    $schemaFile = dirname(__DIR__) . '/database/schema.sql';
    
    if (!file_exists($schemaFile)) {
        throw new Exception('Database schema file not found: ' . $schemaFile);
    }
    
    $schema = file_get_contents($schemaFile);
    $statements = explode(';', $schema);
    
    $created = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                Database::execute($statement);
                $created++;
            } catch (Exception $e) {
                // Ignore "table already exists" errors
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "✓ Database tables created/verified\n\n";

    // Check if admin user exists
    echo "Checking for admin user...\n";
    $adminUser = User::findByEmail('admin@govtribe.local');
    
    if (!$adminUser) {
        echo "Creating default admin user...\n";
        
        // Create admin user with default password
        $adminUser = User::create([
            'email' => 'admin@govtribe.local',
            'password' => 'admin123', // CHANGE THIS IMMEDIATELY!
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        echo "✓ Admin user created\n";
        echo "  Email: admin@govtribe.local\n";
        echo "  Password: admin123 (CHANGE IMMEDIATELY!)\n\n";
    } else {
        echo "✓ Admin user already exists\n\n";
    }

    // Create upload directories
    echo "Creating upload directories...\n";
    $uploadPath = Config::get('upload.path', '/workspace/uploads');
    $directories = [
        $uploadPath,
        $uploadPath . '/attachments',
        $uploadPath . '/proposals',
        $uploadPath . '/documents',
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "✓ Created directory: {$dir}\n";
            } else {
                echo "✗ Failed to create directory: {$dir}\n";
            }
        }
    }
    
    // Create storage directories
    $storagePath = dirname(__DIR__) . '/storage';
    $storageDirs = [
        $storagePath . '/logs',
        $storagePath . '/cache',
        $storagePath . '/backups',
    ];
    
    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "✓ Created directory: {$dir}\n";
            } else {
                echo "✗ Failed to create directory: {$dir}\n";
            }
        }
    }
    
    echo "\n";

    // Test SAM API connection (if configured)
    echo "Testing SAM API connection...\n";
    $apiKey = Config::get('sam.api_key', '');
    
    if (empty($apiKey)) {
        echo "⚠ SAM API key not configured. Please set SAM_API_KEY in your .env file.\n\n";
    } else {
        try {
            $samApi = new \App\Services\SamApiService();
            $testResult = $samApi->testConnection();
            
            if ($testResult['success']) {
                echo "✓ SAM API connection successful\n";
            } else {
                echo "✗ SAM API connection failed: " . $testResult['message'] . "\n";
            }
        } catch (Exception $e) {
            echo "✗ SAM API test failed: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    // Display next steps
    echo "Setup completed successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Change the default admin password immediately\n";
    echo "2. Configure your SAM.gov API key in the admin panel\n";
    echo "3. Set up your preferred NAICS codes and agencies\n";
    echo "4. Configure email settings for digest notifications\n";
    echo "5. Set up cron jobs for automated ingestion:\n";
    echo "   - SAM ingestion: 0 */12 * * * /usr/bin/php " . dirname(__DIR__) . "/jobs/sam_ingest.php\n";
    echo "   - Email digest: 30 7 * * * /usr/bin/php " . dirname(__DIR__) . "/jobs/email_digest.php\n";
    echo "   - System cleanup: 0 2 * * 0 /usr/bin/php " . dirname(__DIR__) . "/jobs/cleanup.php\n\n";
    
    echo "You can now access the platform at: " . Config::get('app.url', 'http://localhost:8080') . "\n";

} catch (Exception $e) {
    echo "✗ Setup failed: " . $e->getMessage() . "\n";
    echo "Please check your configuration and try again.\n";
    exit(1);
}