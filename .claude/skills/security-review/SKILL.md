---
name: security-review
description: Use this skill when the user asks for a security review, mentions auth/authz concerns, input validation, secret handling, SSRF, unsafe external calls, or wants a stricter review of a Symfony/API Platform change.
---

# Security Review

Use this skill to review a change with a security-first lens.

Read first:
- `AGENTS.md`
- `.claude/rules/security.md`
- `.claude/rules/architecture.md`
- `.claude/rules/testing.md`

Read as needed:
- `.claude/patterns.md`
- the relevant diff and nearby files

Workflow:
1. Inspect authentication and authorization boundaries.
2. Check input validation before side effects.
3. Check secrets, outbound calls, and user-controlled remote targets.
4. Check output exposure and error leakage.
5. Check negative-path tests for forbidden or invalid behavior.

Response format:
- findings first, ordered by severity
- then assumptions or open questions
- then a short hardening summary

Rules:
- Tie each finding to a concrete code path.
- Prefer actionable security findings over generic warnings.
