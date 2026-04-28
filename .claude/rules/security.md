---
paths:
  - "src/**"
  - "config/**"
  - ".env*"
---
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

## AI Tool & MCP Policy

10. **MCP servers are blocked project-wide** (`allowedMcpServers: []` in `settings.json`). Any exception requires explicit team approval and must be added to the `allowedMcpServers` list in `settings.json`.

11. **Do not paste proprietary business logic** into external AI tools or web interfaces (ChatGPT, Copilot Chat, etc.). Use Claude Code locally only.

12. **Sensitive file classes are off-limits for AI context**: `.env`, private keys, and any file matched by `.claudeignore`.

13. **Do not log or expose API keys, tokens, or user data** in AI-generated prompts, comments, or test fixtures.

14. **When in doubt, ask a senior engineer** before using AI assistance on code touching authentication, rate limiting, or external API integrations.
