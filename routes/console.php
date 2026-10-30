<?php

use App\Console\Commands\TestMailDeliveryCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('idfr:test-email {recipient?} {--smtp}', function () {
    $cmd = new TestMailDeliveryCommand;
    $cmd->setLaravel($this->getLaravel());
    $cmd->setOutput($this->output);
    $cmd->setInput($this->input);

    return $cmd->handle();
})->purpose('Verify and test real email delivery and SMTP connectivity');
