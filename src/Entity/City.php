<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity]
#[ORM\Table(name: 'cities')]
#[ORM\Index(name: 'idx_cities_department_code', columns: ['department_code'])]
#[ORM\Index(name: 'idx_cities_region_code', columns: ['region_code'])]
#[ORM\Index(name: 'idx_cities_name', columns: ['name'])]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
    private string $postalCode = '';

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10, unique: true)]
        private string $inseeCode,
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
        private string $name,
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
        private string $departmentCode,
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 10)]
        private string $regionCode,
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
