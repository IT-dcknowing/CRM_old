<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TestSmtpCommand extends Command
{
    protected $signature = 'test:smtp {email?}';
    protected $description = 'Test SMTP connection and send a test email';

    public function handle()
    {
        $email = $this->argument('email') ?? 'yo15396050@gmail.com';

        $this->info('=== SMTP Configuration Check ===');
        $this->table(['Key', 'Value'], [
            ['mail.default', config('mail.default')],
            ['mail.mailers.smtp.host', config('mail.mailers.smtp.host')],
            ['mail.mailers.smtp.port', config('mail.mailers.smtp.port')],
            ['mail.mailers.smtp.encryption', config('mail.mailers.smtp.encryption')],
            ['mail.mailers.smtp.username', config('mail.mailers.smtp.username')],
            ['mail.mailers.smtp.password', str_repeat('*', min(strlen(config('mail.mailers.smtp.password') ?? ''), 8)) . ' (' . strlen(config('mail.mailers.smtp.password') ?? '') . ' chars)'],
            ['mail.from.address', config('mail.from.address')],
            ['mail.from.name', config('mail.from.name')],
            ['mail.verified', config('mail.verified') ? 'true' : 'false'],
        ]);

        // Check DB settings
        $this->info("\n=== Database SMTP Settings ===");
        try {
            $dbSettings = \DB::table('smtp_settings')->first();
            if ($dbSettings) {
                $this->table(['Key', 'Value'], [
                    ['DB host', $dbSettings->mail_host],
                    ['DB port', $dbSettings->mail_port],
                    ['DB username', $dbSettings->mail_username],
                    ['DB encryption', $dbSettings->mail_encryption],
                    ['DB verified', $dbSettings->verified],
                    ['DB email_verified', $dbSettings->email_verified],
                ]);

                // Try to decrypt
                try {
                    $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($dbSettings->mail_password);
                    $this->info("DB password decryption: OK (" . strlen($decrypted) . " chars)");
                } catch (\Exception $e) {
                    $this->error("DB password decryption FAILED: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error("Cannot read DB settings: " . $e->getMessage());
        }

        // Try SMTP connection
        $this->info("\n=== Testing SMTP Connection ===");
        try {
            $smtpSetting = \App\Models\SmtpSetting::first();
            $result = $smtpSetting->verifySmtp();
            
            if ($result['success']) {
                $this->info("✅ SMTP connection: " . $result['message']);
            } else {
                $this->error("❌ SMTP connection FAILED: " . $result['message']);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ SMTP verification error: " . $e->getMessage());
            return 1;
        }

        // Try sending test email
        $this->info("\n=== Sending Test Email to {$email} ===");
        try {
            Mail::raw('Ceci est un test d\'envoi SMTP depuis FlowTask CRM. Si vous recevez ce message, la configuration SMTP fonctionne correctement.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('🧪 Test SMTP FlowTask - ' . now()->format('H:i:s'));
            });
            $this->info("✅ Test email sent successfully to {$email}!");
        } catch (\Exception $e) {
            $this->error("❌ Email sending FAILED: " . $e->getMessage());
            Log::error("TestSmtpCommand failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
