# INSEE City API

Symfony/API Platform API exposing French city data from the French commune dataset at [geo.api.gouv.fr](https://geo.api.gouv.fr).

## Overview

The project has two distinct concerns:

- write/import flow: fetch commune data from the external API and persist it into PostgreSQL
- read API: expose city search and lookup through API Platform

The write side keeps an explicit `Application` + `Domain` split.
The read side is intentionally more API Platform native: `App\Entity\City` is the API resource and collection filtering uses API Platform Doctrine filters directly.

## Stack

- PHP 8.5
- Symfony 7.4
- API Platform 4.x
- PostgreSQL 16
- FrankenPHP
- Docker / Docker Compose
- PHPUnit, Behat, PHPStan, PHP CS Fixer, Rector

## Quick Start

```bash
make install
make import
```

API entrypoint:

- `http://localhost:8001/api/v1`

Useful local ports:

- app: `8001`
- postgres: `5433`

## Main Commands

```bash
make up
make down
make build
make install

make lint
make analyse
make rector
make quality

make tests-unit
make tests-integration
make tests-api
make tests

make db-migrate
make db-reset
make import
```

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
│   ├── External/
│   └── Persistence/
└── UI/
    └── Command/
```

### Read Side

`GET /api/v1/cities` and `GET /api/v1/cities/{inseeCode}` are API Platform native and backed directly by `App\Entity\City`.

Supported collection filters:

- `name`: partial match
- `exactName`: exact match
- `departmentCode`: exact match
- `regionCode`: exact match

`postalCode` is included in the API response.
Errors support RFC 7807 problem details via `application/problem+json`.

### Write Side

The import command still follows the layered application flow:

`UI Command -> Application Handler -> Domain Model -> Domain Port -> Infrastructure Adapter`

## Import Notes

To populate the database:

```bash
make import
```

The import now runs with a higher CLI memory limit by default and fetches communes department by department to avoid loading the full French dataset into memory in one shot.

## Quality

```bash
make quality
make tests
make tests-api
```

CI runs PHPUnit, Behat, coverage checks, PHPStan, PHP CS Fixer, Rector, YAML lint, security scans, and Docker linting.

## Agent Docs

Project-specific agent instructions live in [`AGENTS.md`](/Users/thomaslaure/Documents/projects/insee-city-api/AGENTS.md).

`CLAUDE.md` is intentionally only a pointer to `AGENTS.md` so Codex and Claude share one canonical instruction file.
