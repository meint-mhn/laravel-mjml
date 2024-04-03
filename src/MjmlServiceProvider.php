<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml;

use Illuminate\Support\ServiceProvider;

class MjmlServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            sprintf('%s/../config/mjml.php', __DIR__),
            'mjml'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    sprintf('%s/../config/mjml.php', __DIR__) => config_path('mjml.php'),
                ],
                'mjml-config'
            );
        }
    }

}
