---
name: scan-project
description: Use this skill when the user asks to explore, inspect, understand, map, or analyze a Symfony/API Platform repository before making changes. Trigger on requests like "look at this repo", "understand this project", "map the architecture", or "where should this change go?".
---

# Scan Project

Use this skill to build an implementation-ready map of the repository before coding.

Read first:
- `AGENTS.md`
- `.claude/rules/architecture.md`
- `.claude/rules/testing.md`

Read as needed:
- `README.md`
- `composer.json`
- `Makefile`
- `.claude/patterns.md`

Workflow:
1. Inspect the project shape: `src/`, `config/`, `tests/`, `features/`, `migrations/`.
2. Identify the active conventions: Symfony version, API Platform usage, Docker/FrankenPHP setup, quality gates, and testing stack.
3. Find the nearest local example for the area the user wants to change.
4. Summarize architecture, likely files to touch, and main risks.

Rules:
- Prefer local repository patterns over Symfony defaults.
- Do not invent new layers or folders during the exploration phase.
- Surface any `AGENTS.md` rule that constrains implementation decisions.
