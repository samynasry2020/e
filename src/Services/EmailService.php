<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Core\{Config, Database};

class EmailService
{
    private array $smtpConfig;

    public function __construct()
    {
        $this->loadSmtpConfig();
    }

    public function sendDigest(array $user, array $digestData): bool
    {
        $subject = 'Daily GovTribe Digest - ' . date('M j, Y');
        $body = $this->buildDigestEmail($user, $digestData);
        
        return $this->sendEmail($user['email'], $subject, $body);
    }

    public function sendSystemNotification(string $subject, string $message): bool
    {
        $adminEmails = $this->getAdminEmails();
        $success = true;
        
        foreach ($adminEmails as $email) {
            if (!$this->sendEmail($email, $subject, $message)) {
                $success = false;
            }
        }
        
        return $success;
    }

    public function sendSystemAlert(string $subject, string $message): bool
    {
        return $this->sendSystemNotification('[ALERT] ' . $subject, $message);
    }

    private function sendEmail(string $to, string $subject, string $body): bool
    {
        if (empty($this->smtpConfig['host'])) {
            error_log("SMTP not configured - cannot send email to: {$to}");
            return false;
        }

        try {
            // Create SMTP connection
            $smtp = fsockopen($this->smtpConfig['host'], $this->smtpConfig['port'], $errno, $errstr, 30);
            if (!$smtp) {
                throw new \Exception("SMTP connection failed: {$errstr}");
            }

            // SMTP conversation
            $this->smtpCommand($smtp, null, '220'); // Wait for greeting
            $this->smtpCommand($smtp, "EHLO " . gethostname(), '250');
            
            if ($this->smtpConfig['port'] == 587) {
                $this->smtpCommand($smtp, "STARTTLS", '220');
                stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->smtpCommand($smtp, "EHLO " . gethostname(), '250');
            }
            
            if (!empty($this->smtpConfig['username'])) {
                $this->smtpCommand($smtp, "AUTH LOGIN", '334');
                $this->smtpCommand($smtp, base64_encode($this->smtpConfig['username']), '334');
                $this->smtpCommand($smtp, base64_encode($this->smtpConfig['password']), '235');
            }
            
            $this->smtpCommand($smtp, "MAIL FROM: <{$this->smtpConfig['from_email']}>", '250');
            $this->smtpCommand($smtp, "RCPT TO: <{$to}>", '250');
            $this->smtpCommand($smtp, "DATA", '354');
            
            // Email headers and body
            $email = "From: {$this->smtpConfig['from_name']} <{$this->smtpConfig['from_email']}>\r\n";
            $email .= "To: {$to}\r\n";
            $email .= "Subject: {$subject}\r\n";
            $email .= "Date: " . date('r') . "\r\n";
            $email .= "Message-ID: <" . uniqid() . "@" . gethostname() . ">\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $email .= "Content-Transfer-Encoding: 8bit\r\n";
            $email .= "\r\n";
            $email .= $body . "\r\n";
            $email .= ".\r\n";
            
            $this->smtpCommand($smtp, $email, '250');
            $this->smtpCommand($smtp, "QUIT", '221');
            
            fclose($smtp);
            return true;
            
        } catch (\Exception $e) {
            error_log("Email send failed to {$to}: " . $e->getMessage());
            return false;
        }
    }

    private function smtpCommand($smtp, ?string $command, string $expectedCode): void
    {
        if ($command !== null) {
            fwrite($smtp, $command . "\r\n");
        }
        
        $response = fgets($smtp, 512);
        if (substr($response, 0, 3) !== $expectedCode) {
            throw new \Exception("SMTP error: {$response}");
        }
    }

    private function buildDigestEmail(array $user, array $digestData): string
    {
        $body = "Good morning {$user['first_name']},\n\n";
        $body .= "Here's your daily GovTribe digest for " . date('l, F j, Y') . ":\n\n";
        
        // New opportunities
        if (!empty($digestData['new_opportunities'])) {
            $body .= "🆕 NEW OPPORTUNITIES (" . count($digestData['new_opportunities']) . ")\n";
            $body .= str_repeat('-', 50) . "\n";
            
            foreach ($digestData['new_opportunities'] as $opp) {
                $body .= "• {$opp['title']}\n";
                $body .= "  Agency: {$opp['agency_name']}\n";
                $body .= "  Score: {$opp['score']}/100\n";
                if ($opp['due_at']) {
                    $body .= "  Due: " . date('M j, Y', strtotime($opp['due_at'])) . "\n";
                }
                $body .= "\n";
            }
        }
        
        // Due soon
        if (!empty($digestData['due_soon'])) {
            $body .= "⏰ DUE SOON (" . count($digestData['due_soon']) . ")\n";
            $body .= str_repeat('-', 50) . "\n";
            
            foreach ($digestData['due_soon'] as $opp) {
                $daysLeft = max(0, floor((strtotime($opp['due_at']) - time()) / 86400));
                $body .= "• {$opp['title']}\n";
                $body .= "  Due in {$daysLeft} days (" . date('M j', strtotime($opp['due_at'])) . ")\n";
                $body .= "  Status: {$opp['status']}\n\n";
            }
        }
        
        // High-scoring opportunities needing review
        if (!empty($digestData['high_score_review'])) {
            $body .= "⭐ HIGH-SCORING OPPORTUNITIES NEEDING REVIEW\n";
            $body .= str_repeat('-', 50) . "\n";
            
            foreach ($digestData['high_score_review'] as $opp) {
                $body .= "• {$opp['title']} (Score: {$opp['score']})\n";
                $body .= "  Agency: {$opp['agency_name']}\n\n";
            }
        }
        
        // Recent awards
        if (!empty($digestData['recent_awards'])) {
            $body .= "🏆 RECENT AWARDS\n";
            $body .= str_repeat('-', 50) . "\n";
            
            foreach ($digestData['recent_awards'] as $award) {
                $body .= "• {$award['opportunity_title']}\n";
                if ($award['award_amount']) {
                    $body .= "  Amount: $" . number_format($award['award_amount']) . "\n";
                }
                $body .= "  Date: " . date('M j, Y', strtotime($award['award_date'])) . "\n\n";
            }
        }
        
        // Pipeline summary
        if (!empty($digestData['pipeline_summary'])) {
            $body .= "📊 PIPELINE SUMMARY\n";
            $body .= str_repeat('-', 50) . "\n";
            
            foreach ($digestData['pipeline_summary'] as $status) {
                $body .= "{$status['status']}: {$status['count']}\n";
            }
            $body .= "\n";
        }
        
        $body .= "Best regards,\n";
        $body .= "GovTribe Platform\n\n";
        $body .= "---\n";
        $body .= "This is an automated digest. To manage your email preferences, ";
        $body .= "contact your system administrator.\n";
        
        return $body;
    }

    private function loadSmtpConfig(): void
    {
        $settings = Database::fetchAll('SELECT `key`, `value` FROM settings WHERE `key` LIKE "email_%"');
        
        $config = [
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'from_email' => '',
            'from_name' => 'GovTribe Platform'
        ];
        
        foreach ($settings as $setting) {
            $key = str_replace('email_smtp_', '', $setting['key']);
            $key = str_replace('email_from_', 'from_', $key);
            $config[$key] = $setting['value'];
        }
        
        $this->smtpConfig = $config;
    }

    private function getAdminEmails(): array
    {
        $admins = Database::fetchAll(
            'SELECT email FROM users WHERE role = "Admin" AND status = "Active"'
        );
        
        return array_column($admins, 'email');
    }
}