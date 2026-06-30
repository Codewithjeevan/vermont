# Phase 07 — Laravel 8 → 9

**Laravel:** 9.x · **PHP:** 8.1.10 (min 8.0) · **Branch:** `upgrade/phase-07-l9`

Official guide: https://laravel.com/docs/9.x/upgrade
First PHP 8 phase — switch Laragon to **8.1.10**.

## composer.json

- [ ] `laravel/framework` → `^9.0`; `laravel/tinker` → `^2.7`; `laravelcollective/html` → `^6.3`.
- [ ] **`doctrine/dbal` → `^3.0`** (required for column change migrations on L9).
- [ ] `barryvdh/laravel-dompdf` → `^1.0`/`^2.0`; `maatwebsite/excel` → `^3.1` (L9 tag).
- [ ] `nunomaduro/collision` → `^6`; `phpunit/phpunit` → `^9.5`.
- [ ] `guzzlehttp/guzzle` → `^7.2`.
- [ ] `composer update`.

## Code changes

- [ ] **PHP 8.0/8.1 breaking changes** (Rector PHP 80/81 sets, review diffs):
  - named-argument collisions, `match` reserved word, stricter type juggling,
    `Stringable`, removed `each`, nullable param implicit deprecations.
- [ ] **Flysystem 3:** `config/filesystems.php` disk config changes; `Storage` `->put()` return types.
      Verify any file upload/export-to-disk paths.
- [ ] **DomPDF facade path change:** if bumping dompdf to `^2.0`, the facade moves from
      `Barryvdh\DomPDF\Facade` to `Barryvdh\DomPDF\Facade\Pdf`. Update the alias in `config/app.php`
      (`'PDF' => Barryvdh\DomPDF\Facade\Pdf::class`). The `PDF::loadView()` calls in views/controllers
      stay unchanged. (If staying on `^1.0` this phase, defer to Phase 8.)
- [ ] **Symfony 6** under the hood.
- [ ] **`$dates` still works in L9** — defer the `$casts` conversion to Phase 8.
- [ ] **`ignoreOnTouch`, custom casts** — minor.
- [ ] `lang/` directory move (`resources/lang` → `lang/`) is optional in 9 — keep current path.
- [ ] Run Rector Laravel 9 set; review diffs.

## Verification

- [ ] `route:list`, `config:cache`, `view:cache` succeed on PHP 8.1.
- [ ] Characterization tests pass.
- [ ] Smoke flows 1–16; focus on file/storage paths and any `doctrine/dbal` column-change migrations.

## Exit criteria

App on Laravel 9 / PHP 8.1. Merge, tag `v-l9`.

## Notes / deviations
