<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml\Procedures;

use DayLaborers\LaravelMjml\Contracts\MjmlProcedure;
use Symfony\Component\Process\Process;

class CliProcedure implements MjmlProcedure
{
    /**
     * @param string $binaryPath
     */
    public function __construct(protected string $binaryPath)
    {
    }

    public function render(string $mjml): string
    {
        return $this->getProcess()
            ->setInput($mjml)
            ->mustRun()
            ->getOutput();
    }

    /**
     * @return Process
     */
    protected function getProcess(): Process
    {
        return new Process(
            [
                $this->binaryPath,
                '-i',
                '--config.minify',
                'true',
                '-s',
                '--noStdoutFileComment',
            ]
        );
    }
}
