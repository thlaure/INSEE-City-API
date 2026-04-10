Verify a Symfony/API Platform change using the repository's real quality gates.

Verification scope: `$ARGUMENTS`

Workflow:
1. Read `Makefile`, `composer.json`, and `AGENTS.md`.
2. Identify the canonical commands exposed by the repo.
3. Prefer the project wrappers over raw vendor commands.
4. Run the narrowest relevant tests first, then the broader required checks.
5. Report failures with the command, impacted files, and minimal fix direction.

Typical order:
1. `make quality` (runs lint, analyse, rector together)
2. `make tests-unit`
3. `make tests-integration`
4. `make tests`
5. `make tests-api`
6. `make security`

For targeted runs only (skip if running `make quality`):
- `make lint`
- `make analyse`
- `make rector`

Output format:
- `Commands run`
- `Pass/fail summary`
- `Remaining gaps`

Rules:
- If a command is unavailable, say so instead of inventing a substitute.
- If Docker is required, use the project commands or wrappers already defined.
- If a change affects endpoint behavior, do not stop at unit tests only.
- When fixing PHPStan issues, prefer correcting the code, types, or annotations instead of changing `phpstan.neon`.
- Treat edits to `phpstan.neon` as exceptional and ask first unless the user explicitly requested a PHPStan configuration change.
