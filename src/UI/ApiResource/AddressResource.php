<?php

declare(strict_types=1);

namespace App\UI\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Domain\Address\Model\Address;
use App\Domain\Shared\Model\CountryCode;
use App\Infrastructure\Http\Provider\AddressSearchProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

#[ApiResource(shortName: 'Address', operations: [
    new GetCollection(
        uriTemplate: '/addresses/search',
        paginationEnabled: false,
        provider: AddressSearchProvider::class,
        parameters: [
            'q' => new QueryParameter(
                schema: ['type' => 'string'],
                property: 'q',
                description: 'Partial or full-text address query.',
                required: true,
                constraints: [
                    new NotBlank(message: 'The "q" parameter must not be blank.'),
                    new Length(max: 255),
                ],
                castToArray: false,
            ),
            'countryCode' => new QueryParameter(
                schema: ['type' => 'string'],
                property: 'countryCode',
                description: 'Restrict results to this ISO 3166-1 alpha-2 country code.',
                constraints: [
                    new NotBlank(message: 'The "countryCode" parameter must not be blank. Omit it to disable this filter.', allowNull: true),
                    new Choice(callback: [CountryCode::class, 'values'], message: 'The "countryCode" parameter must be a valid ISO 3166-1 alpha-2 country code.'),
                ],
                castToArray: false,
            ),
            'limit' => new QueryParameter(
                schema: ['type' => 'integer'],
                property: 'limit',
                description: 'Maximum number of results (1-20, default 10).',
                constraints: [
                    new Range(notInRangeMessage: 'The "limit" parameter must be between {{ min }} and {{ max }}.', min: 1, max: 20),
                ],
                castToArray: false,
            ),
        ],
    ),
], formats: ['json' => ['application/json']], normalizationContext: ['groups' => ['address:read']])]
final readonly class AddressResource
{
    public function __construct(
        #[ApiProperty(description: 'Full formatted address, as returned by Photon.', example: '10 Rue de la Paix, 75002 Paris')]
        #[Groups(['address:read'])]
        public string $label,
        #[ApiProperty(description: 'House number, null if Photon has no house-number-level match.', example: '10')]
        #[Groups(['address:read'])]
        public ?string $houseNumber,
        #[ApiProperty(description: 'Street name, null if Photon has no street-level match.', example: 'Rue de la Paix')]
        #[Groups(['address:read'])]
        public ?string $street,
        #[ApiProperty(description: 'Postal code, null if unavailable for this result.', example: '75002')]
        #[Groups(['address:read'])]
        public ?string $postalCode,
        #[ApiProperty(description: 'City name, null if unavailable for this result.', example: 'Paris')]
        #[Groups(['address:read'])]
        public ?string $city,
        #[ApiProperty(description: "ISO 3166-1 alpha-2 country code, null if Photon doesn't return one for this result.", example: 'FR')]
        #[Groups(['address:read'])]
        public ?string $countryCode,
        #[ApiProperty(description: 'Latitude, between -90 and 90.', example: 48.8689953)]
        #[Groups(['address:read'])]
        public float $latitude,
        #[ApiProperty(description: 'Longitude, between -180 and 180.', example: 2.3311419)]
        #[Groups(['address:read'])]
        public float $longitude,
    ) {
    }

    public static function fromDomain(Address $address): self
    {
        return new self(
            label: $address->label,
            houseNumber: $address->houseNumber,
            street: $address->street,
            postalCode: $address->postalCode,
            city: $address->city,
            countryCode: $address->countryCode?->value,
            latitude: $address->latitude,
            longitude: $address->longitude,
        );
    }
}
