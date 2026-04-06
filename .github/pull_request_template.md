## Summary

- Describe what changed and why.

## Type of change

- [ ] Feature
- [ ] Bug fix
- [ ] Refactor
- [ ] Migration / schema change
- [ ] Docs only

## Schema-doc checklist (required if `database/migrations/*` changed)

- [ ] I changed one or more files in `database/migrations/*`.
- [ ] I updated `docs/architecture/data-dictionary.md` to match the schema changes.
- [ ] I updated ERD docs in `docs/architecture/erd/` (for example `docs/architecture/erd/mountain_api_erd_v3.html` or the current ERD source file).
- [ ] If any schema doc was not updated, I explained why in "Notes for reviewers".

## Notes for reviewers

- If docs were not updated, explain the reason here.
- Mention any backward-incompatible DB change here.

## Validation

- [ ] I ran `php artisan migrate` successfully.
- [ ] I ran tests locally (`php artisan test`) or explained why not.

