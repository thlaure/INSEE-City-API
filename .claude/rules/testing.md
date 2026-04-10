# Testing Rules

- Tests are part of delivery, not follow-up work.
- Bug fixes should ship with a regression test whenever the behavior can be reproduced in an automated way.
- Choose test scope based on the changed behavior:
  - unit tests for handlers, use-cases, and pure domain logic
  - integration tests for persistence behavior or adapter wiring
  - API tests for endpoint behavior and serialized output
- When endpoint behavior changes, cover both happy path and at least one failure path.
- Keep test naming consistent with the repository convention.
- Run the narrowest relevant tests first, then the broader repository checks.
- If a behavior changes and no test is added or updated, explain why explicitly.
