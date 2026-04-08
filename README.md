# INSEE City API

Symfony/API Platform API exposing French city data from the [INSEE API](https://api.insee.fr).

[![Buy Me A Coffee](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/thomaslaure)

## Features

- Symfony console command to fetch and store city data from the INSEE API
- Re-running the command incrementally updates the database with new data
- `GET /api/v1/cities` endpoint with search filters (name, department, region, etc.)
- RFC 7807 compliant error responses

## Tech Stack

- **PHP 8.5** · **Symfony 7.4** · **API Platform 4.x**
- **FrankenPHP** (Caddy-based, worker mode)
- **PostgreSQL 16**
- **Docker** (isolated dev environment)

## Requirements

- Docker + Docker Compose

## Quick Start

```bash
make install
```

The API will be available at **http://localhost:8001/api/v1**.

> **Port note:** ports `8001` and `5433` are used to avoid conflicts with other local services (Signalist on `8000`, local PostgreSQL on `5432`).

## Available Commands

```bash
make up             # Start containers
make down           # Stop containers
make shell          # Enter app container shell
make install        # Full setup: build + start + composer install + migrations

make lint           # PHP CS Fixer
make analyse        # PHPStan (level 9)
make rector         # Rector refactoring
make quality        # All of the above

make tests-unit     # PHPUnit unit tests
make tests-api      # Behat API tests
make tests          # All PHPUnit tests
make grumphp        # Full pre-commit gate

make db-migrate     # Run migrations
make db-reset       # Drop + recreate + migrate
make import         # Run INSEE city import command

make help           # List all commands
```

## Architecture

Hexagonal Architecture + CQRS:

```
src/
├── Domain/City/          # Business logic (Commands, Queries, Handlers, Models)
├── Infrastructure/       # Adapters (Doctrine repositories, INSEE API client)
├── UI/                   # Controllers, Symfony console commands
└── Entity/               # Doctrine entities
```

API routes are prefixed with `/api/v1/`.

## Development

### DevContainer (recommended)

Open the project in VS Code and select **Reopen in Container**. The devcontainer uses Docker-in-Docker to run the full stack in isolation — no port conflicts with host services.

### Code Quality

```bash
make quality    # CS Fixer + PHPStan + Rector
make grumphp    # Full pre-commit check (also runs tests)
```

### Database

```bash
make db-diff    # Generate a migration from entity changes
make db-migrate # Apply pending migrations
make psql       # Open a PostgreSQL shell
```

## CI

GitHub Actions runs on every push and pull request to `main`:

| Job | Checks |
|-----|--------|
| **Tests** | PHPUnit, Behat, coverage ≥ 80%, CS Fixer, PHPStan, Rector, YAML lint, Doctrine schema |
| **Secret Scan** | TruffleHog |
| **Trivy** | CVE scan on prod image + filesystem misconfigurations (CRITICAL/HIGH, unfixed only) |
| **Docker Lint** | Hadolint on both Dockerfiles |
