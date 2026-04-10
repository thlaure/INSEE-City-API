# Symfony/API Platform Patterns

Use these patterns as generic guidance. Always prefer nearby repository examples when they exist.

Design intent:
- apply SOLID principles without adding unnecessary abstraction
- keep clean architecture and hexagonal boundaries readable
- use native API Platform features directly when they already solve the need cleanly
- choose readability over premature optimization
- write code that is easy for a human reviewer to understand

## API Platform Native Read Resource

Use this pattern when the read side is served directly by API Platform without an extra application layer.

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['feature:read']],
    paginationEnabled: true,
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Feature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['feature:read'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['feature:read'])]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

Rules:
- Use serialization groups to control exposed fields explicitly.
- Add filters as `#[ApiFilter]` attributes rather than custom DQL.
- Do not add an application handler for read-only API Platform operations unless the query logic genuinely belongs in the domain.

## Handler / Use Case

```php
<?php

declare(strict_types=1);

namespace App\Application\Feature\Handler;

use App\Application\Feature\DTO\CreateFeatureInput;
use App\Domain\Feature\Model\Feature;
use App\Domain\Feature\Port\FeatureRepositoryInterface;

final readonly class CreateFeatureHandler
{
    public function __construct(
        private FeatureRepositoryInterface $repository,
    ) {
    }

    public function __invoke(CreateFeatureInput $input): Feature
    {
        $feature = new Feature($input->name);

        $this->repository->save($feature);

        return $feature;
    }
}
```

## Command / Query DTO

```php
<?php

declare(strict_types=1);

namespace App\Application\Feature\DTO;

final readonly class GetFeatureQuery
{
    public function __construct(
        public string $id,
    ) {
    }
}
```

## Input DTO Validation

```php
<?php

declare(strict_types=1);

namespace App\Application\Feature\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateFeatureInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,
    ) {
    }
}
```

## Output DTO / Resource Shape

```php
<?php

declare(strict_types=1);

namespace App\Application\Feature\DTO;

final readonly class FeatureOutput
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }
}
```

## Repository Interface

```php
<?php

declare(strict_types=1);

namespace App\Domain\Feature\Port;

use App\Domain\Feature\Model\Feature;

interface FeatureRepositoryInterface
{
    public function save(Feature $feature): void;

    public function find(string $id): ?Feature;
}
```

## Domain Service

```php
<?php

declare(strict_types=1);

namespace App\Domain\Feature\Service;

final readonly class ComputeFeatureStatusService
{
    public function compute(bool $enabled, ?string $code): string
    {
        if (!$enabled) {
            return 'disabled';
        }

        return null === $code ? 'pending' : 'ready';
    }
}
```

## Unit Test

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Feature\Handler;

use App\Application\Feature\DTO\CreateFeatureInput;
use App\Application\Feature\Handler\CreateFeatureHandler;
use App\Domain\Feature\Port\FeatureRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateFeatureHandlerTest extends TestCase
{
    private FeatureRepositoryInterface&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(FeatureRepositoryInterface::class);
    }

    public function testInvokeWithValidInputSavesFeature(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save');

        $handler = new CreateFeatureHandler($this->repository);

        $result = $handler(new CreateFeatureInput('Example'));

        $this->assertSame('Example', $result->name());
    }
}
```
