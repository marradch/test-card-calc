<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyRatesLoader
{
    public function __construct(
        protected HttpClientInterface $client,
    ) {}

    public function load(): ?array
    {
        $response = $this->client->request(
            'GET',
            'https://api.freecurrencyapi.com/v1/latest',
            [
                'query' => [
                    'apikey' => 'fca_live_pfc87jPfmouVPcDUpl32XkK3D6f7BeupOjtLYJXl',
                    'base_currency' => CurrencyEnum::USD->value,
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new \Exception("Wrong status code from exchanger");
        }

        $content = $response->toArray();
        if (!isset($content['data'])) {
            throw new \Exception("Wrong data structure from exchanger");
        }

        return $content['data'];
    }
}
