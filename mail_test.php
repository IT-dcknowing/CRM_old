<?php
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Configuration SMTP actuelle :\n";
    echo "Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "Password: " . (config('mail.mailers.smtp.password') ? '********' : 'VIDE') . "\n";
    echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "From: " . config('mail.from.address') . "\n";

    echo "\nTentative d'envoi d'un email de test...\n";
    Mail::raw('Ceci est un test SMTP de diagnostic.', function ($message) {
        $message->to('eve10assem@gmail.com')
            ->subject('Test Diagnostic Flux CRM');
    });
    echo "Succès : L'email a été accepté par le serveur SMTP.\n";
}
catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    Log::error("Erreur script mail_test.php: " . $e->getMessage());
}
