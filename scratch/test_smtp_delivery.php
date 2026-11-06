<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Mail;

echo "Checking SMTP settings...\n";
echo 'Mailer: '.config('mail.default')."\n";
echo 'Host: '.config('mail.mailers.smtp.host')."\n";
echo 'Port: '.config('mail.mailers.smtp.port')."\n";
echo 'From: '.config('mail.from.address').' ('.config('mail.from.name').")\n";

$recipient = env('MAIL_FROM_ADDRESS', 'azmiyashiri22@gmail.com');
echo "Testing delivery to: $recipient\n";

$user = new User([
    'name' => 'IDFR Test Member',
    'email' => $recipient,
]);

$testUrl = url('/reset-password/test-token-12345?email='.urlencode($recipient));

try {
    Mail::mailer('smtp')->to($recipient)->send(new PasswordResetMail($user, $testUrl));
    echo "SUCCESS: Email sent successfully via SMTP!\n";
} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
}
