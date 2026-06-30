# Phase 11 — Optimization & Security Hardening

**Laravel:** 12.x · **PHP:** 8.2.27 · **Branch:** `upgrade/phase-11-hardening`

Final phase. Now that the app runs on L12/PHP 8.2, fix the deferred security and performance debt.
Do these as **independent, individually-tested commits** so any single change can be reverted.

## Security

- [ ] **SQL injection — parameterize raw SQL.** Report controllers interpolate `$fromDate`/`$toDate`/
      user values directly into `DB::select("... BETWEEN \"$fromDate\" ...")`. Convert to bindings:
      `DB::select('... BETWEEN ? AND ?', [$fromDate, $toDate])`.
      Files (heaviest first): `SalesReportController`, `DashboardController`, `VatReportController`,
      `CashierReportController`, `PurchaseReportController`, `PurchaseReturnReportController`,
      `SalesmanReportController`, `CustomerLedgersController`, `CustomerReceiptsController`,
      `DamageStockController`, `ItemsController`, `PaymentsController`, `StockBalanceCategorywiseController`,
      `BillingsController`.
- [ ] **Also parameterize interpolated `->whereRaw(...)`** (separate from the report `DB::select`s):
      `CustomersController` birthday/anniversary filters (~L747–793, interpolates `$type`/`$filter`/
      `$month`/`$date`/`$todayDate`) and `ItemsController` (~L1398–1470, interpolates `$intId`).
      Use binding params; output must stay identical.
- [ ] **Re-enable TLS verification.** Remove `CURLOPT_SSL_VERIFYPEER=0` / `CURLOPT_SSL_VERIFYHOST=0`
      in `BillingsController` (Planet API @ ~L1038, L1582, L3543), `MailController` (SMS @ ~L491, L527),
      and `WelcomeController` (licence).
- [ ] **Migrate raw `curl` → Laravel HTTP client (Guzzle 7).** Replace `curl_init` blocks with
      `Http::withToken(...)->post(...)` for Planet Tax Refund, SMS gateway, and licence server.
      Keep request/response shapes identical; verify against sandbox.
- [ ] Move hardcoded endpoints/keys to `.env` / `config/custom.php`.
- [ ] Confirm `APP_DEBUG=false` in production env; verify custom 404/500 pages.

## Performance

- [ ] **N+1 audit:** add eager loading (`with(...)`) on list/report pages (items, bills, purchases,
      customer ledgers). Use Laravel Debugbar or `DB::listen` in dev to find N+1s.
- [ ] **Caching:** `php artisan config:cache route:cache view:cache event:cache` in deploy;
      ensure no closures break route caching.
  - **Blocker:** `routes/web.php` defines closures that call the `Common` facade (`/home` role
    redirect, licence/error closures). `route:cache` fails on closure routes — convert these closures
    to controller actions (e.g. a `HomeController@redirect`) before enabling route caching.
- [ ] **OPcache** enabled in PHP 8.2 (php.ini) — verify.
- [ ] **Indexes:** review hot report queries' `WHERE`/`JOIN` columns (dates, barcode, billNo,
      customerId) and add missing indexes via a new migration.
- [ ] Composer autoloader: `composer install --optimize-autoloader --no-dev` for production.

## Cleanup

- [ ] Remove dead packages (`barryvdh/laravel-cors` already gone; `bower.json` if frontend untouched
      and unused — confirm first).
- [ ] **Blade `{{{ $x }}}` (126 occurrences) — conditional:** during Phase 9/10 verification, test the
      pages that use triple-curly. If L12 Blade renders them literally or errors, do the mechanical
      `{{{ $x }}}` → `{{ $x }}` replace (escaped output is identical) and smoke-check a sample. If they
      still render correctly, **leave them** — cosmetic normalization belongs to the separate frontend
      plan (see below), not this upgrade.
- [ ] Remove `laravel/helpers` bridge if all `array_*`/`str_*` helpers were converted to `Arr`/`Str`.
- [ ] Remove leftover commented code / old config keys.

## Out of scope — defer to a separate "Frontend & Blade Modernization" plan

These are intentionally NOT done in this framework upgrade (they add risk without upgrade value and
need their own discovery — the frontend source is missing, only `public/js` bundles exist):

- Bootstrap 3 → 5, Vue 2 → 3, `laravel-mix` → Vite, remove `bower.json`.
- Rewrite Laravel Collective forms → plain HTML / Blade form components.
- Convert Blade includes/sections → `<x-component>` components.
- Reconstruct `public/js/app.js` source.

Do this as a fresh plan AFTER `v12-release` is stable in production.

## Final regression

- [ ] Run the **entire** [SMOKE-TESTS.md](SMOKE-TESTS.md) checklist (all 18 flows + full export/PDF matrix).
- [ ] Full POS cycle end-to-end + Planet tax-refund sandbox + every report export.
- [ ] Compare core table row counts and a sample of report outputs against the baseline.
- [ ] Load-check the dashboard and heaviest reports for acceptable response time.

## Exit criteria

L12 / PHP 8.2, no interpolated SQL, TLS verified, curl→Http migrated, caches green, smoke 100% pass.
Merge, tag **`v12-release`**.

## Notes / deviations

### ✅ Completed (safe, verified)
- **composer.json** reformatted; `nekhbet/laravel-collective-html` pinned `^1.0`. **Security advisories 51 → 0.**
- **Closure routes → `PageController`** (`/home`, `404`, `500`, `error/{page}`, `test`) and removed the
  unused api `/user` closure → **`php artisan route:cache` now works** (was blocked by closures).
- **SQLi parameterized (contained, tested):**
  - `CustomersController` birthday/anniversary methods — whitelisted the `$type` **column name**
    (`birthday`/`anniversary`) and bound `$filter`/`$todayDate`/`$month`/`$date` values.
  - `ItemsController::searchItem` / `searchBarcode` — bound all `$request`-sourced values in the
    `CASE WHEN` `whereRaw` clauses (exact logic preserved).
- **Full optimize cache cycle verified:** `config:cache` + `route:cache` + `view:cache` + `optimize`
  all succeed on L12. Test suite 9/9.

### ⚠️ Remaining hardening — REQUIRES staged testing against real data/endpoints (NOT done blind)
These are genuine improvements but cannot be verified without the live DB/integration, and doing them
blind risks regressions (violates the "bug-free" requirement). Recommend a dedicated, separately-tested
pass:
1. **Report-controller raw SQL audit:** hundreds of interpolated `DB::select("... '$fromDate' ...")`
   across `SalesReportController` (~4400 lines, 21 reports), `VatReportController`,
   `CashierReportController`, `DashboardController`, `PurchaseReportController`, etc. Each must be
   converted to bindings and its output diffed against a baseline export — a large, data-dependent task.
2. **Re-enable TLS + curl→Http client:** `CURLOPT_SSL_VERIFYPEER=0` in `BillingsController` (Planet tax
   API ×3), `MailController` (SMS ×2), `WelcomeController` (licence). Flipping these on **live
   payment/SMS/licence flows** must be tested against each endpoint (a self-signed cert would break
   login/billing), so it should be staged, not done blind.
3. **N+1 eager loading** — needs query profiling (Debugbar/`DB::listen`) against real data.

### Deferred to the separate "Frontend & Blade Modernization" plan
- `laravel/helpers` bridge kept (no global `array_*`/`str_*` usage found in `app/`, but a view might use
  one — left in place to avoid risk).
- `{{{ }}}` Blade normalization (renders fine on L12 — cosmetic only).
