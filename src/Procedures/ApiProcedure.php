<?php

declare(strict_types=1);

namespace DayLaborers\LaravelMjml\Procedures;

use DayLaborers\LaravelMjml\Contracts\MjmlProcedure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class ApiProcedure implements MjmlProcedure
{
    /** @var string */
    protected string $apiUrl = 'https://api.mjml.io/v1/render';

    /**
     * @param string $applicationId
     * @param string $secretKey
     */
    public function __construct(
        protected string $applicationId,
        protected string $secretKey
    ) {}

    /**
     * @param string $mjml
     *
     * @return string
     * @throws RequestException
     */
    public function render(string $mjml): string
    {
        $response = Http::withBasicAuth($this->applicationId, $this->secretKey)
            ->post($this->apiUrl, compact('mjml'));

        $response->throw();

        return $response->json('html');
    }
}
