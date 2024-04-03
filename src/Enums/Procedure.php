<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml\Enums;

use DayLaborers\LaravelMjml\Procedures\ApiProcedure;
use DayLaborers\LaravelMjml\Procedures\CliProcedure;

enum Procedure: string
{
    case API = 'api';
    case CLI = 'cli';

    /**
     * @return string
     */
    public function rendererClass(): string
    {
        return match ($this) {
            self::API => ApiProcedure::class,
            self::CLI => CliProcedure::class,
        };
    }
}
