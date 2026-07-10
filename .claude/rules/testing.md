---
paths:
  - "tests/**"
  - "features/**"
  - "src/**"
---
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

## Paths and naming

- Unit tests: `tests/Unit`
- Integration tests: `tests/Integration`
- API tests: `features/api` (Behat)
- PHPUnit method names must stay camelCase
- Name test methods `test{Method}{Scenario}{Expected}` — e.g. `testInvokeWithEmptyDataReturnsZeroTotals`

## TDD workflow for new behavior

For new endpoints, handlers, or domain logic (not pure bug fixes), write tests before implementation:

1. Draft Behat scenario(s) (happy path + failure path) and unit test method signatures (names only, no bodies).
2. Fill in the unit test bodies. Tests must reference the not-yet-existing behavior — they should fail.
3. Run the tests and confirm they fail (RED) before writing any implementation. A drafted test that already passes proves nothing — fix it before continuing.
4. Implement the feature until the gates pass (GREEN).
5. Refactor with tests kept green — structure only, no behavior change.

For straightforward bug fixes, a regression test written alongside the fix is sufficient; the full RED-first sequence is for new behavior.

## Unit test rules

- Mock the repository/port **interface**, never a concrete class.
- Use intersection types for mock properties: `private CityRepositoryInterface&MockObject $cityRepository`.
- Initialize mocks in `setUp()`, not inside individual test methods.
- One test method per execution path: success + each exception path + edge cases.
- Every test method must assert something — no assertion-less tests.
- Structure tests as Arrange / Act / Assert.
- Tests must be isolated: no shared mutable state between test methods.
- Never use `random_bytes()`, `random_int()`, or `uniqid()` in tests — use fixed values.
- Never use `new \DateTime()`/`time()` without injection — use a fixed timestamp or an injected clock.
- **DAMP over DRY**: each test should be self-contained and readable without tracing shared helpers; duplication across tests is acceptable when it keeps each test independently understandable.
