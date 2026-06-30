# Rollback Procedure

Every phase is reversible. Two independent layers: **code (git)** and **data (DB dump)**.

## Before starting any phase

```powershell
# 1. Ensure clean tree
git status --short          # must be empty

# 2. Tag the last known-good state
git tag pre-phase-NN

# 3. Back up the database (adjust DB name/credentials)
$ts = Get-Date -Format "yyyyMMdd-HHmmss"
mysqldump -u root vermont_db | Out-File -Encoding utf8 "upgrade/_backups/vermont_db_pre-phaseNN_$ts.sql"

# 4. Snapshot composer.lock
Copy-Item composer.lock "upgrade/_backups/composer.lock.pre-phaseNN"
```

> Create `upgrade/_backups/` once; it is git-ignored (see note below).

## Roll back code only (dependency/code break)

```powershell
git checkout -- .                 # discard working changes
git checkout main                 # leave the phase branch
git branch -D upgrade/phase-NN-<name>
composer install                  # restore vendor/ to last committed lock
```

## Roll back code + DB (migration changed data/schema)

```powershell
# Code
git reset --hard pre-phase-NN

# Vendor
Copy-Item "upgrade/_backups/composer.lock.pre-phaseNN" composer.lock -Force
composer install

# Data
Get-Content "upgrade/_backups/vermont_db_pre-phaseNN_<ts>.sql" | mysql -u root vermont_db
```

> `git reset --hard` is destructive — only run after confirming the backup `.sql` exists.

## Per-phase migration safety

- Prefer `php artisan migrate` (never `migrate:fresh`/`migrate:refresh` on data you care about).
- If a phase adds migrations, confirm each has a working `down()` before running `migrate`.
- After migrating, verify row counts on core tables (`billings`, `billing_items`, `stocks`,
  `purchases`, `customers`, `users`) match the pre-phase snapshot.

## PHP version rollback

Laragon: switch the active PHP back via the Laragon menu (Apache/PHP) or
`php-switch` to the previous version listed in [README.md](README.md). No data impact.

## .gitignore addition (do once in Phase 0)

Add to `.gitignore`:
```
/upgrade/_backups/
```
Keep the plan docs tracked, but never commit DB dumps.
