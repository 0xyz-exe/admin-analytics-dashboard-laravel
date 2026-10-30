# Admin Analytics Dashboard

Admin-only dashboard for persisted user-account analytics: total accounts, date-filtered signups, verified signups, daily chart/table, CSV export. No seeded users or fabricated metrics. Dashboard and export return 403 to non-admins.

## Setup (Windows)

```powershell
Copy-Item .env.example .env
php C:/Users/wondr/.config/herd-lite/bin/composer.phar install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Public registration always creates non-admin accounts. Grant `users.is_admin = 1` only through a trusted administrative/database operation. The dashboard reports actual persisted users; empty ranges show zero and no chart rows. Default date window: last 30 calendar days through today. Date inputs are validated and reversed ranges rejected.

Metric definitions: total accounts = all persisted users; signups = users created in the selected inclusive date range; verified = those signups with non-null `email_verified_at`; verified percentage = verified / signups × 100 (0 when no signups); daily bars and CSV use grouped persisted user records. This dashboard contains no revenue, engagement, or other fabricated data.

## Verification

```powershell
php artisan migrate:fresh --seed --no-interaction
php artisan view:clear
php artisan view:cache
php artisan test --compact
npm run build
php C:/Users/wondr/.config/herd-lite/bin/composer.phar validate --no-check-publish
php C:/Users/wondr/.config/herd-lite/bin/composer.phar audit
npm audit --omit=dev
```

Keep `.env`, personal data, dependencies, SQLite databases, and build output out of Git. Additional analytics require their own persisted source data and explicit metric definitions.


## Screenshots

### Dashboard Overview
![Dashboard](docs/screenshots/01_dashboard.png)

### Login Overview
![Login](docs/screenshots/02_login.png)
