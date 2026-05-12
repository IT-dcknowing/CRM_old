<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SmtpSetting;

$smtp = SmtpSetting::first();

echo "SMTP Settings in DB:\n";
echo "Driver: " . $smtp->mail_driver . "\n";
echo "Host: " . $smtp->mail_host . "\n";
echo "Port: " . $smtp->mail_port . "\n";
echo "Username: " . $smtp->mail_username . "\n";
echo "Encryption: " . $smtp->mail_encryption . "\n";
echo "From Email: " . $smtp->mail_from_email . "\n";
echo "Verified: " . ($smtp->verified ? 'Yes' : 'No') . "\n";
