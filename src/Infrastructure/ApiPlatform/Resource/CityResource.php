<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Infrastructure\ApiPlatform\StateProvider\GetCityStateProvider;
use App\Infrastructure\ApiPlatform\StateProvider\SearchCitiesStateProvider;

#[ApiResource(
    shortName: 'City',
    description: 'A French commune (city) from the INSEE/geo.api.gouv.fr dataset.',
    operations: [
        new Get(
            uriTemplate: '/cities/{inseeCode}',
            openapi: new OpenApiOperation(
                summary: 'Get a single commune by INSEE code',
                description: 'Returns the commune matching the given INSEE code. Returns 404 if not found.',
            ),
            provider: GetCityStateProvider::class,
        ),
        new GetCollection(
            uriTemplate: '/cities',
            openapi: new OpenApiOperation(
                summary: 'List French communes',
                description: 'Returns a paginated Hydra collection of French communes. '
                    . 'All filters are optional and combinable. '
                    . 'Passing a filter parameter with an empty value (e.g. `?name=`) is a validation error — omit the parameter instead.',
                parameters: [
                    new OpenApiParameter(
                        name: 'name',
                        in: 'query',
                        description: 'Partial, case-insensitive match on the city name (e.g. `paris`, `mar`).',
                        required: false,
                        schema: ['type' => 'string', 'example' => 'paris'],
                    ),
                    new OpenApiParameter(
                        name: 'departmentCode',
                        in: 'query',
                        description: 'Exact match on the department code (e.g. `75`, `2A`).',
                        required: false,
                        schema: ['type' => 'string', 'example' => '75'],
                    ),
                    new OpenApiParameter(
                        name: 'regionCode',
                        in: 'query',
                        description: 'Exact match on the region code (e.g. `11` for Île-de-France).',
                        required: false,
                        schema: ['type' => 'string', 'example' => '11'],
                    ),
                    new OpenApiParameter(
                        name: 'page',
                        in: 'query',
                        description: 'Page number, starting at 1.',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'default' => 1, 'example' => 1],
                    ),
                    new OpenApiParameter(
                        name: 'itemsPerPage',
                        in: 'query',
                        description: 'Number of results per page (1–100). Defaults to 30.',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 30, 'example' => 30],
                    ),
                ],
            ),
            provider: SearchCitiesStateProvider::class,
        ),
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 30,
)]
final readonly class CityResource
{
    public function __construct(
        #[ApiProperty(description: 'INSEE code uniquely identifying the commune (e.g. `75056` for Paris).', identifier: true, example: '75056')]
        public string $inseeCode = '',

        #[ApiProperty(
            description: 'Official name of the commune.',
            example: 'Paris',
        )]
        public string $name = '',

        #[ApiProperty(
            description: 'Department code the commune belongs to (e.g. `75`, `2A`).',
            example: '75',
        )]
        public string $departmentCode = '',

        #[ApiProperty(
            description: 'Region code the commune belongs to (e.g. `11` for Île-de-France).',
            example: '11',
        )]
        public string $regionCode = '',

        #[ApiProperty(
            description: 'Latest known population figure. `null` if unavailable.',
            example: 2133111,
        )]
        public ?int $population = null,
    ) {
    }
}
