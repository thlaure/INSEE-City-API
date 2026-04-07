<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Input;

use Symfony\Component\Validator\Constraints as Assert;

final class CitySearchFilters
{
    #[Assert\NotBlank(message: 'The "name" filter must not be blank. Omit the parameter to return all cities.', allowNull: true)]
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'The "departmentCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true)]
    #[Assert\Length(max: 10)]
    public ?string $departmentCode = null;

    #[Assert\NotBlank(message: 'The "regionCode" filter must not be blank. Omit the parameter to disable this filter.', allowNull: true)]
    #[Assert\Length(max: 10)]
    public ?string $regionCode = null;

    #[Assert\Positive(message: 'The "page" parameter must be a positive integer (≥ 1).')]
    public int $page = 1;

    #[Assert\Range(notInRangeMessage: 'The "itemsPerPage" parameter must be between {{ min }} and {{ max }}.', min: 1, max: 100)]
    public int $itemsPerPage = 30;
}
