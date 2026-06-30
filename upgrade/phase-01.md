# Phase 01 — PHP 7.2 → 7.4 readiness (stay on Laravel 5.4)

**Laravel:** 5.4 (no change) · **PHP:** 7.2.34 → verify on 7.4.33 · **Branch:** `upgrade/phase-01-php7`

Goal: clean the code so it runs on PHP 7.2/7.4 **without** bumping Laravel yet, so the framework
upgrade in Phase 2 starts from a PHP-clean base. (Production is already on 7.2, so this is mostly
verification + removing patterns that break on 7.4.)

## composer.json

- [x] `"php": ">=7.2"`.
- [~] Remove dead/removed Composer scripts:
  - [x] `php artisan optimize` removed from `post-install-cmd`/`post-update-cmd` (breaks in L5.5+).
  - [ ] `ComposerScripts::postInstall`/`postUpdate` — still valid in 5.4; **removal deferred to Phase 2**.
- [x] `composer update --lock --no-scripts` on PHP 7.2 — lock hash synced, **no package versions changed**
      (framework still 5.4.36). Full `composer update` deferred (no Laravel bump in this phase).

## Code fixes (PHP 7.2–7.4 breaking/deprecated)

Scanned `app/`, `database/`, `routes/`, `config/` — **codebase is already clean** (it has run on 7.2 in
production). Findings:

- [x] Curly-brace offset access `$str{0}` — **0 matches**.
- [x] `each()` (standalone) — **0 matches** (only `foreach`).
- [x] `create_function()` — **0 matches**.
- [x] `mysql_*` functions — **0 matches**.
- [x] `split()` / `ereg` / `money_format()` (removed funcs) — **0 matches**.
- [~] `get_class()` w/o args, nested ternary w/o parens — these break in **8.0**, not 7.4; addressed in Phase 7.
- [~] Rector PHP_72/PHP_74 run — **skipped** (nothing to transform; Rector deferred to where it adds value:
      Laravel sets + PHP 8 sets, Phase 2+ / Phase 7+).

## Verification

- [x] `artisan --version` = Laravel 5.4.36 on **PHP 7.4.33**; `route:list` succeeds.
- [x] Characterization tests pass on 7.4 (4/4).
- [x] `composer validate` → composer.json valid.
- [ ] Manual smoke flows 1, 3, 4, 12, 13 — deferred (running all manual smoke at the end per user).

## Exit criteria

App fully functional on PHP 7.2/7.4 with Laravel 5.4. Merge to `main`, tag `v-l54-php74`.

## Notes / deviations

- Codebase had **zero** PHP 7.4-removed patterns — Phase 1 was mostly composer.json hygiene + verification.
- Rector setup deferred (no-op for 7.2→7.4). Will install as isolated tool when Laravel/PHP-8 sets are needed.
- `ComposerScripts::postInstall/postUpdate` removal deferred to Phase 2 (still valid in 5.4).
- Full `composer update` intentionally NOT run (would only churn patch versions; Laravel stays 5.4 here).
