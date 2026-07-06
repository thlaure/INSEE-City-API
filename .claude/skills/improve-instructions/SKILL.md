---
name: improve-instructions
description: Use this skill when the user asks to improve agent instructions, update Claude guidance, review drift in CLAUDE.md or .claude files, or keep the repo guidance aligned with how the project actually evolves.
---

# Improve Instructions

Use this skill to propose improvements to repository instruction files in a continuous-improvement workflow.

Read first:
- `.claude/rules/architecture.md`
- `.claude/rules/testing.md`
- `.claude/rules/security.md`

Read as needed:
- `.claude/patterns.md`
- `.claude/commands/symfony/*.md`
- `.claude/skills/*/SKILL.md`
- `Makefile`
- `composer.json`
- `README.md`

Workflow:
1. Inspect the repository and current guidance.
2. Identify durable drift between the codebase, workflows, and instruction files.
3. Keep only reusable improvements, not one-off comments or temporary context.
4. Propose exact updates for `CLAUDE.md`, `.claude/rules`, `.claude/patterns.md`, commands, or skills.
5. Present the proposed patch and rationale.
6. Ask for confirmation before applying any edit.

Rules:
- Never edit instruction files silently.
- Treat `CLAUDE.md` as the canonical project-specific source of truth.
- Avoid adding project noise or temporary guidance to shared instruction files.
- Keep commands and skills aligned when a workflow exists in both forms.
