# INSEE City API

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-ffdd00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black)](https://www.buymeacoffee.com/thomaslaure)

Symfony/API Platform API exposing French city data from the French commune dataset at [geo.api.gouv.fr](https://geo.api.gouv.fr).

## Overview

The project has two distinct concerns:

- **Write/import flow**: fetch commune data from the external API and persist it into PostgreSQL via a CLI command.
- **Read API**: expose city search and lookup through API Platform over HTTP.

The write side follows an explicit `Application` + `Domain` layered split.
The read side is intentionally API Platform native: `App\Entity\City` is the API resource and collection filtering uses API Platform Doctrine filters directly.

## Stack

- PHP 8.5
- Symfony 7.4
- API Platform 4.x
- PostgreSQL 16
- FrankenPHP
- Docker / Docker Compose
- PHPUnit, Behat, PHPStan, PHP CS Fixer, Rector, Enlightn Security Checker

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose
- `make`

No local PHP installation is required. Everything runs inside Docker.

## Environment Variables

Copy `.env` to `.env.local` and set the following variables:

| Variable | Description | Example |
|---|---|---|
| `APP_ENV` | Symfony environment | `dev` |
| `APP_SECRET` | Symfony secret key | any random string |
| `DATABASE_URL` | PostgreSQL DSN | `postgresql://insee:insee@postgres:5432/insee_city` |
| `INSEE_API_BASE_URL` | Base URL for the geo API | `https://geo.api.gouv.fr` |
| `CORS_ALLOW_ORIGIN` | Allowed CORS origin (regex) | `^https?://localhost(:[0-9]+)?$` |
| `DEFAULT_URI` | Base URI for CLI-generated URLs | `http://localhost:8001` |

In Docker Compose development, these are already pre-configured in `docker-compose.yml`.

## Quick Start

```bash
make install   # build containers, install dependencies, run migrations
make import    # populate the database from geo.api.gouv.fr (~35 000 communes)
```

API entrypoint: `http://localhost:8001/api/v1`
API documentation (dev only): `http://localhost:8001/api`

Local ports:

| Service | Port |
|---|---|
| App (HTTP) | `8001` |
| PostgreSQL | `5433` |

## API Reference

### Endpoints

| Method | Path | Description |
|---|---|---|
| `GET` | `/api/v1/cities` | Paginated city collection |
| `GET` | `/api/v1/cities/{inseeCode}` | Single city by INSEE code |
| `GET` | `/health` | Health check (DB connectivity) |

### Collection Filters

All filters are optional. Omitting a filter returns all cities.

| Parameter | Type | Match | Example |
|---|---|---|---|
| `name` | string | Partial | `?name=par` |
| `exactName` | string | Exact | `?exactName=Paris` |
| `departmentCode` | string | Exact | `?departmentCode=75` |
| `regionCode` | string | Exact | `?regionCode=11` |

### Pagination

Default page size: 30. Maximum: 1000.

```
GET /api/v1/cities?page=2&itemsPerPage=100
```

### Response Format

City resource fields:

| Field | Type | Notes |
|---|---|---|
| `inseeCode` | string | INSEE commune code, used as identifier |
| `name` | string | City name |
| `departmentCode` | string | Department code |
| `regionCode` | string | Region code |
| `postalCode` | string\|null | First postal code, `null` if unavailable |

Example response (`application/ld+json`):

```json
{
  "@context": "/api/v1/contexts/City",
  "@id": "/api/v1/cities/75056",
  "@type": "City",
  "inseeCode": "75056",
  "name": "Paris",
  "departmentCode": "75",
  "regionCode": "11",
  "postalCode": "75001"
}
```

Errors use RFC 7807 `application/problem+json`.

### Caching

All responses include `Cache-Control: public, max-age=3600`. Reverse proxies and clients can cache responses for up to one hour.

### Rate Limiting

200 requests per minute per IP. Exceeding the limit returns `429 application/problem+json`.

### Health

```
GET /health
```

Returns `{"status":"ok"}` (200) or `{"status":"error","detail":"Database unavailable"}` (503). Intended for readiness/liveness probes.

## Observability

Every request under `/api/` produces a structured JSON log entry on the `api_access` channel.

Log fields: `request_id`, `consumer`, `method`, `path`, `status`, `ip`, `user_agent`, `duration_ms`.

**Consumer identification**: send `X-App-Name: <your-app-name>` on every request. The value is recorded in logs and allows identifying which internal application made each call. This header is voluntary and not enforced.

**Request tracing**: send `X-Request-Id: <id>` to propagate your own trace ID. If absent, one is generated. The value is always echoed back in the response `X-Request-Id` header.

Log destinations:

| Environment | Destination |
|---|---|
| `dev` | `var/log/api_access.log` |
| `prod` | `php://stdout` (JSON) |

## Architecture

```text
src/
├── Application/
│   └── City/
│       ├── DTO/
│       └── Handler/
├── Domain/
│   └── City/
│       ├── Exception/
│       ├── Model/
│       └── Port/
├── Entity/
│   └── City.php            # Doctrine entity + API Platform read resource
├── Infrastructure/
│   ├── External/           # GeoApiClient — fetches data from geo.api.gouv.fr
│   ├── Http/
│   │   └── Listener/       # Request logging (ApiRequestLogListener) and rate limiting (RateLimitListener)
│   └── Persistence/        # DoctrineCityRepository
└── UI/
    ├── Command/             # ImportCitiesCommand
    └── Controller/          # HealthController
```

### Write Flow

```
ImportCitiesCommand → ImportCitiesHandler → City (domain model) → CityRepositoryInterface → DoctrineCityRepository
                                          ↑
                                   GeoApiClient (CityDataProviderInterface)
```

### Read Flow

```
HTTP Request → API Platform → Doctrine ORM → City (entity) → JSON-LD response
```

## Import

```bash
make import
```

Fetches all French communes from `geo.api.gouv.fr` department by department to avoid loading the full dataset into memory at once. Existing records are updated via upsert on `insee_code`.

## Main Commands

```bash
# Docker
make up
make down
make build
make rebuild
make install       # full setup: build + up + composer install + migrations

# Code quality
make lint          # PHP CS Fixer
make analyse       # PHPStan
make rector        # Rector
make quality       # lint + analyse + rector
make security      # Enlightn dependency vulnerability scan

# Tests
make tests-unit
make tests-integration
make tests-api     # Behat
make tests         # all PHPUnit suites

# Database
make db-migrate
make db-reset
make import

# Utilities
make shell         # enter app container
make logs          # tail all container logs
make routes        # list Symfony routes
```

## Agent Docs

Project-specific agent instructions live in [AGENTS.md](AGENTS.md).

`CLAUDE.md` is intentionally only a pointer to `AGENTS.md` so Codex and Claude share one canonical instruction file.
