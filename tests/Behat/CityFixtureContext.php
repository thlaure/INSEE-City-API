<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\City;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CityFixtureContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @Given there are no cities in the database
     */
    public function thereAreNoCitiesInTheDatabase(): void
    {
        // schema is reset in ApiContext::resetDatabase — nothing to do
    }

    /**
     * @Given the following cities exist:
     */
    public function theFollowingCitiesExist(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $city = new City(
                inseeCode: $row['inseeCode'],
                name: $row['name'],
                departmentCode: $row['departmentCode'],
                regionCode: $row['regionCode'],
                postalCode: '' !== $row['postalCode'] ? ($row['postalCode'] ?? null) : null,
                createdAt: new \DateTimeImmutable(),
            );

            $this->entityManager->persist($city);
        }

        $this->entityManager->flush();
    }
}
