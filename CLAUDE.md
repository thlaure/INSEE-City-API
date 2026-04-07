# Agent Project Guide: INSEE City API

---

# 0. TL;DR (Read This First)

- **Project:** Symfony/API Platform API exposing French city data from the INSEE API
- **Architecture:** Hexagonal Architecture + CQRS + API Platform + SOLID + DDD
- **Flow:** `API Platform → State Processor → Handler → Domain Model → Repository`
- **Backend:** PHP 8.5 + Symfony 7.4 + API Platform 4.x + FrankenPHP
- **Database:** PostgreSQL
- **Testing:** PHPUnit + Behat
- **Code Quality:** PHP CS Fixer + PHPStan (level 9) + Rector
- **Process:**
  1. **Explore** context
  2. **Plan** step-by-step & get approval (explain choices)
  3. **Implement** strictly following the plan (narrate actions)
  4. **Verify** (Lint, Analyze, Rector, Test)

## Quick Start
```bash
make install
# Or manually:
docker compose up -d --build
```

---

# 1. Project Overview

**INSEE City API** is a Symfony/API Platform API that:
- Populates a PostgreSQL database with French city data via a Symfony console command (calling the INSEE API)
- Keeps the database up to date by running the import command to ingest new data
- Exposes a GET endpoint to retrieve city data with search filters

## 1.1 Key Features

| Feature | Description |
|---------|-------------|
| **INSEE Import** | Symfony console command to fetch and store city data from the INSEE API |
| **Incremental Updates** | Re-running the command updates the database with new data |
| **City Search** | GET endpoint with search filters (name, department, region, etc.) |

---

# 2. Tech Stack

## 2.1 Backend
- **Language:** PHP **8.5**
- **Framework:** Symfony **7.4** + **API Platform 4.x**
- **Server:** **FrankenPHP** (built on Caddy, worker mode)
- **Architecture:** CQRS, Clean Architecture, Hexagonal (Ports & Adapters)
- **Database:** PostgreSQL
- **External API:** INSEE API (data source)

---

# 3. Commands

| Purpose | Command |
|---------|---------|
| Start containers | `docker compose up -d --build` |
| Code style | `make lint` |
| Static analysis | `make analyse` |
| Code refactoring | `make rector` |
| All quality checks | `make quality` |
| Backend unit tests | `make tests-unit` |
| All checks + tests | `make grumphp` |
| API tests | `docker compose exec app vendor/bin/behat --suite=api` |
| All commands | `make help` |

---

# 4. Architecture & File Structure

## 4.1 CQRS Components

| Component | Responsibility |
|-----------|----------------|
| **Query** | Read intent (GET). Returns DTOs via read models. |
| **Command** | Write intent (POST/PUT/DELETE). Encapsulates user intent. |
| **Handler** | Orchestrates domain logic. **Only place for business logic.** |
| **InputDTO** | Request payload validation (strict constraints). |
| **OutputDTO** | Response shaping (read-only, English field names). |
| **Controller** | HTTP Adapter. Maps Request → InputDTO → Command/Query → Response. |

## 4.2 Directory Structure

```
src/
├── Domain/                  # Business logic (vertical slices)
│   └── City/
│       ├── Command/
│       ├── Query/
│       ├── Handler/
│       ├── DTO/
│       ├── Model/
│       └── Port/            # Repository interfaces
├── Infrastructure/          # Adapters (implementations)
│   ├── Persistence/         # Doctrine repositories
│   └── External/            # INSEE API client
├── UI/                      # Controllers, CLI commands
│   ├── Controller/
│   └── Command/             # Symfony console commands (import)
└── Entity/                  # Doctrine entities
```

## 4.3 Routing Conventions

| Route Type | Prefix | Purpose |
|------------|--------|---------|
| REST API | `/api/v1/` | Main application endpoints |

---

# 5. Code Style & Quality

## 5.1 Standards
- Every file: `declare(strict_types=1);`
- Use PHP 8.5 features: `readonly` classes, constructor promotion
- PSR-12 coding standard
- Explicit, descriptive naming (e.g., `InseeApiClient` not `ApiService`)

## 5.2 Test Naming (PHP)

> **Critical:** PHP CS Fixer enforces **camelCase** method names — no underscores.

Pattern: `test{Method}{Scenario}{Expected}`

