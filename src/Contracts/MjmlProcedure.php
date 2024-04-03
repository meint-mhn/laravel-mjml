<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml\Contracts;

interface MjmlProcedure
{
    /**
     * @param string $mjml
     *
     * @return string
     */
    public function render(string $mjml): string;
}
