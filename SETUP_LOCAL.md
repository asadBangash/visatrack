# Local setup (Laragon)

1. Project path: `D:\laragon\www\visa-tracking` (original HTML demo remains in `D:\laragon\www\usstate`).
2. Create database: `visa_tracking` (phpMyAdmin or `CREATE DATABASE visa_tracking;`).
3. Copy `.env.example` values if needed; `.env` is preconfigured for Laragon MySQL (`root`, empty password).
4. Virtual host: map `visa-tracking.test` → `D:\laragon\www\visa-tracking\public` (Laragon auto-host for folder name may apply).
5. Run:
   ```bash
   cd D:\laragon\www\visa-tracking
   composer install
   php artisan migrate --seed
   php artisan storage:link
   ```
6. Admin login: http://visa-tracking.test/admin/login  
   - Email: `admin@visa-demo.local`  
   - Password: value of `DEMO_ADMIN_PASSWORD` in `.env` (default `DemoAdmin!ChangeMe`).
7. Public site: http://visa-tracking.test/ and http://visa-tracking.test/visa-status

## Demo public lookup records

| Confirmation | Passport | Surname (5 letters) | Status |
|---|---|---|---|
| DEMO-AA001001 | SAMPLE001 | SINGH | Application Received |
| DEMO-AA001002 | SAMPLE002 | AHMED | Approved |
| DEMO-AA001003 | SAMPLE003 | MALIK | Issued |

Use location **PAKISTAN, ISLAMABAD**, visa type **NIV**, and the CAPTCHA shown on the page.

## Tests

```bash
php artisan test
```

Tests use in-memory SQLite (see `phpunit.xml`).