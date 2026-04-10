# Security Rules

- Never hardcode secrets, tokens, or credentials.
- Enforce authorization on the server side when access is scoped.
- Validate user-controlled input before database writes or outbound calls.
- Keep outbound requests bounded and safe when user input influences them.
- Minimize response data to the fields actually intended for clients.
- Preserve the repository's existing error-handling strategy instead of leaking internals.
- Add negative-path coverage for forbidden, invalid, or unsafe requests when relevant.
- Do not weaken static-analysis protections to make a warning disappear.
- Prefer fixing PHPStan findings in code, types, or PHPDoc rather than adding ignores or broadening `phpstan.neon`.
