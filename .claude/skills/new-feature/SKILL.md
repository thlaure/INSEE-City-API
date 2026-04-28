---
name: new-feature
description: Use this skill when the user asks to add, create, implement, or build new functionality in a Symfony/API Platform project. Trigger on requests like "add an endpoint", "implement a feature", "create a command", or "build this behavior".
---

# New Feature

Use this skill to implement new functionality while staying aligned with the local project architecture.

Read first:
- `AGENTS.md`
- `.claude/rules/architecture.md`
- `.claude/rules/testing.md`
- `.claude/rules/security.md`

Read as needed:
- `.claude/patterns.md`
- nearby files in the same domain or flow

Workflow:
1. If acceptance criteria were not provided, ask for them before doing anything else. Do not proceed until the user supplies them.
2. If context is incomplete, inspect the repo shape and find a nearby example first.
2. Mirror the local naming, placement, and flow instead of generating framework-default code.
3. Follow SOLID pragmatically and keep clean or hexagonal boundaries readable.
4. If API Platform already supports the requested behavior cleanly, use the API Platform feature directly instead of adding extra layers.
5. Keep the implementation simple and easy for a human reviewer to follow.
6. **TDD — tests before implementation, in three checkpoints:**
   - **Checkpoint 1**: Write Behat scenario(s) covering the expected API behavior. Present to user. Wait for approval.
   - **Checkpoint 2**: Write unit test function signatures (method names + arrange/act/assert skeleton, no bodies). Present to user. Wait for approval.
   - **Checkpoint 3**: Fill in unit test bodies. Present to user. Wait for approval.
   - **Only after all three checkpoints are approved**: implement the feature code to make the tests pass.
7. Run the repository quality gates before reporting completion.

Rules:
- Keep entrypoints thin.
- Keep business rules in handlers, use-cases, or domain services.
- Keep repositories focused on persistence.
- Prefer readability over premature optimization.
