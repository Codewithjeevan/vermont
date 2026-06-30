# trul-vermont Upgrade — Master Tracker

Upgrade path: **Laravel 5.4 / PHP 5.6 → Laravel 12 / PHP 8.2** using a **strict incremental** strategy
(one Laravel version at a time), with PHP stepped up alongside.

> Plan generated and owned by the upgrade effort. Update the status table after every phase.

## Strategy decisions (locked)

| Decision | Choice |
|---|---|
| Upgrade path | Strict incremental: 5.4 → 5.5 → 5.6 → 5.7 → 5.8 → 6 → 7 → 8 → 9 → 10 → 11 → 12 |
| Laravel Collective forms | **Keep** via a maintained fork (no mass Blade rewrite) — 194 usages |
| Frontend (Vue2/Mix/bower) | **Keep** compiled bundles in `public/js` as static assets — out of scope |
| Git safety | One branch per phase: `upgrade/phase-NN-<name>`, merge to `main` after smoke tests pass |
| DB safety | Backup/dump before each phase; see [ROLLBACK.md](ROLLBACK.md) |
| Automation | Rector (`rector/rector` + `driftingly/rector-laravel`) per phase, diffs reviewed manually |

## Out of scope (separate plan, AFTER `v12-release`)

Blade/frontend **modernization** is intentionally excluded from this framework upgrade — only
*mandatory* Blade fixes (anything that actually breaks under L12) are done here. A separate
"Frontend & Blade Modernization" plan will handle: Bootstrap 3→5, Vue 2→3, mix→Vite, bower removal,
Collective forms → HTML/components, and reconstructing `public/js` source. See [phase-11.md](phase-11.md).
Tooling note: invoke PHP via the full Laragon path (not system PATH) — see repo memory `php-paths.md`.


## Installed PHP versions (Laragon)

`7.0.33`, `7.2.34`, `7.4.33`, `8.1.10`, `8.2.27`, `8.3.29`
(5.6 / 7.1 / 7.3 / 8.0 are **not** installed — each phase uses the nearest installed version that meets the Laravel floor.)

> **Baseline note:** production already runs on **PHP 7.2** (the client writes PHP-5-style code by habit,
> but the server is 7.2). So the ladder starts at 7.2 — there is no real 5.6 runtime to preserve.
> See [CONVENTIONS.md](CONVENTIONS.md).

## PHP ↔ Laravel ladder (using installed versions)

| Phase | Laravel | Min PHP | PHP used |
|---|---|---|---|
| 0 | 5.4 (baseline) | 5.6.4 | 7.2.34 |
| 1 | 5.4 | 7.0 | 7.2.34 |
| 2 | 5.5 | 7.0 | 7.2.34 |
| 3 | 5.6 / 5.7 / 5.8 | 7.1.3 | 7.2.34 |
| 4 | 6.x | 7.2 | 7.2.34 |
| 5 | 7.x | 7.2.5 | 7.4.33 |
| 6 | 8.x | 7.3 | 7.4.33 |
| 7 | 9.x | 8.0 | 8.1.10 |
| 8 | 10.x | 8.1 | 8.1.10 |
| 9 | 11.x | 8.2 | 8.2.27 |
| 10 | 12.x | 8.2 | 8.2.27 |
| 11 | 12.x (optimize) | 8.2 | 8.2.27 |

## Phase status

