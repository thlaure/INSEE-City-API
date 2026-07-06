<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Provider;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\City;
use App\UI\ApiResource\CityResource;

/**
 * @implements ProviderInterface<CityResource>
 */
final readonly class CityItemProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?CityResource
    {
        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$entity instanceof City) {
            return null;
        }

        return CityResource::fromEntity($entity);
    }
}
