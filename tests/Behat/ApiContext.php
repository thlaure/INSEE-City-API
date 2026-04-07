<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use function array_key_exists;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;

use function count;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

use function gettype;
use function is_array;
use function is_numeric;
use function is_scalar;
use function is_string;
use function json_decode;
use function json_encode;

use const JSON_ERROR_NONE;

use function json_last_error;

use const JSON_THROW_ON_ERROR;

use function preg_match;
use function preg_replace_callback;

use RuntimeException;

use function sprintf;
use function str_contains;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class ApiContext implements Context
{
    /** @var array<string, mixed>|null */
    private ?array $lastResponseData = null;

    /** @var array<string, string> */
    private array $storedVariables = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly KernelBrowser $client,
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function resetDatabase(BeforeScenarioScope $scope): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $this->entityManager->clear();
        $this->storedVariables = [];
        $this->lastResponseData = null;
    }

    /**
     * @When I send a :method request to :url
     */
    public function iSendARequestTo(string $method, string $url): void
    {
        $url = $this->replaceStoredVariables($url);
        $this->client->request(
            $method,
            $url,
            [],
            [],
            $this->buildHeaders('application/json'),
        );
        $this->lastResponseData = null;
    }

    /**
     * @When I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody(string $method, string $url, PyStringNode $body): void
    {
        $url = $this->replaceStoredVariables($url);
        $bodyContent = $this->replaceStoredVariables($body->getRaw());

        $contentType = $method === 'PATCH' ? 'application/merge-patch+json' : 'application/json';

        $this->client->request(
            $method,
            $url,
            [],
            [],
            $this->buildHeaders($contentType),
            $bodyContent,
        );
        $this->lastResponseData = null;
    }

    /**
     * @Then the response status code should be :code
     */
    public function theResponseStatusCodeShouldBe(int $code): void
    {
        $actual = $this->client->getResponse()->getStatusCode();

        if ($actual !== $code) {
            throw new RuntimeException(sprintf(
                'Expected status code %d, got %d. Response: %s',
                $code,
                $actual,
                $this->client->getResponse()->getContent(),
            ));
        }
    }

    /**
     * @Then the response should be JSON
     */
    public function theResponseShouldBeJson(): void
    {
        $content = $this->client->getResponse()->getContent();
        /** @var array<string, mixed>|null $data */
        $data = json_decode((string) $content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Response is not valid JSON: ' . $content);
        }

        $this->lastResponseData = $data;
    }

    /**
     * @Then the JSON response should contain :key
     */
    public function theJsonResponseShouldContain(string $key): void
    {
        $data = $this->getJsonResponse();

        if (!array_key_exists($key, $data)) {
            throw new RuntimeException(sprintf(
                'JSON response does not contain key "%s". Keys: %s',
                $key,
                implode(', ', array_keys($data)),
            ));
        }
    }

    /**
     * @Then the JSON response :key should equal :value
     */
    public function theJsonResponseKeyShouldEqual(string $key, string $value): void
    {
        $data = $this->getJsonResponse();

        if (!array_key_exists($key, $data)) {
            throw new RuntimeException(sprintf('Key "%s" not found in response', $key));
        }

        $actual = $data[$key];
        $expected = $this->castValue($value);

        if ($actual !== $expected) {
            $actualString = is_array($actual) ? (string) json_encode($actual) : (is_scalar($actual) ? (string) $actual : '');

            throw new RuntimeException(sprintf(
                'Expected "%s" to equal "%s", got "%s"',
                $key,
                $value,
                $actualString,
            ));
        }
    }

    /**
     * @Then the JSON collection should be empty
     */
    public function theJsonCollectionShouldBeEmpty(): void
    {
        $data = $this->getJsonResponse();

        if (isset($data['member'])) {
            if ($data['member'] !== []) {
                throw new RuntimeException('Expected empty collection, got: ' . json_encode($data['member']));
            }

            return;
        }

        if ($data !== []) {
            throw new RuntimeException('Expected empty array, got: ' . json_encode($data));
        }
    }

    /**
     * @Then the JSON collection should have :count items
     */
    public function theJsonCollectionShouldHaveItems(int $count): void
    {
        $data = $this->getJsonResponse();

        $actual = isset($data['member']) && is_array($data['member']) ? count($data['member']) : count($data);

        if ($actual !== $count) {
            throw new RuntimeException(sprintf('Expected %d items, got %d', $count, $actual));
        }
    }

    /**
     * @Then the JSON response should contain a valid UUID in :field
     */
    public function theJsonResponseShouldContainAValidUuidIn(string $field): void
    {
        $data = $this->getJsonResponse();

        if (!isset($data[$field])) {
            throw new RuntimeException(sprintf('Response does not contain "%s" field', $field));
        }

        $fieldValue = $data[$field];
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

        if (!is_string($fieldValue) || !preg_match($pattern, $fieldValue)) {
            throw new RuntimeException(sprintf('"%s" is not a valid UUID', is_scalar($fieldValue) ? (string) $fieldValue : gettype($fieldValue)));
        }
    }

    /**
     * @Then the response content type should contain :contentType
     */
    public function theResponseContentTypeShouldContain(string $contentType): void
    {
        $actual = $this->client->getResponse()->headers->get('Content-Type');

        if (!str_contains($actual ?? '', $contentType)) {
            throw new RuntimeException(sprintf(
                'Expected content type to contain "%s", got "%s"',
                $contentType,
                $actual ?? '',
            ));
        }
    }

    /**
     * @Then the JSON response should be a RFC 7807 problem
     */
    public function theJsonResponseShouldBeRfc7807Problem(): void
    {
        $this->theResponseContentTypeShouldContain('application/problem+json');
        $this->theJsonResponseShouldContain('type');
        $this->theJsonResponseShouldContain('title');
        $this->theJsonResponseShouldContain('status');
        $this->theJsonResponseShouldContain('detail');
    }

    /**
     * @Given I store the response :field as :variable
     */
    public function iStoreTheResponseFieldAs(string $field, string $variable): void
    {
        $data = $this->getJsonResponse();

        if (!isset($data[$field])) {
            throw new RuntimeException(sprintf('Field "%s" not found in response. Available: %s', $field, implode(', ', array_keys($data))));
        }

        $fieldValue = $data[$field];

        if (!is_string($fieldValue)) {
            throw new RuntimeException(sprintf('Field "%s" is not a string', $field));
        }

        $this->storedVariables[$variable] = $fieldValue;
    }

    /**
     * @return array<string, mixed>
     */
    private function getJsonResponse(): array
    {
        if ($this->lastResponseData !== null) {
            return $this->lastResponseData;
        }

        $content = $this->client->getResponse()->getContent();
        /** @var array<string, mixed>|null $data */
        $data = json_decode((string) $content, true);

        if (json_last_error() !== JSON_ERROR_NONE || $data === null) {
            throw new RuntimeException('Response is not valid JSON: ' . $content);
        }

        $this->lastResponseData = $data;

        return $data;
    }

    private function replaceStoredVariables(string $text): string
    {
        $storedVars = $this->storedVariables;
        $result = preg_replace_callback('/stored:(\w+)/', static function (array $matches) use ($storedVars): string {
            $variable = $matches[1];

            if (!isset($storedVars[$variable])) {
                throw new RuntimeException(sprintf('Variable "%s" not stored', $variable));
            }

            return $storedVars[$variable];
        }, $text);

        return $result ?? $text;
    }

    private function castValue(string $value): mixed
    {
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        if ($value === 'null') {
            return null;
        }

        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(string $contentType): array
    {
        return [
            'CONTENT_TYPE' => $contentType,
            'HTTP_ACCEPT' => 'application/ld+json',
        ];
    }
}
