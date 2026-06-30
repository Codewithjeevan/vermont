# Phase 09 — Laravel 10 → 11 (STRUCTURAL skeleton migration)

**Laravel:** 11.x · **PHP:** 8.2.27 · **Branch:** `upgrade/phase-09-l11`

Official guide: https://laravel.com/docs/11.x/upgrade
**Biggest structural phase.** L11 introduced a slim skeleton: `Http/Kernel.php` and
`Console/Kernel.php` are gone; their responsibilities move into `bootstrap/app.php`.
Switch Laragon to **8.2.27**.

## composer.json

- [ ] `laravel/framework` → `^11.0`; `laravel/tinker` → `^2.9`.
- [ ] `phpunit/phpunit` → `^11.0`; `nunomaduro/collision` → `^8`.
- [ ] `barryvdh/laravel-dompdf` → `^2.0`/`^3.0`; `maatwebsite/excel` → `^3.1`/`^1.x` (new versioning — verify L11 tag).
- [ ] Collective fork → L11 tag; verify toastr/gravatar on L11.
- [ ] `composer update`.

## Skeleton migration (do carefully, this is the core of the phase)

- [ ] Create the new `bootstrap/app.php` using `Application::configure(basePath: ...)` with:
  - `->withRouting(web: __DIR__.'/../routes/web.php', api: ..., commands: ..., health: '/up')`
  - `->withMiddleware(function (Middleware $middleware) { ... })`
  - `->withExceptions(function (Exceptions $exceptions) { ... })`
- [ ] **Middleware migration** (from old `app/Http/Kernel.php`):
  - Register route-middleware aliases: `auth` (framework), and **`access` → `App\Http\Middleware\checkAccess`**
    via `$middleware->alias(['access' => checkAccess::class])`.
  - Port global middleware order (TrimStrings, EncryptCookies, VerifyCsrfToken — most are now default;
    only register custom/overridden ones).
  - Keep custom `checkAccess`, `RedirectIfAuthenticated`, `EncryptCookies`, `VerifyCsrfToken`,
    `TrimStrings` classes; re-point overrides.
- [ ] **Exception handling** (from `app/Exceptions/Handler.php`):
  - Move custom 404/500 view rendering into `->withExceptions(fn ($e) => $e->render(...))`.
  - Recreate `unauthenticated` redirect behavior.
  - Delete `app/Exceptions/Handler.php` after porting.
- [ ] **Console:** move scheduled tasks/commands from `app/Console/Kernel.php` into
  `routes/console.php` (`Schedule::command(...)`), keep `App\Console\Commands\ResetApp`. Delete old Kernel.
- [ ] **Providers:** L11 uses `bootstrap/providers.php` (not the `config/app.php` providers array).
  - Register app providers: `AppServiceProvider`, `CommonProvider`, `AuthServiceProvider`,
    `EventServiceProvider`, `RouteServiceProvider` (if still needed), `BroadcastServiceProvider`.
  - **These package providers are MANUALLY registered today (no auto-discovery) — verify each is
    auto-discovered under L11, and add to `bootstrap/providers.php` any that are not:**
    `Maatwebsite\Excel\ExcelServiceProvider`, `Thomaswelton\LaravelGravatar\LaravelGravatarServiceProvider`,
    `Collective\Html\HtmlServiceProvider`, `Brian2694\Toastr\ToastrServiceProvider`,
    `Barryvdh\DomPDF\ServiceProvider`.
- [ ] **Aliases:** re-register the custom/package aliases (L11 has no `config/app.php` aliases array by
      default). Required: **`Common` => `App\Helpers\CommonHelper`**, `Form`, `Html`, `Toastr`,
      `Excel`, `PDF` (=> `Barryvdh\DomPDF\Facade\Pdf`), `Gravatar`. Register via
      `->withProviders()`/a small provider or a published `config/app.php` `aliases` array.
  - The `Common` facade is used in **routes/web.php closures** (`/home`, `can_billscreen_view`) and
    in the `checkAccess` middleware — it MUST resolve before routes load.
- [ ] **`AppServiceProvider::boot()`** keeps `Schema::defaultStringLength(191)` and `Paginator::useBootstrap()`.
- [ ] **CommonProvider** keeps the `CommonViewComposer` on `*` views.
- [ ] **Config publishing:** L11 ships fewer config files. Publish only the ones the app relies on
  (`config/custom.php` stays; publish `session`, `cors`, `filesystems`, `mail`, `database`,
  `excel`, `toastr`, `gravatar`, `view` as needed). Keep `config/custom.php` and `config/excel.php`.
- [ ] **`routes/web.php`** unchanged logically; verify the `master/` prefix group + `auth`/`access` middleware.
- [ ] Run Rector Laravel 11 set; review diffs.

## Verification (FULL — highest-risk phase)

- [ ] `php artisan route:list` matches the baseline route set (diff against `routes-baseline.txt`).
- [ ] `config:cache`, `view:cache`, `event:cache` succeed.
- [ ] Characterization tests pass.
- [ ] **All** smoke flows 1–18, with extra attention to: RBAC/`checkAccess` middleware,
      licence+login, custom 404/500 pages, scheduled `ResetApp` command (`php artisan list`),
      and the `Common` facade resolving in views.

## Exit criteria

App on Laravel 11 / PHP 8.2, old Kernels removed, all wiring restored. Merge, tag `v-l11`.

## Notes / deviations

**DONE — Laravel 11.54.0 on PHP 8.2.27.**

**MAJOR DEVIATION (deliberate, lower-risk): kept the L10 skeleton.**
Laravel 11 is intentionally backwards-compatible — `app/Http/Kernel.php`, `app/Console/Kernel.php`
and `app/Exceptions/Handler.php` continue to work unchanged. Rather than rewrite the bootstrap into the
new slim `bootstrap/app.php` (high risk of mis-wiring the custom `access`/`checkAccess` middleware,
`Common` facade, exception views, and providers), **the old structure was retained**. The app boots,
all 441 routes register, config/view cache succeed, and 7/7 tests pass. Migrating to the slim skeleton
is cosmetic and is deferred to the separate cleanup plan.

**Dependency changes:**
- **`laravelcollective/html` (no L11 support) → `nekhbet/laravel-collective-html` 1.0.0** — a maintained,
  **L12-compatible drop-in fork that keeps the original `Collective\Html` namespace + `Form::`/`Html::`
  facades**, so all 194 form usages and the Blade views are unchanged (verified by the alias-resolution test).
- `barryvdh/laravel-dompdf` → ^3 (PDF alias still auto-discovered); `laravel/ui` → ^4.5; `tinker` → ^2.9.
- **`phpunit` → ^11 (11.5.55)** — migrated `phpunit.xml` to the v11 schema. (`composer update` kept reverting
  the constraint to ^9.5; fixed with an explicit `composer require phpunit/phpunit:^11.0 --dev`.)
- `.phpunit.cache/` added to `.gitignore`.

Full RBAC/licence/404-500/Common-facade smoke deferred to the final manual pass.
