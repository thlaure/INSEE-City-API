Perform a focused security review for a Symfony/API Platform change.

Review scope: `$ARGUMENTS`

If the scope is omitted, inspect the current git diff.

Security checklist:
1. Authentication:
   - protected routes are explicit
   - anonymous access is intentional
2. Authorization:
   - server-side access checks exist where needed
   - object ownership or tenant boundaries are enforced
3. Input handling:
   - request DTOs or input boundaries validate user input
   - identifiers, enums, URLs, and uploaded data are constrained
4. External interactions:
   - secrets come from configuration, not code
   - outbound calls are bounded and validated
   - user-provided URLs or remote targets are handled safely
5. Data exposure:
   - only intended fields are returned
   - stack traces, tokens, and internal details are not leaked
6. Side effects:
   - unsafe actions use the correct HTTP semantics
   - validation happens before persistence or outbound calls
7. Tests:
   - negative tests exist for forbidden or invalid paths when relevant

Prioritize findings:
- auth or authz bypass
- sensitive data leaks
- unsafe input or external call handling
- missing negative tests on protected behavior

Response format:
- findings first, ordered by severity
- then open questions or assumptions
- then a short hardening summary

Tie every finding to concrete code paths and missing or incorrect enforcement.
