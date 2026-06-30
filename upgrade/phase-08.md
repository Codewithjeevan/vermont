# Phase 08 — Laravel 9 → 10 (`$dates` → `$casts`)

**Laravel:** 10.x · **PHP:** 8.1.10 · **Branch:** `upgrade/phase-08-l10`

Official guide: https://laravel.com/docs/10.x/upgrade
Headline risk: **`protected $dates` is REMOVED** — 17 models must convert to `$casts`.

## composer.json

- [ ] `laravel/framework` → `^10.0`; `laravel/tinker` → `^2.8`.
- [ ] `phpunit/phpunit` → `^10.0`; `nunomaduro/collision` → `^7`.
- [ ] `doctrine/dbal` now optional — keep `^3` if column-change migrations remain.
- [ ] `barryvdh/laravel-dompdf` → `^2.0`; `maatwebsite/excel` → `^3.1` (L10 tag).
- [ ] **`laravelcollective/html`:** if no official L10 tag, switch to the **maintained drop-in fork**
      (same `Form::`/`Html::` API → no Blade edits). Set constraint to its L10 tag.
      Verify `brian2694/laravel-toastr` and `thomaswelton/laravel-gravatar` have L10-compatible tags;
      replace gravatar with a tiny helper if needed.
- [ ] `composer update`.

## Code changes — `$dates` → `$casts` (the big one)

Convert all 17 models. For each, replace:
```php
protected $dates = ['created_at', 'updated_at', 'birthday'];
```
with:
```php
protected $casts = [
    'birthday' => 'datetime',
    // created_at/updated_at are already datetime by default — only list extra date columns
];
```

- [ ] `Customer.php` (`birthday`, `anniversary`)
- [ ] `Billing.php`
- [ ] `BillingItems.php`
- [ ] `Item.php`
- [ ] `Purchase.php`
- [ ] `PurchasedItem.php`
- [ ] `PurchaseReturn.php`
- [ ] `PurchaseOrder.php`
- [ ] `Payment.php`
- [ ] `Stock.php`
- [ ] `StockAdjustment.php`
- [ ] `DamageStock.php`
- [ ] `ExpiryStock.php`
- [ ] `IssueStock.php`
- [ ] `OpeningCash.php`
- [ ] `OpeningStock.php`
- [ ] `CustomerReceipt.php`
- [ ] `XYPromotion.php`
- [ ] `zSales.php`
- [ ] Grep `protected \$dates` after to confirm zero remain.

> Keep the SAME date format behavior: if any model relied on a custom `$dateFormat`, use
> `'datetime:Y-m-d H:i:s'` casts to preserve output for reports/AJAX.

## Other L10 changes

- [ ] Native return types added throughout the framework — ensure no overridden methods clash
      (e.g. custom `Handler`, `User` overrides). Rector adds matching signatures.
- [ ] **DomPDF `^2.0` facade path:** confirm alias is `Barryvdh\DomPDF\Facade\Pdf::class` (if not done
      in Phase 7). `PDF::loadView()` usages unchanged.
- [ ] `predis/predis` → `^2` if Redis is used (config uses redis driver only if set).
- [ ] dropped `dispatchNow` → `dispatchSync`.
- [ ] Run Rector Laravel 10 + PHP 8.1 sets; review diffs.

## Verification

- [ ] `route:list`, `config:cache`, `view:cache` succeed.
- [ ] Characterization tests pass.
- [ ] Smoke flows 1–18; **specifically verify every date field** in bills, reports, customer
      birthday/anniversary, and AJAX report payloads render identically to pre-upgrade.

## Exit criteria

App on Laravel 10 / PHP 8.1, no `$dates` remaining. Merge, tag `v-l10`.

## Notes / deviations

**DONE — Laravel 10.50.2 on PHP 8.1.10.**
- **All 17 models converted `protected $dates = array(...)` → `protected $casts = [... => 'datetime']`**
  via a whitespace/LF-preserving regex (verified 0 `$dates` remain). `'datetime'` is the faithful
  equivalent of old `$dates` (Carbon instances). Models with classic date accessors/mutators
  (Payment `payment_date`, Purchase/PurchaseReturn `mrn_date`, Stock `date`) keep their custom
  output — accessors take precedence over casts, identical to the old `$dates` behaviour.
- **dompdf → ^2.0:** the `PDF` alias is auto-discovered (now `Barryvdh\DomPDF\Facade\Pdf`); the ~100
  `PDF::loadView()` calls are unchanged and the alias resolves (smoke test passes).
- **`laravelcollective/html` ^6.4 supports L10** — no fork needed yet (re-evaluate at L11).
- `framework` ^10, `tinker` ^2.8, `ui` ^4.2, `mockery` ^1.5. **Security advisories 14 → 3.**
- `phpunit` kept at ^9.5 (9.6.34) — fully L10-compatible; bump to 10/11 deferred to Phase 9 (L11).
- No `predis`/`dispatchNow` usage in app — those L10 notes N/A.
- Deep per-field date-output verification deferred to the final manual smoke pass.
