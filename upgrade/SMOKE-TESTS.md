# Smoke Tests — Critical Flow Checklist

Run **after every phase** before merging. These cover the revenue-critical and high-risk paths.
Mark P/F and note the phase. A phase is not "done" until every row passes.

## How to run

1. Switch to the phase's PHP version, `php artisan serve` (or Laragon vhost).
2. `php artisan route:list` — must succeed with no errors.
3. `php artisan config:cache && php artisan view:cache` — must succeed (catches Blade/config breaks).
4. Walk each flow below in the browser.
5. Run automated characterization tests: `php artisan test` (see bottom).

## Manual critical flows

| # | Flow | Steps | Expected |
|---|---|---|---|
| 1 | Licence + Login | Open `/`, complete licence check, log in as admin | Dashboard loads, no 500 |
| 2 | RBAC | Log in as a limited role | Restricted menus/routes blocked by `checkAccess` |
| 3 | POS Billing | Create a new bill, add items, choose payment mode, save | Bill saved, stock decremented, receipt renders |
| 4 | Bill PDF/Print | Print/download a saved bill | PDF renders with correct totals |
| 5 | Planet Tax Refund | Trigger tax-refund on an eligible bill (sandbox) | API call succeeds, tag recorded in `billing_tax_refunds` |
| 6 | Bill Return | Return items from a bill | Return saved, stock restored |
| 7 | Purchase | Create a purchase / MRN, add items, save | Purchase saved, stock incremented |
| 8 | Purchase Return | Create a purchase return | Saved, stock adjusted |
| 9 | Stock Adjustment | Excess / Shortage / Wastage entry | Stock changes per rule |
| 10 | Damage / Expiry / Issue stock | Create one of each | Saved, stock deducted |
| 11 | Customer + Receipt | Add customer, record a receipt/ledger entry | Ledger balance correct |
| 12 | Dashboard | Open dashboard | All widgets/aggregates load (raw-SQL heavy) |
| 13 | Sales Report | Run with date range, export **Excel** | Rows correct, .xlsx downloads & opens |
| 14 | Sales Report PDF | Same report as PDF | PDF downloads, layout intact |
| 15 | VAT / Cashier / Salesman reports | Run each with filters | Totals correct, exports work |
| 16 | Item import | Import items via Excel | Rows imported, validation works |
| 17 | Toastr flash | Trigger a save/delete | Toast notification appears |
| 18 | Opening cash / stock | Set opening cash and opening stock | Saved correctly |

## Export/PDF coverage (Maatwebsite & dompdf — Phases 4 & 11)

Verify **at least one Excel export and one PDF in every module** after Phase 4 and again after Phase 11:
sales reports, purchase reports, VAT, cashier, salesman, stock balance, customer ledger, items.

## Automated characterization tests (build in Phase 0)

Minimal Feature tests created under `tests/Feature/` to guard the spine across phases:

| Test | Asserts |
|---|---|
| `LoginTest` | login page 200; valid creds redirect to dashboard |
| `BillingStoreTest` | POST a bill persists row + decrements stock |
| `SalesReportTest` | report route 200 with a date range |
| `ExcelExportTest` | one export returns a binary xlsx response |
| `PdfExportTest` | one PDF route returns `application/pdf` |

Run: `php artisan test --testsuite=Feature`

## Sign-off

| Phase | Date | All flows pass? | Notes |
|---|---|---|---|
| 00 | | | |
| 01 | | | |
| 02 | | | |
| 03 | | | |
| 04 | | | |
| 05 | | | |
| 06 | | | |
| 07 | | | |
| 08 | | | |
| 09 | | | |
| 10 | | | |
| 11 | | | |
