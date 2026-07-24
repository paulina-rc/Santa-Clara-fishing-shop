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
│   ├── config.example.php     → copy to config.php on the server
│   └── config.php             → NOT committed (credentials)
├── includes/
│   ├── db.php                 → conexión MySQLi
│   ├── auth.php                → sesiones, login/logout, CSRF, requerir_login()
│   ├── helpers.php             → e(), precio(), url_whatsapp(), slugify(), flash
│   └── productos_helpers.php   → validar_imagen(), guardar_imagen(), borrar_imagen()
├── public/                    → DocumentRoot de Apache
│   ├── .htaccess
│   ├── index.php               → home pública (destacados)
│   ├── productos.php           → listado público por categoría
│   ├── producto.php            → ficha pública de producto
│   ├── includes/
│   │   ├── header.php          → partial: nav pública, meta tags
│   │   └── footer.php          → partial: footer, WhatsApp/Instagram
│   ├── admin/
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── index.php            → dashboard con contadores
│   │   ├── cuenta.php            → cambio de contraseña del admin
│   │   ├── productos.php        → listado admin (filtros, acciones rápidas)
│   │   ├── producto_nuevo.php   → formulario de alta
│   │   ├── producto_editar.php  → formulario de edición (?id=)
│   │   ├── producto_eliminar.php → endpoint POST, borra producto + imagen
│   │   └── producto_toggle.php  → endpoint POST, toggle activo/destacado
│   └── assets/
│       ├── css/
│       │   ├── admin.css       → estilos del panel admin
│       │   └── publico.css     → estilos del sitio público
│       ├── js/
│       ├── img/
│       └── uploads/            → imágenes de productos (writable, sin PHP)
├── sql/
│   ├── schema.sql
│   └── crear_admin.php        → script de un solo uso
├── .gitignore
├── DEPLOY.md                  → guía de despliegue en VPS (Apache, MySQL, SSL)
└── README.md
```

## Desarrollo local — inicio rápido

### 1. Crear base de datos y cargar el esquema

```sql
CREATE DATABASE tienda_pesca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tp_user'@'localhost' IDENTIFIED BY 'a-strong-password';
GRANT ALL PRIVILEGES ON tienda_pesca.* TO 'tp_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
mysql -u tp_user -p tienda_pesca < sql/schema.sql
```

### 2. Configurar

```bash
cp config/config.example.php config/config.php
# editar config/config.php con las credenciales locales y BASE_URL
```

### 3. Permisos de uploads

```bash
chmod 775 public/assets/uploads
```

### 4. Crear el admin inicial

Edita `sql/crear_admin.php` con el usuario/contraseña deseados, ejecútalo y
bórralo:

```bash
php sql/crear_admin.php
rm sql/crear_admin.php
```

### 5. Servidor

Apunta el DocumentRoot de Apache (o el servidor embebido de PHP) a
`public/`. Módulos requeridos: `mod_rewrite`, `mod_headers`, `mod_expires`.

Para desplegar en un VPS de producción (VirtualHost completo, MySQL con
usuario de permisos mínimos, permisos de archivos, HTTPS con Certbot,
checklist post-despliegue), ver **[DEPLOY.md](DEPLOY.md)**.

## Roadmap

- **Sprint 1:** base técnica — estructura de carpetas, conexión a base de
  datos, esquema SQL, autenticación de admin con CSRF y sesiones seguras,
  dashboard con contadores, seguridad de servidor (.htaccess, cabeceras,
  protección de uploads).
- **Sprint 2:** diseño público del catálogo — home, listado por categoría,
  ficha de producto, integración de WhatsApp/Instagram.
- **Sprint 3 (este sprint):** CRUD de productos en el admin — listado con
  filtros y paginación, alta y edición con subida/validación de imágenes
  (MIME real, no extensión declarada), toggle rápido de activo/destacado,
  eliminación con confirmación, cambio de contraseña del admin, y guía de
  despliegue (`DEPLOY.md`).

## Validación

Todos los archivos PHP deben pasar sin errores de sintaxis:

```bash
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```
