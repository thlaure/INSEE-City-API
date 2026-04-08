<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\UuidV7;

#[ApiResource(shortName: 'City', operations: [
    new Get(
        uriTemplate: '/cities/{inseeCode}',
    ),
    new GetCollection(
        uriTemplate: '/cities',
        paginationEnabled: true,
        paginationItemsPerPage: 30,
        paginationMaximumItemsPerPage: 100,
        paginationClientItemsPerPage: true,
        order: ['name' => 'ASC'],
        parameters: [
            'name' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new PartialSearchFilter(),
                property: 'name',
                description: 'Partial match on the city name.',
                constraints: [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'The "name" filter must not be blank. Omit the parameter to return all cities.', allowNull: true),
                    new \Symfony\Component\Validator\Constraints\Length(max: 255),
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
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'The "exactName" filter must not be blank. Omit the parameter to disable exact-name search.', allowNull: true),
                    new \Symfony\Component\Validator\Constraints\Length(max: 255),
                ],
                castToArray: false,
            ),
            'departmentCode' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new ExactFilter(),
                property: 'departmentCode',
                description: 'Exact match on the department code.',
                constraints: [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'The "departmentCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true),
                    new \Symfony\Component\Validator\Constraints\Length(max: 10),
                ],
                castToArray: false,
            ),
            'regionCode' => new QueryParameter(
                schema: ['type' => 'string'],
                filter: new ExactFilter(),
                property: 'regionCode',
                description: 'Exact match on the region code.',
                constraints: [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'The "regionCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true),
                    new \Symfony\Component\Validator\Constraints\Length(max: 10),
                ],
                castToArray: false,
            ),
        ],
    ),
], normalizationContext: ['groups' => ['city:read']])]
#[ORM\Entity]
#[ORM\Table(name: 'cities')]
#[ORM\Index(name: 'idx_cities_department_code', columns: ['department_code'])]
#[ORM\Index(name: 'idx_cities_region_code', columns: ['region_code'])]
#[ORM\Index(name: 'idx_cities_name', columns: ['name'])]
class City
{
    #[ORM\Id]
    #[ApiProperty(identifier: false)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['city:read'])]
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10, unique: true)]
        private string $inseeCode,
        #[Groups(['city:read'])]
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
        private string $name,
        #[Groups(['city:read'])]
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
        private string $departmentCode,
        #[Groups(['city:read'])]
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
        private string $regionCode,
        #[Groups(['city:read'])]
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
        private string $postalCode = '',
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->id = new UuidV7();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getInseeCode(): string
    {
        return $this->inseeCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDepartmentCode(): string
    {
        return $this->departmentCode;
    }

    public function getRegionCode(): string
    {
        return $this->regionCode;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateFromDomainModel(
        string $name,
        string $departmentCode,
        string $regionCode,
        string $postalCode,
    ): void {
        $this->name = $name;
        $this->departmentCode = $departmentCode;
        $this->regionCode = $regionCode;
        $this->postalCode = $postalCode;
        $this->updatedAt = new DateTimeImmutable();
    }
}
