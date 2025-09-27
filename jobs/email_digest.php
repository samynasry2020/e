#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Email Digest Job
 * 
 * This script runs daily at 7:30 AM to send opportunity digest emails
 * Run via cron: 30 7 * * * /usr/bin/php /path/to/jobs/email_digest.php
 */

// Set up environment
require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Models\Opportunity;
use App\Models\User;
use App\Utils\Config;
use App\Utils\Database;
use App\Utils\Logger;

// Set timezone
date_default_timezone_set(Config::get('app.timezone', 'America/Los_Angeles'));

Logger::info('Starting email digest job');

try {
    // Get digest settings
    $digestSettings = $this->getDigestSettings();
    
    if (empty($digestSettings['recipients'])) {
        Logger::info('No digest recipients configured, skipping');
        exit(0);
    }
    
    // Get new opportunities from last 24 hours
    $opportunities = Opportunity::getFiltered([
        'active' => true,
        'posted_after' => date('Y-m-d H:i:s', strtotime('-24 hours')),
    ], 50, 0);
    
    // Get opportunities due soon (next 7 days)
    $dueSoon = Opportunity::getFiltered([
        'active' => true,
        'due_before' => date('Y-m-d H:i:s', strtotime('+7 days')),
        'due_after' => date('Y-m-d H:i:s'),
    ], 20, 0);
    
    // Get high-score opportunities
    $highScore = Opportunity::getFiltered([
        'active' => true,
        'score_min' => 70,
        'status' => 'New',
    ], 10, 0);
    
    // Generate digest content
    $digestContent = $this->generateDigestContent($opportunities, $dueSoon, $highScore);
    
    // Send emails
    $sentCount = 0;
    foreach ($digestSettings['recipients'] as $email) {
        if ($this->sendDigestEmail($email, $digestContent)) {
            $sentCount++;
        }
    }
    
    Logger::info('Email digest job completed', [
        'recipients_count' => count($digestSettings['recipients']),
        'sent_count' => $sentCount,
        'new_opportunities' => count($opportunities),
        'due_soon' => count($dueSoon),
        'high_score' => count($highScore)
    ]);
    
    echo "Digest sent to {$sentCount} recipients\n";
    echo "New opportunities: " . count($opportunities) . "\n";
    echo "Due soon: " . count($dueSoon) . "\n";
    echo "High score: " . count($highScore) . "\n";
    
} catch (Exception $e) {
    Logger::error('Email digest job failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    echo "Digest job failed: " . $e->getMessage() . "\n";
    exit(1);
}

Logger::info('Email digest job finished');

/**
 * Get digest settings from database
 */
function getDigestSettings(): array
{
    $settings = Database::queryOne(
        'SELECT value FROM settings WHERE `key` = ?',
        ['email_digest_recipients']
    );
    
    $recipients = [];
    if ($settings) {
        $recipients = json_decode($settings['value'], true) ?: [];
    }
    
    return [
        'recipients' => $recipients,
        'frequency' => 'daily',
        'time' => '07:30'
    ];
}

/**
 * Generate digest email content
 */
function generateDigestContent(array $newOpportunities, array $dueSoon, array $highScore): string
{
    $appName = Config::get('app.name', 'GovTribe Platform');
    $date = date('l, F j, Y');
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Opportunity Digest - {$date}</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #0d6efd; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .section { margin-bottom: 30px; }
            .section h2 { color: #0d6efd; border-bottom: 2px solid #0d6efd; padding-bottom: 5px; }
            .opportunity { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
            .opportunity h3 { margin-top: 0; color: #333; }
            .meta { color: #666; font-size: 0.9em; }
            .score { display: inline-block; padding: 2px 8px; border-radius: 12px; color: white; font-size: 0.8em; }
            .score-high { background-color: #198754; }
            .score-medium { background-color: #ffc107; color: #000; }
            .score-low { background-color: #dc3545; }
            .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 0.9em; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>{$appName}</h1>
            <h2>Opportunity Digest - {$date}</h2>
        </div>
        
        <div class='content'>
    ";
    
    // New opportunities section
    if (!empty($newOpportunities)) {
        $html .= "
            <div class='section'>
                <h2>New Opportunities (Last 24 Hours)</h2>
        ";
        
        foreach ($newOpportunities as $opportunity) {
            $scoreClass = 'score-low';
            if ($opportunity->score >= 70) $scoreClass = 'score-high';
            elseif ($opportunity->score >= 40) $scoreClass = 'score-medium';
            
            $dueDate = $opportunity->due_at ? date('M j, Y', strtotime($opportunity->due_at)) : 'No due date';
            
            $html .= "
                <div class='opportunity'>
                    <h3>" . htmlspecialchars($opportunity->title) . "</h3>
                    <div class='meta'>
                        <strong>Agency:</strong> " . htmlspecialchars($opportunity->agency->name ?? 'Unknown') . " |
                        <strong>Due:</strong> {$dueDate} |
                        <strong>Score:</strong> <span class='score {$scoreClass}'>{$opportunity->score}</span> |
                        <strong>Type:</strong> " . htmlspecialchars($opportunity->notice_type) . "
                    </div>
                    <p>" . htmlspecialchars(substr($opportunity->description ?? 'No description available', 0, 200)) . "...</p>
                </div>
            ";
        }
        
        $html .= "</div>";
    }
    
    // Due soon section
    if (!empty($dueSoon)) {
        $html .= "
            <div class='section'>
                <h2>Due Soon (Next 7 Days)</h2>
        ";
        
        foreach ($dueSoon as $opportunity) {
            $daysUntilDue = $opportunity->getDaysUntilDue();
            $urgencyClass = $daysUntilDue <= 3 ? 'score-low' : ($daysUntilDue <= 7 ? 'score-medium' : 'score-high');
            
            $html .= "
                <div class='opportunity'>
                    <h3>" . htmlspecialchars($opportunity->title) . "</h3>
                    <div class='meta'>
                        <strong>Agency:</strong> " . htmlspecialchars($opportunity->agency->name ?? 'Unknown') . " |
                        <strong>Due:</strong> " . date('M j, Y', strtotime($opportunity->due_at)) . " 
                        <span class='score {$urgencyClass}'>({$daysUntilDue} days)</span> |
                        <strong>Score:</strong> {$opportunity->score}
                    </div>
                </div>
            ";
        }
        
        $html .= "</div>";
    }
    
    // High score section
    if (!empty($highScore)) {
        $html .= "
            <div class='section'>
                <h2>High-Score Opportunities</h2>
        ";
        
        foreach ($highScore as $opportunity) {
            $html .= "
                <div class='opportunity'>
                    <h3>" . htmlspecialchars($opportunity->title) . "</h3>
                    <div class='meta'>
                        <strong>Agency:</strong> " . htmlspecialchars($opportunity->agency->name ?? 'Unknown') . " |
                        <strong>Score:</strong> <span class='score score-high'>{$opportunity->score}</span> |
                        <strong>Type:</strong> " . htmlspecialchars($opportunity->notice_type) . "
                    </div>
                </div>
            ";
        }
        
        $html .= "</div>";
    }
    
    $html .= "
        </div>
        
        <div class='footer'>
            <p>This digest was automatically generated by {$appName}</p>
            <p>To manage your digest preferences, contact your system administrator</p>
        </div>
    </body>
    </html>
    ";
    
    return $html;
}

/**
 * Send digest email
 */
function sendDigestEmail(string $email, string $content): bool
{
    try {
        // Use basic mail() function for simplicity
        // In production, you'd want to use a proper email service
        $appName = Config::get('app.name', 'GovTribe Platform');
        $subject = "{$appName} - Daily Opportunity Digest";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . Config::get('email.from_email', 'noreply@govtribe.local'),
            'Reply-To: ' . Config::get('email.from_email', 'noreply@govtribe.local'),
        ];
        
        $success = mail($email, $subject, $content, implode("\r\n", $headers));
        
        if ($success) {
            Logger::info('Digest email sent successfully', ['email' => $email]);
        } else {
            Logger::warning('Failed to send digest email', ['email' => $email]);
        }
        
        return $success;
        
    } catch (Exception $e) {
        Logger::error('Error sending digest email', [
            'email' => $email,
            'error' => $e->getMessage()
        ]);
        
        return false;
    }
}