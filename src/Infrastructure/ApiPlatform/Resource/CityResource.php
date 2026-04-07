<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiPlatform\StateProvider\SearchCitiesStateProvider;

#[ApiResource(
    shortName: 'City',
    operations: [
        new GetCollection(
            uriTemplate: '/cities',
            provider: SearchCitiesStateProvider::class,
        ),
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 30,
)]
final readonly class CityResource
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $inseeCode = '',
        public string $name = '',
        public string $departmentCode = '',
        public string $regionCode = '',
        public ?int $population = null,
    ) {
    }
}
