# Phase 00 — Foundation & Safety Net

**Laravel:** 5.4 (no change) · **PHP:** 7.2.34 · **Branch:** `upgrade/phase-00-foundation`

Goal: establish safety nets, tooling, and a baseline so every later phase is reversible and verifiable.
No framework or app behavior changes in this phase.

> Production already runs on **PHP 7.2** (see [CONVENTIONS.md](CONVENTIONS.md)) — baseline on 7.2,
> not 5.6. Laravel 5.4 runs fine on 7.2.

## Tasks

- [ ] Create branch `upgrade/phase-00-foundation`.
- [ ] Create `upgrade/_backups/` and add `/upgrade/_backups/` to `.gitignore`.
- [ ] Verify a working DB dump exists (`mysqldump` → `upgrade/_backups/`). See [ROLLBACK.md](ROLLBACK.md).
- [ ] Tag baseline: `git tag v-baseline`.
- [ ] Confirm app boots on the production runtime → **PHP 7.2.34** (Laravel 5.4 supports 7.0–7.1
      officially but runs on 7.2 in production today).
- [ ] Install Rector dev tooling (kept until end of upgrade):
      `composer require rector/rector driftingly/rector-laravel --dev` (use a tag compatible with current PHP).
      If it cannot install on the 5.4 stack, defer Rector install to Phase 2 and note here.
- [ ] Add a minimal `rector.php` config (paths: `app`, `database`, `routes`, `config`).
- [ ] Build characterization Feature tests listed in [SMOKE-TESTS.md](SMOKE-TESTS.md):
      `LoginTest`, `BillingStoreTest`, `SalesReportTest`, `ExcelExportTest`, `PdfExportTest`.
- [ ] Capture baseline `php artisan route:list` output to `upgrade/_backups/routes-baseline.txt`.
- [ ] Snapshot `composer.lock` → `upgrade/_backups/composer.lock.baseline`.
- [ ] Record baseline row counts of core tables to `upgrade/_backups/rowcounts-baseline.txt`.

## Pre-flight bug fixes (safe on 5.4, prevents later pain)

- [x] `app/Privilege.php`: fixed `belongsToMany("App/Role")` → `belongsToMany(\App\Role::class)`
      and `belongsToMany("App/Resource")` → `belongsToMany(\App\Resource::class)` (slash bug).
- [ ] Sync `.env.example` with env vars actually referenced (MAIL_FROM_ADDRESS/NAME, SESSION_DOMAIN,
      SESSION_SECURE_COOKIE, etc.) — documentation only, no behavior change.

## Progress log

- [x] `/upgrade/` folder scaffolded with all plan docs.
- [x] `/upgrade/_backups/` created and git-ignored.
- [x] Privilege namespace slash bug fixed (both `role()` and `resource()`).
- [ ] Remaining: branch, DB dump, `v-baseline` tag, Rector install, characterization tests,
      route/rowcount baselines — **require PHP switch to 7.0 + composer ops (awaiting go-ahead).**

## Verification

- [ ] `php artisan route:list` succeeds.
- [ ] All 5 characterization tests pass on baseline.
- [ ] Manual smoke flows 1, 3, 13 (login, billing, one Excel report) pass.

## Exit criteria

- Backups + tag in place, Rector available (or deferral noted), characterization tests green,
  tracker updated. Merge to `main`.

## Notes / deviations

> Record anything that differed from the plan here during execution.
