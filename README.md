# Gypsum & Acoustic Engineering Systems

cPanel-friendly PHP/MySQL project structure for a gypsum ceiling, drywall partition, acoustic systems, BOQ estimation, and contractor lead generation website.

## Language Structure

Backend:

- PHP 8.1+ using a small MVC-style structure
- MySQL or MariaDB through PDO
- No Composer or Node.js required for the base version

Frontend:

- HTML5 PHP views
- CSS3 in `public_html/assets/css/app.css`
- Vanilla JavaScript in `public_html/assets/js/app.js`
- No React, Next.js, npm build step, or server process

## Folder Structure

```text
app/
  config/          PHP config
  controllers/     Page, calculator, inquiry controllers
  core/            Router, controller base, view renderer, helpers, database
  models/          Content repository and future DB repositories
  views/           PHP templates
database/
  schema.sql       MySQL/MariaDB table structure
public_html/
  index.php        Front controller for cPanel document root
  .htaccess        Clean URLs and basic hardening
  assets/          CSS, JS, images
  uploads/         Public uploaded files, protected by validation in production
storage/
  logs/            Inquiry/API logs during early development
```

## cPanel Deployment

1. Put `public_html` contents into the hosting account `public_html` folder.
2. Put `app`, `database`, and `storage` one level above `public_html` if your cPanel account allows it.
3. Create a MySQL database in cPanel.
4. Import `database/schema.sql` through phpMyAdmin.
5. Copy `.env.example` to `.env` and fill database, email, analytics, and WhatsApp values.
6. Visit the domain. Clean URLs are handled by `public_html/.htaccess`.

## XAMPP Local Setup

1. Copy the project folder to `C:\xampp\htdocs\gypsum-acoustic`.
2. Start Apache and MySQL from the XAMPP control panel.
3. Create a database named `calculator` in phpMyAdmin.
4. Import `database/schema.sql` into the `calculator` database.
5. Copy `.env.example` to `.env` and use these local values:

```env
APP_URL=http://localhost/gypsum-acoustic/public_html
DB_HOST=localhost
DB_NAME=calculator
DB_USER=root
DB_PASS=
```

Open `http://localhost/gypsum-acoustic/public_html/`.

Create an admin user from the project root:

```bash
php database/create_admin.php admin@example.com "StrongPassword123!" "Super Admin"
```

Seed the starter catalog:

```bash
php database/seed_catalog.php
```

Seed the starter blog posts:

```bash
php database/seed_blogs.php
```

Seed the starter gallery items:

```bash
php database/seed_gallery.php
```

Then open `http://localhost/gypsum-acoustic/public_html/admin/login`.

## Main Routes

- `/` - engineering home and quick calculator
- `/calculators` - material estimation and BOQ preview
- `/products` - seeded product catalog
- `/products/{slug}` - product detail page
- `/installation` - installation method references
- `/knowledge` - NRC/STC/acoustic education
- `/blog` - SEO blog index
- `/blog/{slug}` - blog article detail
- `/gallery` - project gallery
- `/downloads` - resource library placeholders
- `/contact` - inquiry form
- `/admin/login` - admin login
- `/admin` - protected dashboard
- `/admin/inquiries` - saved inquiries
- `/admin/boqs` - saved calculator estimates
- `/admin/brands` - brand management
- `/admin/categories` - category management
- `/admin/products` - product catalog management
- `/admin/blogs` - blog management
- `/admin/downloads` - download management
- `/admin/gallery` - gallery management
- `/admin/users` - admin and role management

## API Endpoints

- `POST /api/boq` - accepts calculator BOQ JSON
- `POST /api/inquiries` - accepts contractor inquiry JSON or form data

Both endpoints validate input and save to MySQL. If the database is unavailable during early development, they fall back to `storage/logs`.

## Maintenance Scripts

- `php scripts/generate_sitemap.php` - rebuilds `public_html/sitemap.xml`
- `php scripts/backup.php` - creates a timestamped backup under `storage/backups`
- `php scripts/restore.php <backup-json>` - restores a backup snapshot into MySQL

## Production Notes

- Keep `public_html/` as the web root on cPanel.
- Keep `app/`, `database/`, `scripts/`, and `storage/` outside the public web root when possible.
- Set `APP_URL`, `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `ADMIN_EMAIL`, and `WHATSAPP_NUMBER` in `.env`.
- Regenerate the sitemap after publishing new pages or posts.
