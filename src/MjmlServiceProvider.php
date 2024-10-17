<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml;

use DayLaborers\LaravelMjml\Contracts\MjmlProcedure;
use DayLaborers\LaravelMjml\Enums\Procedure;
use DayLaborers\LaravelMjml\Procedures\ApiProcedure;
use DayLaborers\LaravelMjml\Procedures\CliProcedure;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\DynamicComponent;
use Illuminate\View\Engines\CompilerEngine;

class MjmlServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            sprintf('%s/../config/mjml.php', __DIR__),
            'mjml'
        );

        $this->app->singleton(MjmlRenderer::class);
        $this->app->bind(MjmlProcedure::class, function (Application $app) {
            return $app->make(Procedure::from(config('mjml.procedure'))->rendererClass());
        });

        $this->app->when(ApiProcedure::class)
            ->needs('$applicationId')
            ->giveConfig('mjml.credentials.application_id');
        $this->app->when(ApiProcedure::class)
            ->needs('$secretKey')
            ->giveConfig('mjml.credentials.secret_key');

        $this->app->when(CliProcedure::class)
            ->needs('$binaryPath')
            ->giveConfig('mjml.cli_path');

        $this->app['view']->addExtension('mjml.blade.php', 'mjml');

        $this->registerMjmlCompiler();
        $this->registerMjmlEngine();
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

    protected function registerMjmlCompiler(): void
    {
        $this->app->singleton('mjml.compiler', function ($app) {
            return tap(
                new MjmlCompiler(
                    $app['files'],
                    $app['config']->get('view.compiled'),
                    $app['config']->get('view.relative_hash', false) ? $app->basePath() : '',
                    false, // never cache mjml compiler
                    $app['config']->get('view.compiled_extension', 'php'),
                ), function ($compiler) {
                    $compiler->setProcedure($this->app->make(MjmlProcedure::class));
                    $compiler->component('dynamic-component', DynamicComponent::class);
                });
        });
    }

    protected function registerMjmlEngine(): void
    {
        $this->app['view.engine.resolver']->register(
            'mjml',
            function () {
                $compiler = new CompilerEngine($this->app['mjml.compiler'], $this->app['files']);

                $this->app->terminating(static function () use ($compiler) {
                    $compiler->forgetCompiledOrNotExpired();
                });

                return $compiler;
            }
        );
    }
}
