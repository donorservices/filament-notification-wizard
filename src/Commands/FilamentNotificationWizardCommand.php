<?php

namespace Donorservices\FilamentNotificationWizard\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class FilamentNotificationWizardCommand extends Command
{
    protected $signature = 'make:filament-notification';
    protected $description = 'Generates a Filament notification';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $attributes = $this->promptWithOptionalFollowUp(
            'Enter the notification attribute you want i.e. "title" (press enter to stop)',
            'Enter the argument of the attribute (press enter to leave blank)'
        );
        $action = $this->ask('What action is being notified for? (e.g., Project build success)');
        $moduleName = $this->ask('If using Modules enter the Module Name (optional).  If not the default location "App\\Filament\\Notifications" will be used.');

        $className = Str::studly($action) . 'Notification';
        $namespace = $moduleName ? "Modules\\$moduleName\\Notifications\\Filament" : "App\\Filament\\Notifications";
        $path = $moduleName ? "Modules/$moduleName/Notifications/Filament/$className.php" : "app/Filament/Notifications/$className.php";

        // Load and process the stub file
        $stubPath = 'src/stubs/filament-notification.php.stub'; // The relative path to your stub within the module

        $stub = file_get_contents($stubPath);

        $stub = str_replace('{{ className }}', $className, $stub);
        $stub = str_replace('{{ namespace }}', $namespace, $stub);

        $attributeStrings = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeStrings[] = "->" . Str::camel($attributeName) . "('$attributeValue')";
        }
        $stub = str_replace('{{attributesPlaceholder}}', implode("\n            ", $attributeStrings), $stub);

        (new Filesystem)->ensureDirectoryExists(dirname($path));
        file_put_contents($path, $stub);

        $this->info("Notification class $className created at $path");
    }

    private function promptWithOptionalFollowUp($mainQuestion, $followUpQuestion = null): array
    {
        $attributes = [];
        while (true) {
            $attributeName = $this->ask($mainQuestion);
            if (!$attributeName) {
                break;
            }
            $attributeValue = $followUpQuestion ? $this->ask($followUpQuestion) : null;
            $attributes[$attributeName] = $attributeValue;
        }
        return $attributes;
    }
}
