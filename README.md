# Santa Clara Fishing Shop 

Catalog site with private admin panel.
**Stack:** PHP 8+, MySQL 8+, HTML, CSS, JavaScript.
**Architecture:** Structured vanilla PHP (no framework), object-oriented MySQLi.

The public-facing site is in Spanish — this README documents the codebase for developers.

---

## Project structure

```
tienda-pesca/
├── config/
│ ├── config.example.php → copy to config.php on the server
│ └── config.php → NOT committed (credentials)
├── includes/
│ ├── db.php → MySQLi connection
│ ├── auth.php → login, sessions, CSRF
│ └── helpers.php → utilities (escape, price, WhatsApp, slug)
├── public/ → VPS DocumentRoot points here
│ ├── .htaccess → security and cache rules
│ ├── index.php → homepage
│ ├── productos.php → listing with category filter
│ ├── producto.php → product detail
│ ├── includes/ → public-site partials
│ │ ├── header.php
│ │ └── footer.php
│ ├── admin/ → private panel
│ │ ├── login.php
│ │ ├── logout.php
│ │ ├── index.php → dashboard
│ │ └── productos.php → CRUD (Sprint 3)
│ └── assets/
│ ├── css/
│ ├── js/
│ ├── img/
│ └── uploads/ → product images (writable)
└── sql/
├── schema.sql → DB structure
└── crear_admin.php → run ONCE, then DELETE
```

Only the `public/` folder is HTTP-accessible.
`config/`, `includes/`, and `sql/` live one directory up.

---

## Deployment on VPS

### 1. Create the database

```sql
CREATE DATABASE tienda_pesca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tp_user'@'localhost' IDENTIFIED BY 'a-strong-password';
GRANT ALL PRIVILEGES ON tienda_pesca.* TO 'tp_user'@'localhost';
FLUSH PRIVILEGES;
```

Then load the schema:
```bash
mysql -u tp_user -p tienda_pesca < sql/schema.sql
```

### 2. Configure

```bash
cp config/config.example.php config/config.php
# edit config/config.php with real credentials
```

### 3. Uploads permissions

```bash
chown -R www-data:www-data public/assets/uploads
chmod 775 public/assets/uploads
```

### 4. Create initial admin

Edit `sql/crear_admin.php` and set the owner's temporary username/password, then:
```bash
php sql/crear_admin.php
```

**DELETE the file afterwards:**
```bash
rm sql/crear_admin.php
```

### 5. Apache

DocumentRoot must point to `.../tienda-pesca/public`.
Required modules: `mod_rewrite`, `mod_headers`, `mod_expires`.

### 6. HTTPS

Install Let's Encrypt via Certbot:
```bash
sudo certbot --apache -d tiendadepescasantaclara.com -d www.tiendadepescasantaclara.com
```

After confirming HTTPS works, uncomment the redirect block in `public/.htaccess`.

---

## Sprints

- **Sprint 1**  — Technical base: structure, DB, admin login, dashboard
- **Sprint 2**  — Public catalog (home, category filters, product detail, responsive)
- **Sprint 3** — Products CRUD in admin panel + deployment + initial content load

---

## Security notes

- Passwords: `password_hash()` + `password_verify()` (bcrypt)
- CSRF token on every admin form
- Session with `httponly`, `secure`, `samesite=Lax`, regenerated on login
- Uploads: extension and size validated, PHP disabled in the folder via `.htaccess`
- Prepared statements on all queries
- All output escaped via `e()`
