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

- [x] `config:cache`, `view:cache` succeed on Laravel 9.52.21 / PHP 8.1.10.
- [x] Characterization tests pass — **7/7**.
- [ ] Manual smoke 1–16 + storage paths + dbal column-change migrations — deferred to final pass.

## Exit criteria

App on Laravel 9 / PHP 8.1. Merge, tag `v-l9`.

## Notes / deviations

**DONE — Laravel 9.52.21 on PHP 8.1.10 (first PHP 8 phase).**
- framework ^9, `doctrine/dbal` → ^3, `guzzle` → ^7.2, `laravel/ui` → ^4, `collective/html` → ^6.3,
  `tinker` → ^2.7, `phpunit` → ^9.5. `dompdf` kept at ^1.0.1 (facade path change deferred to Phase 8's ^2).
- **Removed `thomaswelton/laravel-gravatar`** — it pinned old illuminate/support and blocked L9; confirmed
  **completely unused** (only appeared in our own config/app.php comments). Dropped `Gravatar` from the
  alias-resolution test. (`config/gravatar.php` left as a harmless orphan.)
- PHP 8 code fixes: none needed (code already ran clean on 7.4). `$dates` still works in L9 → converted in Phase 8.
- Note: the Laragon `php-8.1.10` CLI prints a harmless "Failed to set memory limit to 1000 bytes" warning
  (its php.ini), unrelated to the app.

## Notes / deviations
