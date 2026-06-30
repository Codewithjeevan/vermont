# Package Compatibility Matrix

Target version of each dependency at every Laravel step. Use this to set `composer.json`
constraints before running `composer update` in each phase.

> Verify exact tags against Packagist at execution time — pick the highest tag that supports
> the target Laravel version.

## Core

| Package | L5.4 (now) | L5.5 | L5.8 | L6 | L7 | L8 | L9 | L10 | L11/12 |
|---|---|---|---|---|---|---|---|---|---|
| php | >=5.6.4 | ^7.0 | ^7.1.3 | ^7.2 | ^7.2.5 | ^7.3\|^8.0 | ^8.0.2 | ^8.1 | ^8.2 |
| laravel/framework | 5.4.* | 5.5.* | 5.8.* | ^6.0 | ^7.0 | ^8.0 | ^9.0 | ^10.0 | ^12.0 |
| laravel/tinker | ~1.0 | ~1.0 | ^1.0 | ^2.0 | ^2.0 | ^2.5 | ^2.7 | ^2.8 | ^2.9 |
| guzzlehttp/guzzle | ^6.5 | ^6.5 | ^6.5 | ^6.5\|^7 | ^7.0 | ^7.0 | ^7.2 | ^7.5 | ^7.8 |

## Feature / UI packages

| Package | Now | Action & target |
|---|---|---|
| barryvdh/laravel-dompdf | ^0.8 | → ^0.9 (L6) → ^1.0 (L7/8) → ^2.0 (L9/10) → ^3.0 (L11/12). `PDF::loadView()` API stays compatible. |
| maatwebsite/excel | ~2.1 | → **^3.1 at Phase 4** (major rewrite: `Excel::create()` → Export classes). Stays 3.1 through L9, verify ^3.1 on L10/11/12. |
| barryvdh/laravel-cors | 0.9.2 | **Remove at L7.** Use first-party CORS (`config/cors.php`). It is currently not even wired. |
| laravelcollective/html | ^5.2 | Bump per Laravel: ^5.5/^5.6/^5.8/^6.0/^6.2/^6.3/^6.4. **At L10/11 switch to maintained community fork** (drop-in) — see note below. |
| brian2694/laravel-toastr | ^5.57 | Bump per Laravel; verify maintained tag for L9+. Fallback: session flash + JS toastr if abandoned. |
| thomaswelton/laravel-gravatar | ~1.0 | Verify maintained fork for L8+; replace with `creativeorange/gravatar` or a tiny helper if broken. |

## Dev packages

| Package | Now | Target |
|---|---|---|
| phpunit/phpunit | ~5.0 | ~6 (L5.5) → ^7 (L5.8) → ^8 (L6) → ^9 (L7-L9) → ^10 (L10) → ^11 (L11/12) |
| mockery/mockery | 0.9.* | ^1.0 (L5.5+) |
| fzaninotto/faker | ~1.4 | **Abandoned** → `fakerphp/faker ^1.9` (L8+) |
| doctrine/dbal | ^2.5 | ^2.5 → **^3.0 at L9** (needed for column changes); optional from L10 |
| nunomaduro/collision | — | add `^5`/`^6`/`^7`/`^8` from L8+ for nicer CLI errors |

## Laravel Collective fork note (Phase 10/11)

`laravelcollective/html` official tags stop around L9. Two options when reaching L11/12:
- **Drop-in (recommended):** community fork that keeps the same `Form::` / `Html::` facades and
  `{!! Form::open() !!}` syntax → **zero Blade changes**. Set constraint to the fork's L12 tag.
- **spatie/laravel-html:** cleaner/maintained but different API → would require editing 194 form usages.

Decision: use the **drop-in fork** to avoid touching views.

## Packages to remove during upgrade
- `barryvdh/laravel-cors` (replaced by first-party CORS at L7).
- `fzaninotto/faker` (replaced by `fakerphp/faker`).
- Composer scripts `ComposerScripts::postInstall/postUpdate` and `artisan optimize` (removed at Phase 1).
