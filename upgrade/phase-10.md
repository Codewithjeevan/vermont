# Phase 10 — Laravel 11 → 12

**Laravel:** 12.x · **PHP:** 8.2.27 · **Branch:** `upgrade/phase-10-l12`

Official guide: https://laravel.com/docs/12.x/upgrade
L12 is largely a maintenance release — minimal breaking changes once on the L11 slim skeleton.

## composer.json

- [ ] `laravel/framework` → `^12.0`; `laravel/tinker` → `^2.9`+.
- [ ] `phpunit/phpunit` → `^11.x`; `nunomaduro/collision` → `^8`.
- [ ] Bump every remaining package to its **L12-compatible tag**:
  - `barryvdh/laravel-dompdf` → `^3.0`
  - `maatwebsite/excel` → latest L12 tag
  - Collective drop-in fork → L12 tag (confirm; else freeze on L11 tag if compatible)
  - `brian2694/laravel-toastr` → L12 tag, or replace with session-flash + JS toastr if abandoned
  - `thomaswelton/laravel-gravatar` → replace with helper if no L12 tag
  - `guzzlehttp/guzzle` → `^7.8`
- [ ] `composer update`; resolve any final constraint conflicts.

## Code changes

- [ ] Review L12 upgrade guide for the handful of behavioral changes (mostly internal).
- [ ] Re-publish/verify config defaults changed in L12.
- [ ] Run Rector Laravel 12 set; review diffs.
- [ ] Confirm no deprecated API warnings in logs under `APP_DEBUG=true`.

## Verification

- [x] `config:cache`, `view:cache` succeed on Laravel 12.62.0 / PHP 8.2.27.
- [x] Characterization tests pass — **7/7**.
- [ ] `route:cache` fails on closure routes (`/home`, `404`, `500`, `error/{page}`) — fixed in Phase 11.
- [ ] Full manual smoke + every Excel/PDF — deferred to the final pass (Phase 11).

## Exit criteria

App fully on **Laravel 12 / PHP 8.2**, all packages on L12 tags. Merge, tag `v-l12`.

## Notes / deviations

**DONE — Laravel 12.62.0 on PHP 8.2.27. 🎯 TARGET VERSION REACHED.**
- `laravel/framework` → ^12, `laravel/ui` → ^4.6. **No code changes needed** — L12 is a maintenance
  release and the L11 (kept) skeleton carried over cleanly.
- All third-party packages already L12-compatible: `nekhbet/laravel-collective-html` (drop-in fork),
  `barryvdh/laravel-dompdf` ^3, `maatwebsite/excel` ^3.1, `brian2694/laravel-toastr`, `phpunit` ^11.
- `route:cache` still blocked by closure routes using the `Common` facade — converting those to
  controller actions is a Phase 11 task.
