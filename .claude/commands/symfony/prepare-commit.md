Prepare a commit for the current changes using Conventional Commits and repo-aware verification notes.

Scope or context: `$ARGUMENTS`

Workflow:
1. Inspect `git status`, the current branch, and the current diff.
2. Stage the intended files with `git add`.
3. Run the repository quality checks relevant to the current change, using the real project gates from `Makefile`.
4. If the current branch is `main`, `master`, `develop`, or another protected/shared branch, create a dedicated branch before committing.
5. Build a Conventional Commit message that matches the actual change scope.
6. Prepare PR-ready notes that explain:
   - what changed
   - why it changed
   - how it was implemented
   - what was verified
   - remaining risks or follow-up points
7. Prepare a verification checklist based on the real project gates from `Makefile`.
8. If the user explicitly asks to commit, ask for confirmation before running `git commit`.
9. If the user explicitly asks to push, ask for confirmation before running `git push`.

Rules:
- Never commit or push silently.
- Treat `git commit` and `git push` as confirmation-required actions even when the user previously asked for preparation work.
- Stage only the files that belong to the intended change.
- Prefer a short, accurate Conventional Commit title over a clever one.
- Do not invent verification steps that do not exist in the repository.
- If quality, review, or security checks reveal blockers, surface them before proposing the final commit.

Output format:
- `Pre-commit checks`
- `Suggested branch`
- `Suggested commit title`
- `Suggested commit body`
- `PR notes`
- `Verification checklist`
