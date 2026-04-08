# Commit Convention

This document defines commit message rules so the project history stays clear, searchable, and easier to review.

## Commit Message Format

Use this format:

`<type>: <short, clear message>`

Example:

`feat: add mountain list endpoint`

Optional format if module context is useful:

`<type>(<scope>): <short, clear message>`

Example:

`fix(auth): correct login validation`

## Commit Types

- `feat` = add a new feature.
- `fix` = fix a bug.
- `refactor` = improve internal code structure without changing feature behavior.
- `chore` = routine maintenance or cleanup (for example, removing unused code).
- `docs` = documentation-only changes.
- `build` = dependency updates or build/compile configuration changes.
- `test` = add or update test cases (unit, feature, integration, and others).

## Good Commit Practices

- Keep one commit focused on one clear purpose.
- Write short, specific messages that explain what changed.
- Avoid vague messages such as `update` or `fix bug`.
- Split large changes into smaller commits so reviews stay manageable.

## How to Push

1. Create a new branch from the latest working branch.
2. Stage relevant changes.
3. Commit using the convention above.
4. Push the branch to remote.
5. Open a Pull Request on GitHub.

```bash
git checkout -b <new-branch-name>
git add .
git commit -m "<use-convention-above>"
git push -u origin <new-branch-name>
```

## Pull Request Notes

- Make sure the PR description explains the goal of the change.
- Highlight the main impact (feature, bugfix, schema, etc.).
- When possible, attach test evidence (test results or screenshots) before merge.
