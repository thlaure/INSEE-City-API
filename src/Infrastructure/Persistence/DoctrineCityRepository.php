<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\City\Model\City as DomainCity;
use App\Domain\City\Port\CityRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\UuidV7;

final class DoctrineCityRepository implements CityRepositoryInterface
{
    /** @var array<string, true>|null */
    private ?array $knownCityKeys = null;

    private readonly Connection $connection;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->connection = $entityManager->getConnection();
    }

    public function save(DomainCity $city): bool
    {
        $knownCityKeys = $this->getKnownCityKeys();
        $cityKey = $this->buildCityKey($city->countryCode->value, $city->localCode);
        $isNew = !isset($knownCityKeys[$cityKey]);

        $this->connection->executeStatement(
            <<<'SQL'
                INSERT INTO cities (id, country_code, local_code, name, department_code, region_code, postal_code, created_at, updated_at)
                VALUES (:id, :country_code, :local_code, :name, :department_code, :region_code, :postal_code, :created_at, :updated_at)
                ON CONFLICT (country_code, local_code) DO UPDATE SET
                    name = EXCLUDED.name,
                    department_code = EXCLUDED.department_code,
                    region_code = EXCLUDED.region_code,
                    postal_code = EXCLUDED.postal_code,
                    updated_at = EXCLUDED.updated_at
            SQL,
            [
                'id' => (string) new UuidV7(),
                'country_code' => $city->countryCode->value,
                'local_code' => $city->localCode,
                'name' => $city->name,
                'department_code' => $city->departmentCode,
                'region_code' => $city->regionCode,
                'postal_code' => $city->postalCode ?? null,
                'created_at' => $this->formatDateTime($city->createdAt),
                'updated_at' => $this->formatDateTime($city->updatedAt),
            ],
            [
                'id' => ParameterType::STRING,
                'country_code' => ParameterType::STRING,
                'local_code' => ParameterType::STRING,
                'name' => ParameterType::STRING,
                'department_code' => null !== $city->departmentCode ? ParameterType::STRING : ParameterType::NULL,
                'region_code' => null !== $city->regionCode ? ParameterType::STRING : ParameterType::NULL,
                'postal_code' => null !== $city->postalCode ? ParameterType::STRING : ParameterType::NULL,
                'created_at' => ParameterType::STRING,
                'updated_at' => ParameterType::STRING,
            ],
        );

        if ($isNew) {
            $this->knownCityKeys[$cityKey] = true;
        }

        return $isNew;
    }

    public function flush(): void
    {
    }

    /**
     * @return array<string, true>
     */
    private function getKnownCityKeys(): array
    {
        if (null !== $this->knownCityKeys) {
            return $this->knownCityKeys;
        }

        /** @var list<array{country_code: string, local_code: string}> $rows */
        $rows = $this->connection->fetchAllAssociative('SELECT country_code, local_code FROM cities');
        $this->knownCityKeys = array_fill_keys(
            array_map(fn (array $row): string => $this->buildCityKey($row['country_code'], $row['local_code']), $rows),
            true,
        );

        return $this->knownCityKeys;
    }

    private function buildCityKey(string $countryCode, string $localCode): string
    {
        return sprintf('%s:%s', $countryCode, $localCode);
    }

    private function formatDateTime(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
