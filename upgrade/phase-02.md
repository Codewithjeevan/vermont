# Phase 02 — Laravel 5.4 → 5.5 (LTS)

**Laravel:** 5.5 · **PHP:** 7.0.33 · **Branch:** `upgrade/phase-02-l55`

Official guide: https://laravel.com/docs/5.5/upgrade

## composer.json

- [ ] `laravel/framework` → `5.5.*`
- [ ] `phpunit/phpunit` → `^6.0`, `mockery/mockery` → `^1.0`
- [ ] `laravelcollective/html` → `^5.5`
- [ ] Keep `maatwebsite/excel ~2.1` (still compatible), `barryvdh/laravel-dompdf ^0.8`.
- [ ] `composer update`.

## Code changes

- [ ] **Package auto-discovery:** remove now-auto-discovered providers/aliases from `config/app.php`
      where the package supports discovery (dompdf, toastr, html, excel as applicable). Verify each still loads.
- [ ] **Exception handler:** `app/Exceptions/Handler.php`
  - `report(Exception $e)` and `render($request, Exception $e)` signatures unchanged but verify
    custom 404/500 rendering still works.
  - `unauthenticated()` already 5.4-style — confirm it matches 5.5 signature.
- [ ] **Validation:** `$this->validate()` now returns the validated data; optional adoption.
- [ ] **`$dates`** still fully supported — no change.
- [ ] **RouteServiceProvider / mapApiRoutes** — confirm `routes/api.php` wiring.
- [ ] Run Rector Laravel 5.5 set; review diffs.

## Verification

- [ ] `route:list`, `config:cache`, `view:cache` succeed.
- [ ] Characterization tests pass.
- [ ] Smoke flows 1–4, 12–15 pass.

## Exit criteria

App on Laravel 5.5 LTS / PHP 7.0. Merge, tag `v-l55`.

## Notes / deviations
