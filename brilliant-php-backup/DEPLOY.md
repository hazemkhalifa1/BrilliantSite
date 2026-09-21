# Deploying Brilliant Engineering (PHP) on cPanel shared hosting

This is a standard **LAMP** deployment (Linux + Apache/LiteSpeed + MySQL + PHP). cPanel shared hosting
supports all of it natively — no Node.js, no .NET, no composer.

## 1. Create the database

1. In cPanel open **MySQL® Databases**.
2. Create a database, e.g. `myuser_brilliant`.
3. Create a DB user (or reuse one) and grant it **All Privileges** on that database.
   Note the **database name, user, and password**.

## 2. Upload the site

1. In **File Manager** (or FTP), go to `public_html`.
2. Upload the **contents of the `brilliant-php` folder** into `public_html` (i.e. `index.php`,
   `.htaccess`, `app/`, `views/`, `css/`, `js/`, `img/`, `uploads/`, `i18n/`, `db/` … all at the
   web root). Make sure the dotfile `.htaccess` is uploaded too.
3. Ensure `uploads/` is writable by the web server (right-click → Permission → **755** or **775**).
4. Make sure `.user.ini` is uploaded too — it raises `upload_max_filesize`/`post_max_size` on
   PHP-FPM/LiteSpeed hosts where `.htaccess` `php_value` is ignored.

> Alternatively, if your domain must live in a subfolder, put the whole folder there and point the
> domain at it.

## 3. Configure the app (set it to MySQL)

Edit `config.php` — or better, set PHP environment variables (do **not** commit secrets). In cPanel you
can set environment variables via **PHP → Per-directory settings → "+" → Environment Variables**.

```ini
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=myuser_brilliant
DB_USER=myuser_brilliant
DB_PASS=YourStrongPassword
JWT_SECRET=a-long-random-secret-string
ADMIN_PASSWORD=ChangeMe123!
SITE_URL=https://yourdomain.com
```

`DB_NAME`, `DB_USER` and `DB_PASS` are **required** when `DB_DRIVER=mysql`; `JWT_SECRET` and
`ADMIN_PASSWORD` are always required. If any is missing the app refuses to start (there are no
hardcoded fallbacks). Do **not** put secrets in `config.php` — it only reads them from the
environment now.

## 4. Import the schema (recommended) — or let the app create it

The app can **auto-create** the tables and **seed** the admin user + defaults on its first request.
That's the easiest path. If you prefer to do it manually:

1. Open **phpMyAdmin**, select the database.
2. **Import** `db/schema.mysql.sql`.
3. (Tables will be empty — the app seeds the admin user + default categories/types/hero/contact on
   its first run. Content is managed through the admin panel.)

## 5. Set the site URL

In cPanel **Domains** ensure the domain points at `public_html`. Confirm `SITE_URL` in `config.php`
matches your domain `https://yourdomain.com` (used for canonical URLs, sitemap, meta tags).

## 6. Open the site

- Public site: `https://yourdomain.com` (English) / `https://yourdomain.com/ar` (Arabic)
- Admin: `https://yourdomain.com/en/login`

**Initial admin account** — created on first run from the `ADMIN_EMAIL` and `ADMIN_PASSWORD`
environment variables (there is no hardcoded default password):

```
email:    admin@brilliant-eng.com   (or whatever ADMIN_EMAIL is set to)
password: (whatever ADMIN_PASSWORD is set to)
```

## 7. Verify

- `/sitemap.xml` and `/robots.txt` return 200.
- Admin dashboard lists counts (Services / Projects / Products / Blog / Team / Clients).
- Create a Service on the admin → it appears on the public `/services` page.
- The contact form stores submissions server-side.

## 8. Recommended hardening

- Change the default admin password on first login.
- Set a strong random `JWT_SECRET`.
- In cPanel "Domain → Manage Redirection" ensure `http` → `https`.
- If your host uses PHP-FPM/LiteSpeed, the `.htaccess` rules already handle routing; if you have a
  conflict, check the "PHP handler" is not blocking the rewrite.

## Notes on paths

- All static assets are served from the web root (`/css`, `/js`, `/img`, `/uploads`).
- `storage/` and `db/` are protected from direct web access via `.htaccess`.
- Uploaded files land in `uploads/` and are returned to the admin as `/uploads/...` paths.
