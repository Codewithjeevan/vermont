# Phase 06 — Laravel 7 → 8

**Laravel:** 8.x · **PHP:** 7.4.33 · **Branch:** `upgrade/phase-06-l8`

Official guide: https://laravel.com/docs/8.x/upgrade

## composer.json

- [ ] `laravel/framework` → `^8.0`; `laravel/tinker` → `^2.5`; `laravelcollective/html` → `^6.2`.
- [ ] `barryvdh/laravel-dompdf` → `^1.0`; `maatwebsite/excel` stays `^3.1` (verify L8 tag).
- [ ] `nunomaduro/collision` → `^5`; `phpunit/phpunit` → `^9.3`; `fakerphp/faker` confirmed.
- [ ] `composer update`.

## Code changes

- [ ] **Model factories** are now class-based (`HasFactory` + `database/factories/*Factory.php`).
      This app has no model factories in use → low impact. If seeders use `factory()`, migrate them
      or keep `laravel/legacy-factories` as a temporary bridge.
- [ ] **Route namespace removed:** the `$namespace` auto-prefix in `RouteServiceProvider` is gone by default.
      Either re-add `protected $namespace = 'App\\Http\\Controllers';` and keep string route actions,
      **or** convert routes to `[Controller::class, 'method']`. Re-adding `$namespace` = least churn.
- [ ] **Pagination uses Tailwind by default** → call `Paginator::useBootstrap()` in `AppServiceProvider::boot()`
      (Bootstrap 3 frontend). Verify all report paginators render.
- [ ] **Maintenance middleware renamed:** in `app/Http/Kernel.php` global stack, replace
      `\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class` with
      `\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class`.
- [ ] **`Auth::routes()`:** confirm it still resolves under L8 against the existing custom auth
      controllers. Only add `laravel/ui` if you need to regenerate scaffolding (likely skip).
- [ ] **Models stay in `App\`** (do NOT move to `App\Models`) to avoid touching 43 models + references.
- [ ] `php artisan schema:dump` optional (migration squashing) — skip to keep history.
- [ ] Maintenance mode, queue `retryAfter` → `backoff` — minor.
- [ ] Run Rector Laravel 8 set; review diffs.

## Verification

- [x] `route:list`, `config:cache`, `view:cache` succeed on Laravel 8.83.29 / PHP 7.4.33.
- [x] Characterization tests pass — **7/7**.
- [ ] Manual smoke 1–16 + pagination styling — deferred to final pass.

## Exit criteria

App on Laravel 8 / PHP 7.4. Merge, tag `v-l8`.

## Notes / deviations

**DONE — Laravel 8.83.29 on PHP 7.4.33.**
- `AppServiceProvider::boot()`: added `Paginator::useBootstrap()` (L8 default became Tailwind; app uses Bootstrap 3).
- `app/Http/Kernel.php`: `CheckForMaintenanceMode` → **`PreventRequestsDuringMaintenance`**.
- **Route namespace:** `RouteServiceProvider` already keeps `$namespace = 'App\Http\Controllers'` + explicit `mapWebRoutes`/`mapApiRoutes`, so string controller actions still resolve — no route changes needed. Models kept in `App\`.
- `laravel/ui` → ^3.0; `barryvdh/laravel-dompdf` → ^1.0; `laravel/tinker` → ^2.5; `phpunit` → ^9.3, mockery ^1.3.1.
- Migrated `phpunit.xml` to the PHPUnit 9 schema (`--migrate-configuration`). Security advisories 15 → 13.

## Notes / deviations
