<?php

namespace Donorservices\FilamentNotificationWizard\Commands;

use Illuminate\Console\Command;

class FilamentNotificationWizardCommand extends Command
{
    public $signature = 'filament-notification-wizard';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
