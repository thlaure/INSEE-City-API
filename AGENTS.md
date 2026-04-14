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
- prefer readability and reviewability over premature optimization
- if API Platform already provides a clean built-in solution, use it instead of adding unnecessary layers
- prefer fixing PHPStan issues in code, types, or PHPDoc instead of changing `phpstan.neon`

Execution principles:

- think before coding: state material assumptions explicitly, surface meaningful ambiguity instead of choosing silently, and ask when the ambiguity is risky enough to lead to the wrong implementation
- simplicity first: prefer the minimum implementation that fully satisfies the request; avoid speculative abstractions, configurability, and error handling for scenarios that are not part of the problem
- surgical changes: touch only what is needed for the request and for verification; do not refactor adjacent code unless correctness or the requested change requires it
- clean up only your own mess: remove imports, variables, dead code, or formatting issues made obsolete by your change, but do not opportunistically rewrite unrelated areas
- define success in verifiable terms for non-trivial tasks; prefer checks that prove the requested behavior directly
- bug fix: reproduce first when practical, then verify the fix
- behavior change: add or update tests that demonstrate the intended outcome
- refactor: verify behavior before and after with the relevant test suite

Ask first:

- adding composer packages
- changing PostgreSQL schema
- changing the external city data source strategy
- changing `phpstan.neon`
- running `git commit`
- running `git push`

Never:

- commit directly to `master`, `main`, or `develop`
- hardcode secrets
- put business logic in controllers or framework entrypoints
- create duplicated agent instructions across `AGENTS.md` and `CLAUDE.md`
- run `git commit` or `git push` silently; always ask for confirmation in the current conversation first

## Shared `.claude` Assets

Claude and Codex must both use the repo-local `.claude/` folder as shared operational guidance.

Use these files as the common behavior layer:

- `.claude/settings.json`
- `.claude/rules/architecture.md`
- `.claude/rules/testing.md`
- `.claude/rules/security.md`
- `.claude/patterns.md`

Use the matching workflow when the task fits:

- scan or inspect repository work: `.claude/skills/scan-project/SKILL.md` or `.claude/commands/symfony/scan-project.md`
- new functionality: `.claude/skills/new-feature/SKILL.md` or `.claude/commands/symfony/new-feature.md`
- bug fixing: `.claude/skills/bug-fix/SKILL.md` or `.claude/commands/symfony/bug-fix.md`
- general review: `.claude/skills/review-change/SKILL.md` or `.claude/commands/symfony/review-change.md`
- security review: `.claude/skills/security-review/SKILL.md` or `.claude/commands/symfony/security-review.md`
- verification and checks: `.claude/skills/verify-quality/SKILL.md` or `.claude/commands/symfony/verify-quality.md`
- commit preparation: `.claude/skills/prepare-commit/SKILL.md` or `.claude/commands/symfony/prepare-commit.md`
- instruction improvement: `.claude/skills/improve-instructions/SKILL.md` or `.claude/commands/symfony/improve-instructions.md`
- execution discipline for review, refactor, or ambiguity-heavy tasks: `.claude/skills/karpathy-guidelines/SKILL.md`

Guidance:

- skills and commands are two interfaces for the same workflows; do not let them drift
- prefer skills when the user is speaking naturally
- prefer commands when the user explicitly invokes a named workflow
- rules and patterns are the shared source of truth behind both interfaces
- `.claude/settings.json` is the versioned repository-default settings file for both Claude and Codex
- `.claude/settings.local.json` is only for optional local overrides and must not be treated as the shared team standard

## Instructions Improvement Policy

Instruction files are living documentation and should improve with the project and environment, but only through an explicit proposal-and-confirmation workflow.

Files in scope:

- `AGENTS.md`
- `CLAUDE.md`
- `.claude/rules/*.md`
- `.claude/patterns.md`
- `.claude/commands/symfony/*.md`
- `.claude/skills/*/SKILL.md`

Policy:

- instructions may be improved when there is durable evidence of drift
- examples of drift:
  - repeated corrections or reviewer comments
  - `Makefile`, `composer.json`, or repo-structure changes
  - architecture or testing conventions that changed in practice
  - duplicated or conflicting guidance
- only reusable, stable guidance should be added
- temporary context, one-off fixes, and local anecdotes should not be added to instruction files
- changes to instruction files must be proposed first and applied only after explicit confirmation in the current conversation

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
