# Phase 00 — Foundation & Safety Net

**Laravel:** 5.4 (no change) · **PHP:** 7.2.34 · **Branch:** `upgrade/phase-00-foundation`

Goal: establish safety nets, tooling, and a baseline so every later phase is reversible and verifiable.
No framework or app behavior changes in this phase.

> Production already runs on **PHP 7.2** (see [CONVENTIONS.md](CONVENTIONS.md)) — baseline on 7.2,
> not 5.6. Laravel 5.4 runs fine on 7.2.

## Tasks

- [ ] Create branch `upgrade/phase-00-foundation`.
- [x] Create `upgrade/_backups/` and add `/upgrade/_backups/` to `.gitignore`.
- [x] Verify a working DB dump exists (`mysqldump` → `upgrade/_backups/`). See [ROLLBACK.md](ROLLBACK.md).
      Dump: `vermont_db_pre-phase00_20260630-144814.sql` (1.82 MB).
- [x] Tag baseline: `git tag v-baseline`. Branch: `upgrade/phase-00-foundation`.
- [x] Confirm app boots on the production runtime → **PHP 7.2.34** (`artisan --version` = Laravel 5.4.36,
      `route:list` OK).
- [~] Install Rector dev tooling — **DEFERRED to Phase 1** (see Notes). Installing Rector into the
      L5.4 composer tree risks dependency conflicts; it will be installed as an **isolated tool**
      (separate composer project under PHP 8.2) when first needed in Phase 1.
- [~] `rector.php` config — created with the isolated Rector install in Phase 1.
- [x] Build characterization Feature tests — created `tests/Feature/UpgradeSmokeTest.php`
      (app boots, error views render, core routes registered, Common facade resolves). 4/4 green on PHP 7.2.
      The heavier auth/billing/Excel/PDF flows stay in [SMOKE-TESTS.md](SMOKE-TESTS.md) (manual) until a
      dedicated test DB + auth fixtures are wired.
- [x] Capture baseline `route:list` → `upgrade/_backups/routes-baseline.txt`.
- [x] Snapshot `composer.lock` → `upgrade/_backups/composer.lock.baseline`.
- [x] Record baseline row counts → `upgrade/_backups/rowcounts-baseline.txt`
      (billings 5476, billing_items 8648, stocks 8983, purchases 4, customers 1226, users 23).

## Pre-flight bug fixes (safe on 5.4, prevents later pain)

- [x] `app/Privilege.php`: fixed `belongsToMany("App/Role")` → `belongsToMany(\App\Role::class)`
      and `belongsToMany("App/Resource")` → `belongsToMany(\App\Resource::class)` (slash bug).
- [ ] Sync `.env.example` with env vars actually referenced (MAIL_FROM_ADDRESS/NAME, SESSION_DOMAIN,
      SESSION_SECURE_COOKIE, etc.) — documentation only, no behavior change.

## Progress log

- [x] `/upgrade/` folder scaffolded with all plan docs.
- [x] `/upgrade/_backups/` created and git-ignored.
- [x] Privilege namespace slash bug fixed (both `role()` and `resource()`).
- [x] Branch `upgrade/phase-00-foundation` + tag `v-baseline` created.
- [x] DB dump, route-list, composer.lock, row-count baselines captured.
- [x] Characterization smoke test added and green (4/4) on PHP 7.2.34.
- [~] Rector install deferred to Phase 1 (isolated tool).

## Verification

- [x] `php artisan route:list` succeeds (PHP 7.2.34).
- [x] Characterization smoke tests pass on baseline (4/4).
- [ ] Manual smoke flows 1, 3, 13 (login, billing, one Excel report) — run in browser before merge.

## Exit criteria

- Backups + tag in place, Rector deferral noted, characterization tests green,
  tracker updated. Merge to `main`.

## Notes / deviations

- **Baseline PHP is 7.2, not 5.6** — production runtime is 7.2 (client writes PHP-5-style code only).
- **Rector deferred to Phase 1:** avoids polluting/conflicting the L5.4 composer tree; will run as an
  isolated composer project on PHP 8.2 targeting the configured lower PHP/Laravel sets.
- Characterization tests intentionally avoid DB/auth/licence side effects so they stay stable across
  all phases; billing/report/Excel/PDF remain manual (SMOKE-TESTS.md) for now.
- `.env.example` sync still pending (docs-only, no behavior impact) — optional before merge.
