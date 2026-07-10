<?php

declare(strict_types=1);

namespace App\UI\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\QueryParameter;
use App\Domain\Shared\Model\CountryCode;
use App\Entity\City;
use App\Infrastructure\Http\Provider\CityCollectionProvider;
use App\Infrastructure\Http\Provider\CityItemProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(shortName: 'City', operations: [
    new Get(
        uriTemplate: '/cities/{countryCode}/{localCode}',
        uriVariables: [
            'countryCode' => new Link(fromClass: self::class, identifiers: ['countryCode']),
            'localCode' => new Link(fromClass: self::class, identifiers: ['localCode']),
        ],
        provider: CityItemProvider::class,
    ),
    new GetCollection(
        uriTemplate: '/cities',
        paginationEnabled: true,
        paginationItemsPerPage: 30,
        paginationMaximumItemsPerPage: 1000,
        paginationClientItemsPerPage: true,
        order: ['name' => 'ASC'],
        provider: CityCollectionProvider::class,
        parameters: [
            'name' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new PartialSearchFilter(),
                property: 'name',
                description: 'Partial match on the city name.',
                constraints: [
                    new NotBlank(message: 'The "name" filter must not be blank. Omit the parameter to return all cities.', allowNull: true),
                    new Length(max: 255),
                ],
                castToArray: false,
            ),
            'exactName' => new QueryParameter(
                key: 'exactName',
                schema: ['type' => 'string'],
                filter: new ExactFilter(),
                property: 'name',
                description: 'Exact match on the city name.',
                constraints: [
                    new NotBlank(message: 'The "exactName" filter must not be blank. Omit the parameter to disable exact-name search.', allowNull: true),
                    new Length(max: 255),
                ],
                castToArray: false,
            ),
            'countryCode' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new ExactFilter(),
                property: 'countryCode',
                description: 'Exact match on the country code.',
                constraints: [
                    new NotBlank(message: 'The "countryCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true),
                    new Length(max: 2),
                    new Choice(callback: [CountryCode::class, 'values'], message: 'The "countryCode" filter must be a valid ISO 3166-1 alpha-2 country code.'),
                ],
                castToArray: false,
            ),
            'departmentCode' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new ExactFilter(),
                property: 'departmentCode',
                description: 'Exact match on the department code.',
                constraints: [
                    new NotBlank(message: 'The "departmentCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true),
                    new Length(max: 10),
                ],
                castToArray: false,
            ),
            'regionCode' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new ExactFilter(),
                property: 'regionCode',
                description: 'Exact match on the region code.',
                constraints: [
                    new NotBlank(message: 'The "regionCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true),
                    new Length(max: 10),
                ],
                castToArray: false,
            ),
        ],
    ),
], normalizationContext: ['groups' => ['city:read']], stateOptions: new Options(entityClass: City::class))]
final readonly class CityResource
{
    public function __construct(
        #[ApiProperty(description: 'ISO 3166-1 alpha-2 country code, part of the identifier. Validated against the full ISO list at write time and on the countryCode filter.', identifier: true, example: 'FR')]
        #[Groups(['city:read'])]
        public CountryCode $countryCode,
        #[ApiProperty(description: "Country-local city code, part of the identifier. Semantics are provider-defined, not a portable government code for every country: France's is the official INSEE commune code, Germany's is GeoNames' internal geonameid (an arbitrary numeric ID, not an administrative code).", identifier: true, example: '75056')]
        #[Groups(['city:read'])]
        public string $localCode,
        #[ApiProperty(description: 'City name.', example: 'Paris')]
        #[Groups(['city:read'])]
        public string $name,
        #[ApiProperty(description: 'Department code, null if not applicable for the country.', example: '75')]
        #[Groups(['city:read'])]
        public ?string $departmentCode,
        #[ApiProperty(description: 'Region code, null if not applicable for the country.', example: '11')]
        #[Groups(['city:read'])]
        public ?string $regionCode,
        #[ApiProperty(description: 'First postal code, null if unavailable.', example: '75001')]
        #[Groups(['city:read'])]
        public ?string $postalCode,
    ) {
    }

    public static function fromEntity(City $entity): self
    {
        return new self(
            countryCode: $entity->getCountryCode(),
            localCode: $entity->getLocalCode(),
            name: $entity->getName(),
            departmentCode: $entity->getDepartmentCode(),
            regionCode: $entity->getRegionCode(),
            postalCode: $entity->getPostalCode(),
        );
    }
}
