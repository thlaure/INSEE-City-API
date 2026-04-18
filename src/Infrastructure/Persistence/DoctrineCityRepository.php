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
    private ?array $knownInseeCodes = null;

    private readonly Connection $connection;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->connection = $entityManager->getConnection();
    }

    public function save(DomainCity $city): bool
    {
        $knownInseeCodes = $this->getKnownInseeCodes();
        $isNew = !isset($knownInseeCodes[$city->inseeCode]);

        $this->connection->executeStatement(
            <<<'SQL'
                INSERT INTO cities (id, insee_code, name, department_code, region_code, postal_code, created_at, updated_at)
                VALUES (:id, :insee_code, :name, :department_code, :region_code, :postal_code, :created_at, :updated_at)
                ON CONFLICT (insee_code) DO UPDATE SET
                    name = EXCLUDED.name,
                    department_code = EXCLUDED.department_code,
                    region_code = EXCLUDED.region_code,
                    postal_code = EXCLUDED.postal_code,
                    updated_at = EXCLUDED.updated_at
            SQL,
            [
                'id' => (string) new UuidV7(),
                'insee_code' => $city->inseeCode,
                'name' => $city->name,
                'department_code' => $city->departmentCode,
                'region_code' => $city->regionCode,
                'postal_code' => $city->postalCode ?? null,
                'created_at' => $this->formatDateTime($city->createdAt),
                'updated_at' => $this->formatDateTime($city->updatedAt),
            ],
            [
                'id' => ParameterType::STRING,
                'insee_code' => ParameterType::STRING,
                'name' => ParameterType::STRING,
                'department_code' => ParameterType::STRING,
                'region_code' => ParameterType::STRING,
                'postal_code' => null !== $city->postalCode ? ParameterType::STRING : ParameterType::NULL,
                'created_at' => ParameterType::STRING,
                'updated_at' => ParameterType::STRING,
            ],
        );

        if ($isNew) {
            $this->knownInseeCodes[$city->inseeCode] = true;
        }

        return $isNew;
    }

    public function flush(): void
    {
    }

    /**
     * @return array<string, true>
     */
    private function getKnownInseeCodes(): array
    {
        if (null !== $this->knownInseeCodes) {
            return $this->knownInseeCodes;
        }

        /** @var list<string> $codes */
        $codes = $this->connection->fetchFirstColumn('SELECT insee_code FROM cities');
        $this->knownInseeCodes = array_fill_keys($codes, true);

        return $this->knownInseeCodes;
    }

    private function formatDateTime(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
