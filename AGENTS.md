# Agent Guide

Canonical agent instructions for this repository live in this file.

`CLAUDE.md` must stay a thin pointer to `AGENTS.md` so Claude and Codex share one source of truth.

## Project

- Symfony/API Platform API for French city data
- PHP 8.5, Symfony 7.4, API Platform 4.x, PostgreSQL, FrankenPHP
- Primary workflows: import commune data and expose read-only city search/lookup

## Architecture

- Use Hexagonal Architecture and Clean Architecture principles pragmatically
- Keep `Application` for use-case orchestration
- Keep `Domain` for business models, domain exceptions, and ports
- Keep `Infrastructure` for Doctrine/API clients/runtime adapters
- Keep `UI` for entrypoints such as Symfony console commands

Current rule for this project:

- write/import flow remains layered and explicit
- read API is intentionally more API Platform native and is backed directly by `App\Entity\City`

## Current Flow

Write/import flow:

- `UI Command -> Application Handler -> Domain Model -> Domain Port -> Infrastructure Adapter`

Read flow:

- `API Platform -> Doctrine ORM -> Entity resource`

## Repository Structure

```text
src/
├── Application/City/
├── Domain/City/
├── Entity/
├── Infrastructure/
│   ├── External/
│   ├── Http/Listener/      # ApiRequestLogListener, RateLimitListener
│   └── Persistence/
└── UI/
    ├── Command/
    └── Controller/         # HealthController
```

## Engineering Rules

Always:

- keep `declare(strict_types=1);`
- prefer explicit naming
- write tests for behavior changes
- run verification after changes
- preserve API version prefix `/api/v1`

Ask first:

- adding composer packages
- changing PostgreSQL schema
- changing the external city data source strategy

Never:

- commit directly to `master` or `main`
- hardcode secrets
- put business logic in controllers or framework entrypoints
- create duplicated agent instructions across `AGENTS.md` and `CLAUDE.md`

## API

### Search

`GET /api/v1/cities` and `GET /api/v1/cities/{inseeCode}` are API Platform native.

Supported filters:

- `name`: partial match
- `exactName`: exact match
- `departmentCode`: exact match
- `regionCode`: exact match

`postalCode` is `?string` — `null` when no data exists, never an empty string.

### Health

`GET /health` — checks DB connectivity. Returns 200 or 503. Not under `/api/v1`.

### Observability

- Every `/api/` request is logged (channel `api_access`, JSON) with: `request_id`, `consumer`, `method`, `path`, `status`, `ip`, `user_agent`, `duration_ms`.
- Consumers should send `X-App-Name` header (sanitized, not enforced).
- `X-Request-Id` is generated if absent and echoed in the response.

### Rate Limiting

200 req/min per IP via `symfony/rate-limiter` (sliding window). Returns `429 application/problem+json`.

## Quality Gates

Run when relevant:

- `make lint`
- `make analyse`
- `make rector`
- `make tests-unit`
- `make tests-integration`
- `make tests-api`
- `make security`

Preferred full verification:

- `make quality`
- `make tests`
- `make tests-api`
- `make security`

## Testing Notes

- Unit tests: `tests/Unit`
- Integration tests: `tests/Integration`
- API tests: `features/api`
- PHPUnit method names must stay camelCase

## Documentation Policy

Use this split:

- `README.md`: human-facing project overview and usage
- `AGENTS.md`: canonical agent instructions
- `CLAUDE.md`: pointer file only

If agent instructions need to change:

1. update `AGENTS.md`
2. keep `CLAUDE.md` minimal and referential
3. update `README.md` only for human-facing behavior or workflow changes
