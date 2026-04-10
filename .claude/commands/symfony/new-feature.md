Implement a new Symfony/API Platform feature by mirroring the local repository patterns.

User request: `$ARGUMENTS`

Execution order:
1. Run the equivalent of `/symfony:scan-project` if context is incomplete.
2. Find one nearby example in the same area and mirror its structure.
3. Implement the smallest coherent slice that satisfies the request.
4. Write tests in the same session.
5. Run the repository quality gates before reporting completion.

Default expectations unless the repo clearly differs:
- Follow SOLID principles.
- Prefer clean architecture and hexagonal boundaries when the project already uses them.
- If API Platform already provides a direct, readable solution for the requested behavior, use the API Platform feature instead of adding extra layers.
- Symfony entrypoints stay thin.
- Input validation happens at the DTO or request boundary.
- Business logic lives in handlers, use-cases, or domain services.
- Repositories handle persistence only.
- Output shaping is explicit through DTOs, resources, or entity serialization.
- Prefer simple, readable code over clever or highly optimized code.
- If performance and readability conflict and there is no measured bottleneck, choose readability.
- Keep the result easy for a human reviewer to follow.

Checklist:
1. Confirm the target flow: API Platform native, layered write side, or both.
2. Reuse existing naming and file placement conventions.
3. Keep `declare(strict_types=1);` and modern PHP syntax.
4. Add or update validation at the input boundary.
5. Keep exceptions and HTTP error mapping aligned with the existing project.
6. Add the right tests:
   - unit tests for behavior and orchestration
   - integration tests when persistence behavior changes
   - API tests when endpoint behavior changes
7. Verify with the commands exposed by `Makefile`.

Avoid:
- business logic in controllers or framework entrypoints
- new dependencies without explicit approval
- schema changes without explicit approval
- adding hexagonal or CQRS indirection when API Platform can solve the case directly and cleanly
- premature optimization or indirection that hurts readability
- project reshaping when the request only needs a local change
