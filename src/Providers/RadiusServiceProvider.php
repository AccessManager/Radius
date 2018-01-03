<?php

namespace AccessManager\Radius\Providers;


use AccessManager\Radius\AttributeMakers\MikrotikAttributeMaker;
use AccessManager\Radius\Commands\AccountingCommand;
use AccessManager\Radius\Commands\AuthCommand;
use Illuminate\Support\ServiceProvider;

class RadiusServiceProvider extends ServiceProvider
{
    protected $commands = [
        AuthCommand::class,
        AccountingCommand::class,
    ];

    public function register()
    {
        $this->commands($this->commands);

        $this->loadMigrationsFrom( __DIR__ . "/../Database/Migrations");
    }
}