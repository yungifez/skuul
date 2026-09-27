---
paths:
  - 'database/migrations/**'
  - 'database/seeders/PermissionSeeder.php'
---

# Migrations

## Protect personal data in migrations
Do not delete or relocate personal or special-category data until its retention decision, backfill verification, backup, and restore procedure are approved. Treat deployed destructive migrations as forward-only; add a remediation migration instead of editing them.

## A new permission needs a migration, not only a seeder line
`PermissionSeeder` runs once, at install, and uses `syncPermissions`, so it cannot be rerun on a live school. A permission added only to the seeder never reaches an existing install, and its screens stay closed to everyone there. When you add a permission, also add a migration that creates it only when missing and grants it to the same built-in roles and `platform-admin` with `insertOrIgnore` on `role_has_permissions`. Skip a permission that already exists, so a school's own role changes are kept. See `2026_09_27_074218_backfill_permissions_added_after_install.php`.
