# Production deployment (visatrack)

Laravel must be served from the **`public/`** directory. If your host only allows the project root as the document root, use the included root `index.php` and `.htaccess` (Apache).

## Recommended: document root = `public/`

Point the site (Apache/Nginx/cPanel) to:

```text
/path/to/visatrack/public
```

Same as Laragon local: `...\visa-tracking\public`.

## Apache (document root = project root)

If you cannot change the document root, deploy the full repo and ensure:

- `mod_rewrite` is enabled
- Root `.htaccess` and `index.php` are present (they forward to `public/`)

## Server setup (after `git clone`)

```bash
cd /path/to/visatrack

composer install --no-dev --optimize-autoloader

cp .env.example .env
# Edit .env: APP_URL, APP_KEY, DB_*, DEMO_ADMIN_PASSWORD

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### `.env` (production)

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

`APP_URL` must be the site root **without** `/public` (e.g. `https://ceac.stateus.org`). The app forces that root for all links on production, even when the host runs `public/index.php` from the project folder.

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visa_tracking
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

DEMO_ADMIN_EMAIL=admin@your-domain.com
DEMO_ADMIN_PASSWORD=choose-a-strong-password
```

Create the MySQL database before `migrate`.

### Permissions

Writable by the web server user:

```bash
chmod -R ug+rwx storage bootstrap/cache
```

### Nginx snippet (document root = public)

```nginx
root /var/www/visatrack/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### URLs

- Public: `/`, `/visa-status`
- Admin: `/admin/login`

### Troubleshooting “public folder” issues

| Symptom | Fix |
|--------|-----|
| 404 on all routes, only static files work | Document root must be `public/` or use root `.htaccess` |
| Assets/CSS missing | `APP_URL` must match your live URL (with `https://`) |
| 500 after deploy | `storage/` and `bootstrap/cache/` permissions; run `php artisan config:clear` |
| Photos on confirmation broken | `chmod` on `storage/`; photographs live in `storage/app/private/` (not `public/storage`) |

## Update deployment

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```