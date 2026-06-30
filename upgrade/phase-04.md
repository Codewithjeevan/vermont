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

- [ ] `route:list`, `config:cache`, `view:cache` succeed.
- [ ] Characterization tests pass (incl. `ExcelExportTest`).
- [ ] Smoke: **all** Excel exports + imports (flows 13, 15, 16) and a sample PDF (14).

## Exit criteria

App on Laravel 6 LTS / PHP 7.2 with Excel 3.1. Merge, tag `v-l6`.

## Notes / deviations
