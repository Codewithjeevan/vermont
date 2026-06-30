# Phase 01 — PHP 7.2 → 7.4 readiness (stay on Laravel 5.4)

**Laravel:** 5.4 (no change) · **PHP:** 7.2.34 → verify on 7.4.33 · **Branch:** `upgrade/phase-01-php7`

Goal: clean the code so it runs on PHP 7.2/7.4 **without** bumping Laravel yet, so the framework
upgrade in Phase 2 starts from a PHP-clean base. (Production is already on 7.2, so this is mostly
verification + removing patterns that break on 7.4.)

## composer.json

- [ ] `"php": ">=7.2"`.
- [ ] Remove dead/removed Composer scripts:
  - `Illuminate\Foundation\ComposerScripts::postInstall` / `postUpdate`
  - `php artisan optimize` (removed in later Laravel; harmless to drop now).
- [ ] `composer update` on PHP 7.2; resolve any resolver complaints.

## Code fixes (PHP 7.2–7.4 breaking/deprecated)

Run Rector `SetList::PHP_72` / `PHP_74` (review diffs), then manually verify:

- [ ] Curly-brace array/string offset access `$str{0}` → `$str[0]` (**removed in 7.4**).
- [ ] `each()` usage (deprecated 7.2) — search `\beach\(` (exclude `foreach`).
- [ ] `create_function()` → closures — search `create_function`.
- [ ] Removed `mysql_*` functions (search `mysql_`).
- [ ] Real-valued array keys / continue-in-switch warnings (7.3).
- [ ] `get_class()` without args (deprecated 7.2), nested ternary without parens (7.4).
- [ ] PHP4-style constructors (method named like the class).

## Verification

- [ ] `php -v` shows 7.2.34 (and re-test on 7.4.33); `php artisan route:list` succeeds.
- [ ] Characterization tests pass.
- [ ] Smoke flows 1, 3, 4, 12, 13 pass.

## Exit criteria

App fully functional on PHP 7.2/7.4 with Laravel 5.4. Merge to `main`, tag `v-l54-php74`.

## Notes / deviations
