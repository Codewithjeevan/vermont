# Phase 03 — Laravel 5.5 → 5.6 → 5.7 → 5.8

**Laravel:** 5.6 → 5.7 → 5.8 · **PHP:** 7.2.34 · **Branch:** `upgrade/phase-03-l58`

Three minor hops, **one sub-commit each**. Guides:
- https://laravel.com/docs/5.6/upgrade
- https://laravel.com/docs/5.7/upgrade
- https://laravel.com/docs/5.8/upgrade

## Sub-step 5.5 → 5.6

- [ ] `laravel/framework` → `5.6.*`; `laravelcollective/html` → `^5.6`; `phpunit` → `^7.0`.
- [ ] **Logging:** add `config/logging.php` (new in 5.6). Map old single-file log to `stack`/`single`.
      Remove `'log' => ...` keys from `config/app.php`.
- [ ] Blade cached views path changes — `php artisan view:clear`.
- [ ] Commit `5.6`.

## Sub-step 5.6 → 5.7

- [ ] `laravel/framework` → `5.7.*`; `laravelcollective/html` → `^5.7`.
- [ ] Email verification contract optional (skip — custom auth).
- [ ] `resources/assets` → `resources/` path change (frontend is static, low impact; verify view paths).
- [ ] `Paginator` URL/window changes — verify report pagination.
- [ ] Commit `5.7`.

## Sub-step 5.7 → 5.8

- [ ] `laravel/framework` → `5.8.*`; `laravelcollective/html` → `^5.8`; `phpunit` → `^7.5`.
- [ ] **Carbon 2:** `nesbot/carbon` → `^2.0`. Audit date formatting/`diffForHumans`/`->toDateTimeString()`.
- [ ] **CACHE TTL CHANGE (critical):** `Cache::put()` / `remember()` TTL is now **seconds, not minutes**.
      Search `Cache::put`, `->remember(`, `->put(` and multiply old minute values by 60.
- [ ] String/Array helpers deprecation warnings appear (still work) — note for Phase 4 removal.
- [ ] `dotenv` v3 parsing — verify `.env` quoting.
- [ ] Commit `5.8`.

## Verification (after each sub-step)

- [ ] `route:list`, `config:cache`, `view:cache` succeed.
- [ ] Characterization tests pass.
- [ ] Smoke flows 1–4, 12–15; pay special attention to anything time/cache related after 5.8.

## Exit criteria

App on Laravel 5.8 / PHP 7.2. Merge, tag `v-l58`.

## Notes / deviations