```php
// Good
public function testInvokeWithValidCityCodeReturnsCityData(): void
public function testInvokeWithInvalidCodeThrowsNotFoundException(): void

// Bad — PHP CS Fixer will reject
public function testInvoke_ValidCode_ReturnsCityData(): void
```

## 5.3 Quality Tools

| Tool | Purpose | Command |
|------|---------|---------|
| PHP CS Fixer | Code style (PSR-12) | `make lint` |
| PHPStan | Static analysis (level 9) | `make analyse` |
| Rector | Code modernization | `make rector` |
| GrumPHP | Pre-commit gate | `make grumphp` |

---

# 6. Agent Instructions & Boundaries

## 6.0 Communication Style
When working on this project, the agent MUST:
- **Explain choices in real-time:** Before implementing, explain WHY a particular approach is chosen
- **Narrate actions:** Describe what you are doing as you do it
- **Justify technical decisions:** When choosing a pattern, library, or approach, explain the reasoning
- **Highlight trade-offs:** When multiple valid approaches exist, explain the pros/cons
- **Compare alternatives explicitly:** For non-trivial decisions, list at least two alternatives

## 6.1 ALWAYS DO
- Follow CQRS, Hexagonal Architecture, SOLID
- Validate inputs strictly via InputDTOs
- Run `make quality` on generated PHP code
- Write tests for every change (PHPUnit/Behat)
- Use Conventional Commits for git messages

## 6.2 ASK FIRST
- Adding new composer packages
- Changing PostgreSQL schema
- Modifying the INSEE API integration strategy

## 6.3 NEVER DO
- Commit to `master` directly — always create a `feat/` or `fix/` branch and PR
- Hardcode API keys (use environment variables)
- Write business logic in controllers
- Create "god services"
- Add coupling between domains

---

# 7. Exception Handling (RFC 7807)

All API errors follow **RFC 7807 - Problem Details for HTTP APIs**.

## 7.1 Problem Details Format
```json
{
  "type": "https://api.example.com/problems/city-not-found",
  "title": "City Not Found",
  "status": 404,
  "detail": "The city with code 75056 was not found",
  "instance": "/api/v1/cities/75056"
}
```

## 7.2 Problem Types

| Type URI | Title | Status | When |
|----------|-------|--------|------|
| `/problems/validation-error` | Validation Error | 400 | Input validation failed |
| `/problems/not-found` | Resource Not Found | 404 | Entity doesn't exist |
| `/problems/conflict` | Resource Conflict | 409 | Duplicate or conflict |
| `/problems/unprocessable` | Unprocessable Entity | 422 | Business rule violation |
| `/problems/internal-error` | Internal Error | 500 | Unexpected server error |

---

# 8. Testing

Testing is **mandatory**. Target 80%+ coverage on business logic (enforced in CI).

| Type | Purpose | Location |
|------|---------|----------|
| **Unit** | Test Handlers, Domain Models | `tests/Unit/` |
| **API** | Full HTTP flow via Behat | `features/api/` |

**Naming:** `test{Method}{Scenario}{Expected}` (camelCase, no underscores — enforced by PHP CS Fixer)

---

# 9. API Response Formats

## 9.1 Single Resource
```json
{ "code": "75056", "name": "Paris", "department": "75", "region": "11" }
```

## 9.2 Hydra Collection (API Platform resources)
```json
{
  "@context": "/api/v1/contexts/City",
  "@type": "Collection",
  "member": [...],
  "totalItems": 10
}
```

---

# 10. Git Conventions

## 10.1 Branching Strategy
- Branch naming: `feat/<description>`, `fix/<description>`, `refactor/<description>`, `chore/<description>`
- Create PR via `gh pr create` — never commit directly to `master`

## 10.2 Commit Messages (Conventional Commits v1.0.0)

```
feat(city): add INSEE code validation before import
fix(import): correct pagination in INSEE API client
refactor(city): extract search filter logic
test(city): add integration tests for search endpoint
chore(ci): bump actions to Node.js 24
```

**GrumPHP enforces 72-char line limit** on subject and body lines.

---

# 11. Slash Commands

| Command | Use For |
|---------|---------|
| `/new-feature <description>` | Start structured 9-step feature workflow |
| `/bug-fix <description>` | Start structured 8-step bug fix workflow |
| `/hotfix <description>` | Expedited minimal fix for production issues |
| `/review` | Code review of current git diff |
| `/quality` | Run `make quality` + unit tests and report |
| `/simplify` | Clean up recently changed code without changing behavior |
