<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence;

use App\Domain\City\Model\City;
use App\Infrastructure\Persistence\DoctrineCityRepository;
use App\Tests\Integration\DatabaseTestCase;

final class DoctrineCityRepositoryTest extends DatabaseTestCase
{
    private DoctrineCityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DoctrineCityRepository::class);
    }

    public function testSaveCreatesNewCityAndReturnsTrue(): void
    {
        $city = $this->makeCity('75056', 'Paris', '75', '11');

        $isNew = $this->repository->save($city);
        $this->repository->flush();

        $this->assertTrue($isNew);
        $entity = $this->entityManager->getRepository(\App\Entity\City::class)->findOneBy(['inseeCode' => '75056']);
        $this->assertInstanceOf(\App\Entity\City::class, $entity);
        $this->assertSame('Paris', $entity->getName());
    }

    public function testSaveUpdatesExistingCityAndReturnsFalse(): void
    {
        $city = $this->makeCity('75056', 'Paris', '75', '11');
        $this->repository->save($city);
        $this->repository->flush();

        $updated = $this->makeCity('75056', 'Paris Updated', '75', '11');
        $isNew = $this->repository->save($updated);
        $this->repository->flush();

        $this->assertFalse($isNew);
        $entity = $this->entityManager->getRepository(\App\Entity\City::class)->findOneBy(['inseeCode' => '75056']);
        $this->assertInstanceOf(\App\Entity\City::class, $entity);
        $this->assertSame('Paris Updated', $entity->getName());
    }

    public function testSavePersistsPostalCode(): void
    {
        $this->repository->save($this->makeCity('75056', 'Paris', '75', '11', '75001'));
        $this->repository->flush();

        $entity = $this->entityManager->getRepository(\App\Entity\City::class)->findOneBy(['inseeCode' => '75056']);
        $this->assertInstanceOf(\App\Entity\City::class, $entity);
        $this->assertSame('75001', $entity->getPostalCode());
    }

    private function makeCity(string $inseeCode, string $name, string $dept, string $region, ?string $postalCode = null): City
    {
        return new City(
            inseeCode: $inseeCode,
            name: $name,
            departmentCode: $dept,
            regionCode: $region,
            postalCode: $postalCode,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }
}
