<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\City;
use App\UI\ApiResource\CityResource;

/**
 * @implements ProviderInterface<CityResource>
 */
final readonly class CityCollectionProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
    ) {
    }

    /**
     * @return iterable<CityResource>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);

        if ($entities instanceof PaginatorInterface) {
            return new CityResourcePaginator($entities);
        }

        /** @var iterable<City> $entities */
        $mapped = [];
        foreach ($entities as $entity) {
            $mapped[] = CityResource::fromEntity($entity);
        }

        return $mapped;
    }
}
