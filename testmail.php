<?php
require __DIR__ . '/vendor/autoload.php';

use Services\MailService;

$mailer = new MailService();
$testEmail = "ryankagua@gmail.com";
$verificationCode = rand(100000, 999999);

if ($mailer->sendVerification($testEmail, $verificationCode)) {
    echo "✅ Mail sent successfully to $testEmail with code: $verificationCode";
} else {
    echo "❌ Failed to send mail.";
}
