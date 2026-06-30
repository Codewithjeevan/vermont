# Phase 05 — Laravel 6 → 7

**Laravel:** 7.x · **PHP:** 7.4.33 · **Branch:** `upgrade/phase-05-l7`

Official guide: https://laravel.com/docs/7.x/upgrade

## composer.json

- [ ] `laravel/framework` → `^7.0`; `laravelcollective/html` → `^6.1`; `guzzlehttp/guzzle` → `^7.0`.
- [ ] `barryvdh/laravel-dompdf` → `^0.9`/`^1.0`.
- [ ] **Remove `barryvdh/laravel-cors`** — replaced by first-party CORS.
- [ ] `nunomaduro/collision` → `^4` (dev).
- [ ] `composer update`.

## Code changes

- [ ] **Exception Handler signature change (important):** in `app/Exceptions/Handler.php` the methods
      currently type-hint `Exception`. L7 changes them to `Throwable`:
      `report(Throwable $e)`, `render($request, Throwable $e)`. Update the `use` import
      (`use Throwable;`) and both signatures. Verify the custom 404/500 rendering still works.
- [ ] **`Auth::routes()`** (used in `routes/web.php`) still ships in framework core and points at the
      app's existing custom auth controllers — verify it resolves. `laravel/ui` is only required if you
      need to **regenerate** auth scaffolding/views (this app already has them, so likely skip).
- [ ] **First-party CORS:** publish `config/cors.php` (`php artisan config:publish cors` / vendor publish).
      Wire only if API/cross-origin is actually used; otherwise leave defaults.
- [ ] **Symfony 5** under the hood — check any direct Symfony component usage.
- [ ] **Carbon date serialization:** dates now serialize as ISO-8601 in JSON/API responses.
      If any AJAX/report JS depends on the old `Y-m-d H:i:s` format, override `serializeDate()`
      on the affected models or add a global cast. Audit AJAX report endpoints.
- [ ] **Blade components** (new tag syntax) — optional, skip.
- [ ] **`laravel/ui`** for any auth scaffolding views if referenced (custom auth — likely skip).
- [ ] Factory/`Str::uuid`, `Markdown` mail — minor.
- [ ] Run Rector Laravel 7 set; review diffs.

## Verification

- [ ] `route:list`, `config:cache`, `view:cache` succeed.
- [ ] Characterization tests pass.
- [ ] Smoke flows 1–16; **verify AJAX report date rendering** after Carbon serialization change.

## Exit criteria

App on Laravel 7 / PHP 7.4. Merge, tag `v-l7`.

## Notes / deviations
