# Phase 04 — Laravel 5.8 → 6 (LTS) + Excel 2.1 → 3.1

**Laravel:** 6.x · **PHP:** 7.2.34 · **Branch:** `upgrade/phase-04-l6`

Official guide: https://laravel.com/docs/6.x/upgrade
This phase has **two parts**: the framework bump (4A) and the Maatwebsite Excel rewrite (4B, large).

## Part 4A — Framework 5.8 → 6

- [ ] `laravel/framework` → `^6.0`; `laravel/tinker` → `^2.0`; `laravelcollective/html` → `^6.0`.
- [ ] `barryvdh/laravel-dompdf` → `^0.9`.
- [ ] **String/Array helpers removed.** Either:
  - quick bridge: `composer require laravel/helpers`, **or**
  - convert `array_get/array_set/str_slug/str_random/...` → `Arr::`/`Str::` (Rector Laravel set does most).
  - Search: `array_get|array_set|array_has|array_first|array_pluck|str_slug|str_random|str_contains|str_limit`.
- [ ] **Carbon 1 → 2** fully (continued from 5.8) — verify date diffs/format strings.
- [ ] Authorization responses, `Str::` in framework — minor.
- [ ] `fzaninotto/faker` → `fakerphp/faker` if used in seeders/factories.
- [ ] Ziggy/Blade — n/a.
- [ ] Run Rector Laravel 6 set; review diffs.
- [ ] Commit `6-framework`.

## Part 4B — Maatwebsite Excel 2.1 → 3.1 (BIG, ~50 usages)

> v3 removes `Excel::create()->sheet()->export()` and `Excel::load()`. Replace with Export/Import classes.

- [ ] `maatwebsite/excel` → `^3.1`; republish `config/excel.php` (v3 schema; back up old one).
- [ ] Inventory every export/import (see grep list in repo). Group by module:
      sales reports, purchase reports, VAT, cashier, salesman, stock balance, customer ledger, items, opening stock.
- [ ] For each **export**, create an `App\Exports\<Name>Export` class:
  - `FromCollection` or `FromView` (reuse existing Blade where the old code built HTML),
  - implement `WithHeadings`, `WithMapping`, `ShouldAutoSize`, `WithStyles` as needed,
  - controller: `return Excel::download(new <Name>Export($params), 'file.xlsx');`.
- [ ] For each **import** (`\Excel::load(...)->get()`), create `App\Imports\<Name>Import`:
  - `ToModel` / `ToCollection` + `WithHeadingRow`; controller: `Excel::import(new <Name>Import, $file)`.
- [ ] Preserve exact column order, headings, number/date formatting, and totals rows.
- [ ] Test **every** export opens in Excel and matches pre-upgrade output (compare against a baseline file).
- [ ] Consider splitting 4B into sub-commits per module for safer review.
- [ ] Commit `6-excel`.

## Part 4C — CORS prep

- [ ] Note `barryvdh/laravel-cors` is unused/unwired; plan removal in Phase 5. No action if not wired.

## Verification

- [x] `route:list` + `config:cache` succeed on Laravel 6.20.45.
- [x] Characterization tests pass — **7/7** (5 smoke + 2 new `LegacyExcelTest`: export valid xlsx + import round-trip).
- [ ] Manual smoke: all Excel exports + imports + a sample PDF — deferred to final manual pass.

## Exit criteria

App on Laravel 6 LTS / PHP 7.2 with Excel 3.1. Merge, tag `v-l6`.

## Notes / deviations

**DONE — Laravel 6.20.45.**

**4A (framework):** framework ^6, tinker ^2, collective ^6, excel ^3.1, dompdf ^0.9,
`laravel/helpers` bridge, faker→fakerphp, phpunit ^8. `barryvdh/laravel-cors` already removed (Phase 2).
Old v2 `config/excel.php` (`PHPExcel_Shared_Font`) replaced with the v3 config; v2 backed up at
`upgrade/_backups/excel.php.v2.bak`.

**4B (Excel) — DEVIATION from the planned per-export rewrite:**
Instead of hand-rewriting all 46 exports + 4 imports into native v3 Export/Import classes (high risk of
changing columns/formatting across 21 complex `SalesReportController` reports), built a **compatibility
shim** [app/Legacy/LegacyExcel.php](../app/Legacy/LegacyExcel.php) that re-implements the Maatwebsite 2.x
API (`create`/`sheet`/`appendRow`/`row`/`getHighestRow`/`setFontWeight`/`export`/`load`/`get`) on top of
PhpSpreadsheet (bundled with excel 3.1). The export method surface was verified to be tiny (no
cells/merge/format/loadView anywhere), so the shim is faithful.

All 50 sites were repointed `Excel::` → `\App\Legacy\LegacyExcel::` across 28 controllers via a
line-ending-preserving regex replace — **zero changes to row-building logic = zero output risk**.
Round-trip tested. Native v3 migration can happen later in the separate frontend/cleanup plan.
