<?php

namespace App\Console\Commands;

use App\Mail\EmailVerificationOtpMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailDeliveryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idfr:test-email {recipient? : Recipient email address to receive the test OTP} {--smtp : Force test over SMTP mailer even if default is log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and test real email delivery and SMTP connectivity for IDFR verification emails';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultMailer = config('mail.default');
        $useSmtp = $this->option('smtp') || $defaultMailer === 'smtp';
        $activeMailer = $useSmtp ? 'smtp' : $defaultMailer;
        $smtpConfig = config('mail.mailers.smtp', []);

        $this->info('--- IDFR Mail Configuration Diagnostics ---');
        $this->line("Active Delivery Driver: <comment>{$activeMailer}</comment> (Default in .env: {$defaultMailer})");
        $this->line('SMTP Host: <comment>'.($smtpConfig['host'] ?? 'N/A').'</comment>');
        $this->line('SMTP Port: <comment>'.($smtpConfig['port'] ?? 'N/A').'</comment>');
        $this->line('SMTP Encryption: <comment>'.($smtpConfig['encryption'] ?? 'None').'</comment>');
        $this->line('SMTP Scheme: <comment>'.($smtpConfig['scheme'] ?? 'Auto').'</comment>');
        $this->line('SMTP Username: <comment>'.(! empty($smtpConfig['username']) ? $smtpConfig['username'] : 'NOT CONFIGURED (null)').'</comment>');
        $this->line('SMTP Password: <comment>'.(! empty($smtpConfig['password']) ? 'CONFIGURED ('.strlen($smtpConfig['password']).' chars)' : 'NOT CONFIGURED (null)').'</comment>');
        $this->line('From Address: <comment>'.config('mail.from.address').'</comment>');
        $this->line('From Name: <comment>'.config('mail.from.name').'</comment>');
        $this->newLine();

        if ($activeMailer === 'log') {
            $this->warn('NOTE: MAIL_MAILER is currently set to "log".');
            $this->warn('Emails are currently written to storage/logs/laravel.log rather than dispatched to real inboxes.');
            $this->warn('To send real emails to your Gmail/inbox:');
            $this->warn('1. Set MAIL_MAILER=smtp in your .env');
            $this->warn('2. Configure MAIL_USERNAME, MAIL_PASSWORD, and MAIL_ENCRYPTION=tls in .env');
            $this->warn('3. Or run: php artisan idfr:test-email your-email@gmail.com --smtp');
            $this->newLine();
        }

        $recipient = $this->argument('recipient') ?: config('mail.from.address');

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid recipient email address: {$recipient}");

            return Command::FAILURE;
        }

        $this->info("Attempting to send a test IDFR OTP email to <comment>{$recipient}</comment> via <comment>{$activeMailer}</comment>...");

        try {
            $testOtp = sprintf('%06d', random_int(100000, 999999));
            $mailable = new EmailVerificationOtpMail($testOtp, 'IDFR Member');

            if ($useSmtp) {
                Mail::mailer('smtp')->to($recipient)->send($mailable);
            } else {
                Mail::to($recipient)->send($mailable);
            }

            $this->newLine();
            $this->info("✓ SUCCESS: Test email accepted and dispatched successfully to [{$recipient}] via [{$activeMailer}].");

            if ($activeMailer === 'log') {
                $this->line('Message was recorded in storage/logs/laravel.log (driver is "log").');
            } else {
                $this->line('SMTP server accepted the message. Please check the inbox and spam folder.');
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('✗ DELIVERY FAILED: Unable to send email via SMTP.');
            $this->error('Error Message: '.$e->getMessage());
            $this->newLine();

            if (str_contains(strtolower($e->getMessage()), 'authentication') || str_contains(strtolower($e->getMessage()), '535')) {
                $this->warn('Diagnostic Hint: SMTP Authentication failed. If using Gmail:');
                $this->warn('1. Ensure 2-Step Verification is turned ON for your Google account.');
                $this->warn('2. Generate a 16-character Google App Password (not your personal password).');
                $this->warn('3. Set MAIL_PASSWORD="your-app-password" without spaces.');
            } elseif (str_contains(strtolower($e->getMessage()), 'connection') || str_contains(strtolower($e->getMessage()), 'timeout') || str_contains(strtolower($e->getMessage()), '10060')) {
                $this->warn('Diagnostic Hint: Connection timeout or unreachable host.');
                $this->warn('Verify your network connection and ensure port 587 (TLS) or 465 (SSL) is not blocked by a firewall.');
            }

            return Command::FAILURE;
        }
    }
}
