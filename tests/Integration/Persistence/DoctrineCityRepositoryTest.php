<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence;

use App\Domain\City\Model\City;
use App\Domain\City\Model\CitySearchCriteria;
use App\Infrastructure\Persistence\DoctrineCityRepository;
use App\Tests\Integration\DatabaseTestCase;
use DateTimeImmutable;

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
        $found = $this->repository->findByInseeCode('75056');
        $this->assertNotNull($found);
        $this->assertSame('Paris', $found->name);
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
        $found = $this->repository->findByInseeCode('75056');
        $this->assertNotNull($found);
        $this->assertSame('Paris Updated', $found->name);
    }

    public function testFindByInseeCodeReturnsNullForUnknownCode(): void
    {
        $this->assertNull($this->repository->findByInseeCode('99999'));
    }

    public function testFindByCriteriaWithNoFiltersReturnsPaginatedResults(): void
    {
        $this->repository->save($this->makeCity('75056', 'Paris', '75', '11'));
        $this->repository->save($this->makeCity('69123', 'Lyon', '69', '84'));
        $this->repository->flush();

        $collection = $this->repository->findByCriteria(new CitySearchCriteria());

        $this->assertSame(2, $collection->getTotalCount());
        $this->assertCount(2, $collection->getItems());
    }

    public function testFindByCriteriaFiltersByName(): void
    {
        $this->repository->save($this->makeCity('75056', 'Paris', '75', '11'));
        $this->repository->save($this->makeCity('69123', 'Lyon', '69', '84'));
        $this->repository->flush();

        $collection = $this->repository->findByCriteria(new CitySearchCriteria(name: 'par'));

        $this->assertSame(1, $collection->getTotalCount());
        $this->assertSame('Paris', $collection->getItems()[0]->name);
    }

    public function testFindByCriteriaFiltersByDepartmentCode(): void
    {
        $this->repository->save($this->makeCity('75056', 'Paris', '75', '11'));
        $this->repository->save($this->makeCity('75008', 'Paris 8e', '75', '11'));
        $this->repository->save($this->makeCity('69123', 'Lyon', '69', '84'));
        $this->repository->flush();

        $collection = $this->repository->findByCriteria(new CitySearchCriteria(departmentCode: '75'));

        $this->assertSame(2, $collection->getTotalCount());
    }

    public function testFindByCriteriaFiltersByRegionCode(): void
    {
        $this->repository->save($this->makeCity('75056', 'Paris', '75', '11'));
        $this->repository->save($this->makeCity('69123', 'Lyon', '69', '84'));
        $this->repository->flush();

        $collection = $this->repository->findByCriteria(new CitySearchCriteria(regionCode: '84'));

        $this->assertSame(1, $collection->getTotalCount());
        $this->assertSame('Lyon', $collection->getItems()[0]->name);
    }

    public function testFindByCriteriaPaginates(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->repository->save($this->makeCity("0000{$i}", "City {$i}", '01', '84'));
        }

        $this->repository->flush();

        $page1 = $this->repository->findByCriteria(new CitySearchCriteria(page: 1, itemsPerPage: 2));
        $page2 = $this->repository->findByCriteria(new CitySearchCriteria(page: 2, itemsPerPage: 2));

        $this->assertSame(5, $page1->getTotalCount());
        $this->assertCount(2, $page1->getItems());
        $this->assertCount(2, $page2->getItems());
    }

    private function makeCity(string $inseeCode, string $name, string $dept, string $region): City
    {
        return new City(
            inseeCode: $inseeCode,
            name: $name,
            departmentCode: $dept,
            regionCode: $region,
            postalCode: '',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );
    }
}
