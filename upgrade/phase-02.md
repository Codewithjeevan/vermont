# Phase 02 — Laravel 5.4 → 5.5 (LTS)

**Laravel:** 5.5.50 · **PHP:** 7.2.34 · **Branch:** `upgrade/phase-02-l55`

Official guide: https://laravel.com/docs/5.5/upgrade

## composer.json

- [x] `laravel/framework` → `5.5.*` (resolved to **5.5.50**)
- [x] `phpunit/phpunit` → `~6.0` (6.5.14), `mockery/mockery` → `^1.0`
- [x] `laravelcollective/html` → `^5.5`
- [x] Kept `maatwebsite/excel ~2.1`, `barryvdh/laravel-dompdf ^0.8`.
- [x] **Removed `barryvdh/laravel-cors`** — it pinned `illuminate/support 5.1–5.4` and blocked 5.5.
      Confirmed completely unused (0 references in app/config/routes/bootstrap). Plan moved this from
      Phase 7 → here out of necessity.
- [x] Switched composer `scripts` to the 5.5 block (`post-autoload-dump` + `package:discover`);
      dropped `ComposerScripts::postInstall/postUpdate`.
- [x] `composer update -W` (PHP 7.2) — 62 packages resolved, autoload generated.

## Code changes

- [x] **Package auto-discovery:** removed the now auto-discovered providers (Tinker, Excel, Gravatar,
      Collective\Html, Toastr, DomPDF) and their aliases (Form, Html, Toastr, Excel, PDF, Gravatar)
      from `config/app.php`. Kept the `Common` alias + app providers. **Verified all 7 aliases still
      resolve** via a new test (`testPackageAliasesResolve`).
- [x] **Exception handler:** `report(Exception $e)` / `render($request, Exception $e)` signatures are
      still correct for 5.5 (Throwable change is L7). Custom 404/500 rendering intact.
- [x] **`$dates`** still fully supported — no change.
- [~] **Validation** `$this->validate()` returning data — not adopted (optional).
- [~] Rector Laravel 5.5 set — skipped (no transforms needed; manual changes sufficed).

## Verification

- [x] `route:list` succeeds; `config:cache` succeeds (`view:cache` is a 5.6 command).
- [x] Characterization tests pass — **5/5** (added alias-resolution guard).
- [ ] Manual smoke flows 1–4, 12–15 — deferred (running manual smoke at the end per user).

## Exit criteria

App on Laravel 5.5.50 LTS / PHP 7.2. Merge, tag `v-l55`.

## Notes / deviations

- `barryvdh/laravel-cors` removed in this phase (was blocking 5.5; confirmed dead).
- Provider/alias cleanup done now (reduces Phase 9 re-wiring work).
- `composer audit` reports legacy advisories + several abandoned packages (collective/html, gravatar,
  faker, phpexcel via excel 2.1, swiftmailer) — all scheduled for replacement in later phases.
