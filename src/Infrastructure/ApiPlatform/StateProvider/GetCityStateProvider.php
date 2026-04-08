<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Handler\GetCityHandler;
use App\Domain\City\Query\GetCityQuery;
use App\Infrastructure\ApiPlatform\Resource\CityResource;

use function is_string;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<CityResource>
 */
final readonly class GetCityStateProvider implements ProviderInterface
{
    public function __construct(
        private GetCityHandler $getCityHandler,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CityResource
    {
        $raw = $uriVariables['inseeCode'] ?? '';
        $inseeCode = is_string($raw) ? $raw : '';

        try {
            $city = ($this->getCityHandler)(new GetCityQuery($inseeCode));
        } catch (CityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return new CityResource(
            inseeCode: $city->inseeCode,
            name: $city->name,
            departmentCode: $city->departmentCode,
            regionCode: $city->regionCode,
            postalCode: $city->postalCode,
        );
    }
}
