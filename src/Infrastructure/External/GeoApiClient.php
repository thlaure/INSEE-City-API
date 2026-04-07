<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Domain\City\Exception\CityDataProviderException;
use App\Domain\City\Port\CityDataProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class GeoApiClient implements CityDataProviderInterface
{
    private const string COMMUNES_PATH = '/communes';

    private const string FIELDS = 'code,nom,codeDepartement,codeRegion,population';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl,
    ) {
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function fetchAllCommunes(): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . self::COMMUNES_PATH, [
                'query' => [
                    'fields' => self::FIELDS,
                    'format' => 'json',
                    'geometry' => 'none',
                ],
            ]);

            /** @var array<array<string, mixed>> $data */
            $data = $response->toArray();

            return $data;
        } catch (Throwable $e) {
            throw CityDataProviderException::fromPrevious($e);
        }
    }
}
