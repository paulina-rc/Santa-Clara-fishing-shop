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
│   ├── db.php                → conexión MySQLi
│   ├── auth.php               → sesiones, login/logout, CSRF, requerir_login()
│   └── helpers.php            → e(), precio(), url_whatsapp(), slugify(), flash
├── public/                    → DocumentRoot de Apache
│   ├── .htaccess
│   ├── index.php               → home (placeholder, diseño en Sprint 2)
│   ├── admin/
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── index.php           → dashboard
│   │   └── productos.php       → Sprint 3
│   └── assets/
│       ├── css/admin.css
│       ├── js/
│       ├── img/
│       └── uploads/            → imágenes de productos (writable)
├── sql/
│   ├── schema.sql
│   └── crear_admin.php        → script de un solo uso
├── .gitignore
└── README.md
```

## Base de datos

### 1. Crear base de datos y usuario MySQL

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

Certbot ajusta el VirtualHost automáticamente para servir HTTPS. Una vez
verificado que el sitio carga por HTTPS, **descomenta el bloque de
redirección** al inicio de `public/.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Esto fuerza que todo el tráfico HTTP se redirija a HTTPS.

## Roadmap

- **Sprint 1 (este sprint):** base técnica — estructura de carpetas, conexión
  a base de datos, esquema SQL, autenticación de admin con CSRF y sesiones
  seguras, dashboard con contadores, seguridad de servidor (.htaccess,
  cabeceras, protección de uploads).
- **Sprint 2:** diseño público del catálogo (ya aprobado) — home, listado por
  categoría, ficha de producto, integración de WhatsApp/Instagram.
- **Sprint 3:** CRUD de productos en el admin (`productos.php`) — alta, edición,
  borrado, subida y recorte de imágenes, marcar destacado/activo.

## Validación

Todos los archivos PHP deben pasar sin errores de sintaxis:

```bash
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```
