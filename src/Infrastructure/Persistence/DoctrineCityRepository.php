<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\City\Model\City as DomainCity;
use App\Domain\City\Model\CityCollection;
use App\Domain\City\Model\CitySearchCriteria;
use App\Domain\City\Port\CityRepositoryInterface;
use App\Entity\City as CityEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class DoctrineCityRepository implements CityRepositoryInterface
{
    /** @var EntityRepository<CityEntity> */
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $this->repository = $entityManager->getRepository(CityEntity::class);
    }

    public function findByInseeCode(string $inseeCode): ?DomainCity
    {
        $entity = $this->repository->findOneBy(['inseeCode' => $inseeCode]);

        return $entity instanceof CityEntity ? $this->toDomainModel($entity) : null;
    }

    public function save(DomainCity $city): bool
    {
        $entity = $this->repository->findOneBy(['inseeCode' => $city->inseeCode]);

        if (null === $entity) {
            $entity = new CityEntity(
                inseeCode: $city->inseeCode,
                name: $city->name,
                departmentCode: $city->departmentCode,
                regionCode: $city->regionCode,
                createdAt: $city->createdAt,
            );
            $entity->updateFromDomainModel(
                name: $city->name,
                departmentCode: $city->departmentCode,
                regionCode: $city->regionCode,
                postalCode: $city->postalCode,
            );
            $this->entityManager->persist($entity);

            return true;
        }

        $entity->updateFromDomainModel(
            name: $city->name,
            departmentCode: $city->departmentCode,
            regionCode: $city->regionCode,
            postalCode: $city->postalCode,
        );

        return false;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function findByCriteria(CitySearchCriteria $criteria): CityCollection
    {
        $qb = $this->repository->createQueryBuilder('c');

        if (null !== $criteria->name) {
            $qb->andWhere('LOWER(c.name) LIKE LOWER(:name)')
                ->setParameter('name', '%' . $criteria->name . '%');
        }

        if (null !== $criteria->departmentCode) {
            $qb->andWhere('c.departmentCode = :departmentCode')
                ->setParameter('departmentCode', $criteria->departmentCode);
        }

        if (null !== $criteria->regionCode) {
            $qb->andWhere('c.regionCode = :regionCode')
                ->setParameter('regionCode', $criteria->regionCode);
        }

        $countQb = clone $qb;
        $totalCount = (int) $countQb->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();

        $offset = ($criteria->page - 1) * $criteria->itemsPerPage;
        $qb->orderBy('c.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($criteria->itemsPerPage);

        /** @var CityEntity[] $entities */
        $entities = $qb->getQuery()->getResult();

        return new CityCollection(
            items: array_map($this->toDomainModel(...), $entities),
            totalCount: $totalCount,
        );
    }

    private function toDomainModel(CityEntity $entity): DomainCity
    {
        return new DomainCity(
            inseeCode: $entity->getInseeCode(),
            name: $entity->getName(),
            departmentCode: $entity->getDepartmentCode(),
            regionCode: $entity->getRegionCode(),
            postalCode: $entity->getPostalCode(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }
}
