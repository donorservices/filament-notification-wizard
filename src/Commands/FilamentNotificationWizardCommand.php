<?php

namespace Donorservices\FilamentNotificationWizard\Commands;

use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

use function Laravel\Prompts\search;

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

        $notificationMethods = $this->analyzeNotificationMethods();
        foreach ($notificationMethods as $methodName => $params) {
            $this->info("Method: $methodName");
            foreach ($params as $param) {
                $this->info(" - Parameter: {$param['name']}, Type: {$param['type']}, Optional: " . ($param['optional'] ? 'Yes' : 'No'));
            }
        }

        $attributes = $this->promptWithOptionalFollowUp();
        $action = $this->ask('What action is being notified for? (e.g., Project build success)');
        $moduleName = $this->ask('If using Modules enter the Module Name (optional).  If not the default location "App\\Filament\\Notifications" will be used.');

        $className = Str::studly($action) . 'Notification';
        $namespace = $moduleName ? "Modules\\$moduleName\\Notifications\\Filament" : 'App\\Filament\\Notifications';
        $path = $moduleName ? "Modules/$moduleName/Notifications/Filament/$className.php" : "app/Filament/Notifications/$className.php";

        // $stubPath = base_path('/stubs/filament-notification.php.stub');
        $stubPath = __DIR__ . '/../../stubs/filament-notification.php.stub';

        // Load and process the stub file
        // $stubPath = 'stubs/filament-notification.php.stub'; // The relative path to your stub within the module

        $stub = file_get_contents($stubPath);

        $stub = str_replace('{{ className }}', $className, $stub);
        $stub = str_replace('{{ namespace }}', $namespace, $stub);

        $attributeStrings = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $methodCall = Str::camel($attributeName);
            $attributeStrings[] = $attributeValue === null ? $methodCall . '()' : $methodCall . "('$attributeValue')";
        }
        $stub = str_replace('{{attributesPlaceholder}}', implode("\n            ", $attributeStrings), $stub);

        (new Filesystem)->ensureDirectoryExists(dirname($path));
        file_put_contents($path, $stub);

        $this->info("Notification class $className created at $path");
    }

    private function analyzeNotificationMethods(): array
    {
        $class = new ReflectionClass(Notification::class);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $methodDetails = [];
        foreach ($methods as $method) {
            if ($this->shouldExcludeMethod($method)) {
                continue;
            }

            $parameters = [];
            foreach ($method->getParameters() as $param) {
                $type = $param->getType();

                if ($type instanceof ReflectionUnionType) {
                    $typeNames = array_map(fn ($t) => $t->getName(), $type->getTypes());
                    $typeName = implode('|', $typeNames);
                } elseif ($type instanceof ReflectionNamedType) {
                    $typeName = $type->getName();
                } else {
                    $typeName = 'mixed';
                }

                $parameters[] = [
                    'name' => $param->getName(),
                    'type' => $typeName,
                    'optional' => $param->isOptional(),
                ];
            }

            $methodDetails[$method->getName()] = $parameters;
        }

        return $methodDetails;
    }

    private function shouldExcludeMethod(ReflectionMethod $method): bool
    {
        // Exclude methods like constructors, factory methods, etc.
        return strpos($method->name, 'make') === 0 || strpos($method->name, '__') === 0;
    }

    private function &getNotificationAttributes(): array
    {
        static $attributes = null;
        if ($attributes === null) {
            // Load attributes
            $notificationClass = new ReflectionClass(Notification::class);
            $methods = $notificationClass->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                if (strpos($method->name, 'make') === 0 || strpos($method->name, '__') === 0) {
                    continue;
                }
                $attributes[$method->name] = $method->name;
            }
        }

        return $attributes;
    }

    private function promptWithOptionalFollowUp(): array
    {
        $attributes = [];
        $availableAttributes = &$this->getNotificationAttributes();

        while (true) {
            $attributeName = $this->searchForAttribute($availableAttributes);
            if ($attributeName === null) {
                break;
            }

            unset($availableAttributes[$attributeName]); // Remove the selected attribute

            $attributeValue = $this->handleText('Enter the argument of the attribute (press enter to leave blank)');
            $attributes[$attributeName] = $attributeValue === null ? '' : $attributeValue;
        }

        return $attributes;
    }

    private function searchForAttribute(array &$availableAttributes): ?string
    {
        $exitOption = 'EXIT_SEARCH';

        $result = search(
            'Search for a Filament Notification attribute (press Enter or ESC to stop)',
            function (string $searchValue) use (&$availableAttributes, $exitOption) {
                if ($searchValue === '') {
                    return [$exitOption => 'Exit Search']; // Add an exit option
                }

                return array_filter(
                    $availableAttributes,
                    fn ($attribute) => str_contains($attribute, $searchValue)
                );
            }
        );

        return $result !== $exitOption ? $result : null; // Return null if 'Exit Search' was selected
    }

    private function handleText($question): ?string
    {
        $response = $this->ask($question);

        return $response === '' ? null : $response;
    }
}
