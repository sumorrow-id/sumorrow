# Sumorrow Backend

This repository contains the backend schema and application code for Sumorrow.

## Local setup (Windows + XAMPP)

### Prerequisites

- PHP 8.2+ and Composer available in terminal
- XAMPP installed
- Apache and MySQL started from XAMPP Control Panel
- MySQL running on port `3306`

### 1) Clone and install dependencies

```powershell
cd C:\path\to\workspace
git clone <your-repo-url> sumorrow
cd sumorrow
composer install
```

### 2) Configure environment

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Set database values in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sumorrow
DB_USERNAME=root
DB_PASSWORD=
```

### 3) Create local database

Use phpMyAdmin, or run:

```powershell
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS sumorrow"
```

or add manually in your MySQL client:

```sql
CREATE DATABASE IF NOT EXISTS sumorrow
```

### 4) Clear cached config and run migrations

```powershell
php artisan config:clear
php artisan cache:clear
php artisan migrate
```

If there any error like

```powershell
# SQLSTATE[42S02]: Base table or view not found: 1146 Table 'sumorrow.cache' doesn't exist (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: sumorrow, SQL: delete from `cache`)
```

Just ignore it, and continue running the command

### 5) Optional: reset local schema

Use this only when you want to wipe and recreate all tables locally.

```powershell
php artisan migrate:fresh
```

### 6) Verify DB connection (optional)

```powershell
php artisan tinker
```

Inside Tinker:

```php
DB::select('SELECT database() as db');
```

Expected database should be `sumorrow`.

## Team migration workflow

- Pull latest changes before running migrations.
- Run `php artisan migrate` after every pull that includes migration files.
- Never edit old migration files that are already shared; create a new migration for schema changes.
- Update architecture docs (`docs/architecture/*`) when schema changes.

## Architecture documentation

- ERD index and conventions: `docs/architecture/erd/README.md`
- Data dictionary: `docs/architecture/data-dictionary.md`

## Notes

- Keep sensitive architecture details in team-only channels if this repo is shared externally.
- This README only covers local setup and migration workflow.
