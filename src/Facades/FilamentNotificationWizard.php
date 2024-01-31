<?php

namespace Donorservices\FilamentNotificationWizard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Donorservices\FilamentNotificationWizard\FilamentNotificationWizard
 */
class FilamentNotificationWizard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Donorservices\FilamentNotificationWizard\FilamentNotificationWizard::class;
    }
}
