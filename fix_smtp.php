<?php
$s = \App\Models\SmtpSetting::first();
if ($s) {
    $s->mail_password = 'E=0l~2yVR7e7vVii';
    $s->save();
    echo "Updated SMTP Password\n";
    
    // Verify it works
    $test = $s->verifySmtp();
    echo "Verification result: " . json_encode($test) . "\n";
} else {
    echo "No SMTP setting found\n";
}
