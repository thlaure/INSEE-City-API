Review this Symfony/API Platform repository and produce an implementation-ready map.

User request: `$ARGUMENTS`

Workflow:
1. Read `AGENTS.md`, `CLAUDE.md`, `README.md`, `composer.json`, and `Makefile`.
2. Inspect the project layout before proposing any change:
   - `src/`
   - `config/`
   - `tests/`
   - `features/`
   - `migrations/`
3. Detect the active conventions:
   - Symfony and PHP versions
   - API Platform usage style
   - Docker/FrankenPHP setup
   - layered, CQRS, or API Platform native flows
   - test stack and quality gates
4. Identify the nearest existing pattern for the requested area.
5. Call out project-specific constraints that matter before coding.

Output format:
- `Context`: 4-8 bullets with relevant architecture and tooling facts
- `Existing patterns`: file paths worth mirroring
- `Files likely to change`: exact paths or tight glob patterns
- `Risks`: regressions, hidden coupling, or prerequisites
- `Implementation plan`: short numbered list

Rules:
- Prefer local project patterns over Symfony defaults.
- Do not invent new folders or layers when the repo already has a clear shape.
- Surface any policy in `AGENTS.md` that requires explicit confirmation before changes.