| Phase | Title | Branch | Status |
|---|---|---|---|
| [00](phase-00.md) | Foundation & Safety Net | `upgrade/phase-00-foundation` | 🟡 Automated done — manual smoke pending |
| [01](phase-01.md) | PHP 5.6 → 7.0 readiness (stay L5.4) | `upgrade/phase-01-php7` | ✅ Done (PHP 7.4, code clean) |
| [02](phase-02.md) | Laravel 5.4 → 5.5 (LTS) | `upgrade/phase-02-l55` | ✅ Done (5.5.50, smoke 5/5) |
| [03](phase-03.md) | Laravel 5.5 → 5.6 → 5.7 → 5.8 | `upgrade/phase-03-l58` | ✅ Done (5.8.38 + Carbon 2, smoke 5/5) |
| [04](phase-04.md) | Laravel 5.8 → 6 + Excel 2.1→3.1 | `upgrade/phase-04-l6` | ✅ Done (6.20.45 + LegacyExcel shim, 7/7) |
| [05](phase-05.md) | Laravel 6 → 7 | `upgrade/phase-05-l7` | ✅ Done (7.30.7, tests 7/7) |
| [06](phase-06.md) | Laravel 7 → 8 | `upgrade/phase-06-l8` | ✅ Done (8.83.29, tests 7/7) |
| [07](phase-07.md) | Laravel 8 → 9 | `upgrade/phase-07-l9` | ✅ Done (9.52.21 / PHP 8.1, tests 7/7) |
| [08](phase-08.md) | Laravel 9 → 10 (`$dates`→`$casts`) | `upgrade/phase-08-l10` | ✅ Done (10.50.2, 17 models cast, 7/7) |
| [09](phase-09.md) | Laravel 10 → 11 (structural skeleton) | `upgrade/phase-09-l11` | ✅ Done (11.54.0 / PHP 8.2, kept skeleton, 7/7) |
| [10](phase-10.md) | Laravel 11 → 12 | `upgrade/phase-10-l12` | ⬜ Not started |
| [11](phase-11.md) | Optimization & Security Hardening | `upgrade/phase-11-hardening` | ⬜ Not started |

Status legend: ⬜ Not started · 🟡 In progress · ✅ Done · ⛔ Blocked

## Per-phase loop (apply every phase)

1. `git checkout -b upgrade/phase-NN-<name>` from previous merged phase.
2. Switch Laragon PHP to the version in the ladder.
3. Update `composer.json` constraints for the target Laravel + packages.
4. `composer update` and resolve dependency conflicts.
5. Run Rector with the matching PHP/Laravel set; **review every diff** before keeping.
6. Apply manual fixes from the official Laravel upgrade guide for that version.
7. `php artisan config:clear route:clear view:clear cache:clear`.
8. Run smoke tests ([SMOKE-TESTS.md](SMOKE-TESTS.md)) + characterization tests.
9. Commit, update this tracker, merge to `main`, tag.
10. Take a fresh DB backup before starting the next phase.

## Reference docs
- [COMPATIBILITY-MATRIX.md](COMPATIBILITY-MATRIX.md) — package versions per Laravel step
- [CONVENTIONS.md](CONVENTIONS.md) — formatting, payment modes & logic that must NOT break
- [ROLLBACK.md](ROLLBACK.md) — git + DB rollback procedure
- [SMOKE-TESTS.md](SMOKE-TESTS.md) — critical-flow manual + automated checklist

## Known high-risk items (carried across phases)
- ~50 `Excel::create()` exports (Maatwebsite 2.1 → 3.1 rewrite) — Phase 4.
- 17 models using removed `protected $dates` → `$casts` — Phase 8.
- `Handler.php` type-hints `Exception` → must become `Throwable` — Phase 5; then migrated to `bootstrap/app.php` — Phase 9.
- Providers/aliases are **manually registered** (not auto-discovered): Excel, Gravatar, Collective\Html, Toastr, DomPDF + `Common`/`Form`/`Html`/`PDF` aliases — must be re-wired in Phase 9.
- `CheckForMaintenanceMode` middleware renamed `PreventRequestsDuringMaintenance` — Phase 6.
- DomPDF facade path `Barryvdh\DomPDF\Facade` → `...\Facade\Pdf` — Phase 7/8.
- `Auth::routes()` stays in framework core; `laravel/ui` only needed to regenerate scaffolding — Phase 5/6.
- L11 slim skeleton (remove `Http/Kernel`, `Console/Kernel`; move to `bootstrap/app.php`) — Phase 9.
- Raw string-interpolated SQL: report controllers **plus** `CustomersController`/`ItemsController` `whereRaw` (SQLi) — Phase 11.
- Raw `curl` + `CURLOPT_SSL_VERIFYPEER=0`: `BillingsController` (×3), `MailController` (×2 SMS), `WelcomeController` (licence) — Phase 11.
- Route closures using the `Common` facade (`/home`, etc.) block `route:cache` — Phase 11.
- 194 Laravel Collective form usages — kept via maintained fork (Phase 8/9 swap if needed).
