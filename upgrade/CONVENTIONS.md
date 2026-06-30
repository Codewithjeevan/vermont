# Conventions to Preserve During the Upgrade

These are project-specific facts that must NOT be broken by any refactor, Rector run, or cleanup.
Read before touching code in any phase.

## Runtime reality

- **Production runs on PHP 7.2** (the client writes PHP-5-style code by habit, but the server is 7.2).
  The baseline ladder therefore starts at PHP 7.2, not 5.6.
- Laravel 5.4 app, custom RBAC, licence-gated login.

## File formatting (do not "normalize" blindly)

- `public/js/app.js` is a **minified webpack bundle with NO source files**. Frontend source was never
  committed. **Do not rebuild or reformat it.** If a JS change is unavoidable, edit the minified code
  directly and verify via `Invoke-WebRequest http://<host>/js/app.js`. Browsers cache it aggressively —
  hard refresh to test.
- `app.js` uses **LF** line endings. Preserve LF (do not let editors convert to CRLF).
- `SalesReportController.php` uses **TAB indentation with LF** endings. Match it when editing.
- Keep existing indentation style per-file; Rector/CS-fixer must not reflow unrelated lines.

## Business logic that must stay intact

- **Payment modes** (integer `payment_mode`): `1`=Cash, `2`=Card & Cash, `3`=Credit,
  `4`=Credit Card, `5`=Noon Credit (Noon Credit behaves like Credit — no cash/card fields,
  customer info required). Touched in: `BillingsController` (Z/X reports), `DashboardController`,
  `SalesReportController`, `CustomerReceiptsController`, `CustomerLedgersController`,
  `billing-payment.blade.php`, `public/js/app.js`.
- Stock movement rules (Excess +, Shortage/Wastage −) in stock adjustment must be preserved.
- Licence validation (XOR+base64 against the `options` table) gates the login screen — keep behavior.
- Planet Tax Refund API request/response shapes must stay byte-compatible when curl→Http is migrated.

## Refactor guardrails

- When parameterizing raw SQL (Phase 11), **the result set must be identical** — compare output to a
  pre-change baseline file, do not "improve" the query logic.
- When converting `$dates`→`$casts` (Phase 8), preserve the existing **date output format** used by
  reports/AJAX (use `datetime:Y-m-d H:i:s` casts where the old format mattered).
- Keep models in the `App\` namespace (do NOT move to `App\Models`) to avoid touching 43 models
  and all their references.
- Keep Laravel Collective form syntax (use the maintained drop-in fork) — do not rewrite 194 forms.
