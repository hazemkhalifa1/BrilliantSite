# Brilliant Engineering Co. — cPanel-compatible PHP Edition

A faithful, feature-complete port of the **Brilliant Engineering Co.** website (originally a Next.js
frontend + ASP.NET Core API, repo: `github.com/hazemkhalifa1/BrilliantSite`) rewritten in **PHP 8 + MySQL** so it
runs on standard **cPanel shared hosting (Linux)** with **no Node.js and no .NET** required.

Everything — the public bilingual (English/Arabic, RTL/LTR) brutalist corporate site, the full admin
CMS, the JSON API, the contact form, SEO (metadata, sitemap, robots), and file uploads — is preserved.

## Highlights

- **Design-faithful**: the exact Tailwind utility classes and custom Brutalist styles are reused. The
  compiled stylesheet (`css/app.css`) was generated from the original source, so the look is identical.
- **Bilingual AR/EN** with automatic RTL for Arabic (Cairo + Space Grotesk fonts, same as the original).
- **Full admin CMS**: dashboard, CRUD for Services / Service Categories / Projects / Products / Product
  Brands / Product Categories / Blog Posts / Team / Clients, plus Settings (hero section, hero stats,
  contact info, social links, blog tags), image/document uploads, publish/draft toggles and reorder.
- **JWT auth** (pure-PHP HS256, no Composer) for the API + secure session login for the admin.
- **JSON REST API** mirroring the .NET controllers (auth, hero, contact, meta, sitemap, services,
  categories, products, brands, projects, types, blog, tags, clients, team, social links, upload).
- **SEO**: per-page meta, `/sitemap.xml`, `/robots.txt`, structured data (JSON-LD) on home/product/blog.

## Tech

| Concern          | Choice                                                        |
|------------------|---------------------------------------------------------------|
| Language         | PHP 8.1+ (no framework, no Composer)                           |
| Database         | MySQL/MariaDB (production) or SQLite (local dev/demo)          |
| DB access        | PDO (positional prepared statements; backtick identifiers)     |
| Auth             | Password hash (bcrypt) + HS256 JWT; session for the admin UI   |
| Assets           | Compiled Tailwind CSS + Google Fonts (Cairo, Space Grotesk)    |
| Rich text        | Quill (CDN) for blog content in the admin                      |

## Requirements

- PHP 8.1+ with extensions: `pdo_mysql` (production), `pdo_sqlite` (local), `mbstring`, `openssl`,
  `fileinfo`, `gd`/no, `json`, `curl`.
- cPanel shared hosting: PHP (Select PHP Version → 8.1/8.2/8.3) + a MySQL database.

## Running locally (quick start, no MySQL needed)

Requires a PHP 8.3 CLI with `pdo_sqlite`. The app auto-creates and seeds a SQLite database on first run.

Secrets are required (the app fails fast if they're missing). Set them for local dev:

```bash
cd brilliant-php

# macOS / Linux
JWT_SECRET="dev-secret-change-me" ADMIN_PASSWORD="ChangeMe123!" php -S 127.0.0.1:8080 router.php

# Windows PowerShell
# $env:JWT_SECRET="dev-secret-change-me"; $env:ADMIN_PASSWORD="ChangeMe123!"; php -S 127.0.0.1:8080 router.php

# open http://127.0.0.1:8080   (English) / http://127.0.0.1:8080/ar (Arabic)
```

- Public site: `http://127.0.0.1:8080` / `/ar`
- Admin login: `http://127.0.0.1:8080/en/login`
  - Email `admin@brilliant-eng.com` · Password: the value you set in `ADMIN_PASSWORD`
  (`ADMIN_EMAIL` / `ADMIN_PASSWORD` come from env vars — there is no hardcoded fallback.)

## Project layout

```
brilliant-php/
├── index.php            # Front controller
├── router.php           # Local dev-server router
├── .htaccess            # cPanel rewrite + security
├── config.php           # DB / JWT / uploads config (env-overridable)
├── bootstrap.php        # Autoloader + helpers
├── app/                 # PHP App code
│   ├── Kernel.php       # Dispatcher (routing, locale, session, schema)
│   ├── Router.php
│   ├── Controllers/     # PageController, ApiController, LoginController, AdminController
│   └── Services/        # Database, Jwt, Auth, I18n, View, Response, Repo, Schema, ...
├── views/               # Server-rendered templates
│   ├── layouts/         # public / admin / auth
│   ├── partials/        # navbar, footer, page-header, lang-switcher
│   ├── public/          # home, about, services, projects, products, blog, team, clients, contact
│   └── admin/           # dashboard, list, form, settings
├── i18n/                # en.php / ar.php (translated from the original JSON)
├── css/ js/ img/ uploads/   # compiled assets + uploads
├── db/                  # schema.mysql.sql, schema.sqlite.sql
├── storage/             # SQLite db (dev), protected from web
└── docs/CONVENTIONS.md
```

## Deployment to cPanel

See **[DEPLOY.md](DEPLOY.md)** for the step-by-step guide.
