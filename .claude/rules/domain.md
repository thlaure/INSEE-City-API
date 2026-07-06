---
paths:
  - "src/Domain/**/*.php"
  - "src/Application/**/*.php"
---

# Domain Code Rules

- `declare(strict_types=1)` is required in every file.
- Prefer `final` and `readonly` where it fits existing code and framework constraints; do not force them blindly.
- Prefer constructor injection.
- Prefer small typed intermediary DTOs or value objects over untyped arrays when the shape is reused, non-trivial, or carries business meaning.
- Repository method signatures must only reference domain models or DTOs; never accept or return HTTP-layer or API resource objects.
- Avoid speculative generalization and utility layers that blur ownership.

## Method naming — readability is a golden rule

Name every method so a reader knows what it does without reading the body.

**Methods that return data** must use a verb that signals both the action and that a value comes back: `get`, `find`, `resolve`, `compute`, `build`, `extract`. If a reader could mistake the method for a side-effectful operation or a property, the name is wrong.
- Bare nouns: forbidden — `cities()` → `getCities()`, `inseeCode()` → `getInseeCode()`
- Side-effect verbs on return methods: forbidden — `apply()`, `handle()`, `process()`, `execute()` imply mutation, not a return value
- Preposition phrases on instance methods: forbidden — `fromX()`, `withX()` are not verbs; use `resolveFromX()`, `buildWithX()`. Exception: `static` named constructors (`fromEntity()`, `fromArray()`).

**Methods that produce a side-effect** use an imperative verb: `save`, `import`, `dispatch`.

**Boolean methods** use `is`, `has`, `can`, `should` — `isKnownInseeCode()`, `hasPostalCode()`.

- Avoid abbreviations unless the term is universally known in the domain (e.g. `insee` is fine).
- Private helpers follow the same rules — they are read as often as public methods.

## Variable naming

Name what it contains, not what it is.
- Avoid `$data`, `$result`, `$response`, `$value` — name the concept: `$cityDto`, `$inseeCodes`
- When multiple IDs of different types are in scope: `$inseeCode`, `$departmentCode`, not `$code`
- Loop variables: `$entry`, `$item` OK in tight loops (≤3 lines); use a descriptive name otherwise

## Type declarations

- Every method must declare a return type — never omit it
- Every parameter must have a type declaration — use `mixed` only when genuinely heterogeneous, and add a `@param` PHPDoc explaining why
- Never use bare `array` without a `@param`/`@return` PHPDoc shape when the structure is known
- Domain code must not leak infrastructure types into its signatures — map DBAL/Doctrine-specific types to domain types at the boundary

## Guard clauses

Prefer early return over nested conditions — flat is better than indented.

```php
// bad
if ($condition) { ...20 lines... }

// good
if (!$condition) { return null; }
...20 lines...
```

## Constants over magic values

Use named constants for any non-trivial literal used in logic: string identifiers, thresholds, system codes.

```php
// bad
if ('75' === $departmentCode)

// good
private const string PARIS_DEPARTMENT_CODE = '75';
if (self::PARIS_DEPARTMENT_CODE === $departmentCode)
```
