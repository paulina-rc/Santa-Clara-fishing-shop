# Tienda de Pesca Santa Clara

Sitio catálogo (sin carrito de compras) con panel de administración privado. El
dueño del negocio sube productos, fotos, descripciones y precios desde
`/admin`. El público solo navega el catálogo.

Stack: PHP 8+ puro (sin framework), MySQL 8+ / MySQLi orientado a objetos,
HTML/CSS/JS vanilla. Despliegue en VPS privado con Apache.

## Estructura del proyecto

```
tienda-pesca/
├── config/
│   └── config.example.php   → plantilla de configuración (copiar a config.php)
├── includes/
│   ├── db.php                → conexión MySQLi
│   ├── auth.php               → sesiones, login/logout, CSRF, requerir_login()
│   └── helpers.php            → e(), precio(), url_whatsapp(), slugify(), flash,
│                                 tarjeta_producto(), categoria_icono_svg(), recortar()
├── public/                    → DocumentRoot de Apache
│   ├── .htaccess
│   ├── index.php               → home pública: hero, categorías, destacados, contacto
│   ├── productos.php           → listado de productos con filtro por categoría (?cat=slug)
│   ├── producto.php            → ficha de producto (?id=X) con CTA de WhatsApp
│   ├── includes/                → parciales del sitio público (bloqueados por .htaccess)
│   │   ├── header.php            → promo bar + cabecera + nav dinámico + <head>/SEO
│   │   └── footer.php            → footer + cierre de </body></html>
│   ├── admin/
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── index.php           → dashboard
│   │   └── productos.php       → Sprint 3
│   └── assets/
│       ├── css/
│       │   ├── admin.css        → estilos del panel de administración
│       │   └── publico.css      → estilos del catálogo público (paleta/tipografías aprobadas)
│       ├── js/
│       │   ├── admin.js
│       │   └── publico.js       → menú hamburguesa móvil
│       ├── img/                 → hero.jpg/.jpeg/.png/.webp opcional (foto del hero, con fallback a gradiente)
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
CREATE USER 'tienda_pesca_user'@'localhost' IDENTIFIED BY 'una-contraseña-segura';
GRANT ALL PRIVILEGES ON tienda_pesca.* TO 'tienda_pesca_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Cargar el esquema

```bash
mysql -u tienda_pesca_user -p tienda_pesca < sql/schema.sql
```

Esto crea las tablas `categorias` (con las 6 categorías iniciales),
`productos` y `admins`.

## Configuración

```bash
cp config/config.example.php config/config.php
```

Edita `config/config.php` con los datos reales: `BASE_URL`, credenciales de
`DB_*`, número de WhatsApp, redes sociales, etc. **Este archivo no se sube al
repositorio** (está en `.gitignore`) porque contiene credenciales.

## Permisos de la carpeta uploads

En el VPS, el usuario del servidor web (normalmente `www-data`) debe poder
escribir en `public/assets/uploads/`:

```bash
sudo chown -R www-data:www-data public/assets/uploads
sudo chmod -R 775 public/assets/uploads
```

Esa carpeta tiene su propio `.htaccess` que deshabilita la ejecución de PHP,
así que aunque sea escribible, no se pueden ejecutar scripts subidos ahí.

## Crear el administrador inicial

1. Abre `sql/crear_admin.php` y cambia `$usuario`, `$password` (mínimo 8
   caracteres, distinta del placeholder) y `$nombre`.
2. Ejecuta desde el servidor:

   ```bash
   php sql/crear_admin.php
   ```

3. **Borra el archivo inmediatamente después de usarlo**:

   ```bash
   rm sql/crear_admin.php
   ```

   El script se niega a correr si no cambiaste la contraseña placeholder, y
   al finalizar imprime un recordatorio de borrarlo.

## Configuración de Apache

Módulos requeridos:

```bash
sudo a2enmod rewrite headers expires
sudo systemctl restart apache2
```

Ejemplo de VirtualHost (ajusta dominio y rutas):

```apache
<VirtualHost *:80>
    ServerName tiendapescasantaclara.com
    DocumentRoot /var/www/tienda-pesca/public

    <Directory /var/www/tienda-pesca/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/tienda-pesca-error.log
    CustomLog ${APACHE_LOG_DIR}/tienda-pesca-access.log combined
</VirtualHost>
```

`AllowOverride All` es necesario para que `public/.htaccess` y
`public/assets/uploads/.htaccess` funcionen.

## SSL con Certbot

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d tiendapescasantaclara.com -d www.tiendapescasantaclara.com
```

Certbot ajusta el VirtualHost automáticamente para servir HTTPS. Una vez
verificado que el sitio carga por HTTPS, **descomenta el bloque de
redirección** al inicio de `public/.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Esto fuerza que todo el tráfico HTTP se redirija a HTTPS.

## Catálogo público (Sprint 2)

Todo el contenido público se lee de la base de datos, no hay nada quemado en
el HTML.

- **`index.php`** — home con promo bar, hero, grid de categorías (leídas de
  `categorias WHERE activa=1 ORDER BY orden`), "Más vendidos" (`productos`
  con `activo=1 AND destacado=1`, límite 8) y banda de contacto. Si la BD
  está vacía, cada sección muestra un estado amable en vez de romperse.
- **`productos.php`** — listado con chips de filtro por categoría
  (`?cat=slug`). Si el slug no existe o está inactivo, muestra "categoría no
  encontrada" con vuelta al inicio en lugar de fallar.
- **`producto.php`** — ficha de producto (`?id=X`). Valida que el ID sea un
  entero y que el producto exista y esté activo; si no, responde 404 con una
  página amable. Incluye botón "Pedir por WhatsApp" con el mensaje
  precargado (nombre + precio) y una sección de 4 productos relacionados de
  la misma categoría.
- **`public/includes/header.php` / `footer.php`** — parciales reutilizables.
  Los puntos de entrada (`index.php`, `productos.php`, `producto.php`) hacen
  `require` de `config.php`, `db.php` y `helpers.php` **antes** de incluir
  estos parciales; los parciales no vuelven a requerirlos. `header.php`
  arma el `<head>` (título, meta description, Open Graph) a partir de las
  variables `$meta_titulo`, `$meta_descripcion` y `$meta_imagen` que cada
  página define antes del `require`. Ambos archivos están bloqueados a
  acceso HTTP directo vía `public/.htaccess`.
- **Foto del hero** — opcional. Si existe `public/assets/img/hero.jpg` (o
  `.jpeg`/`.png`/`.webp`), se muestra ahí; si no, el hero cae al degradado
  marino/teal definido en CSS. Sustituí ese archivo cuando el cliente mande
  la foto real.
- **Tarjeta de producto** — un solo formato (`tarjeta_producto()` en
  `includes/helpers.php`) reusado en la home, el listado y "también te
  puede interesar", para que el diseño no se desincronice entre páginas.

## Roadmap

- **Sprint 1 (completo):** base técnica — estructura de carpetas, conexión
  a base de datos, esquema SQL, autenticación de admin con CSRF y sesiones
  seguras, dashboard con contadores, seguridad de servidor (.htaccess,
  cabeceras, protección de uploads).
- **Sprint 2 (completo):** diseño público del catálogo — home (hero,
  categorías, destacados, contacto), listado por categoría con filtros,
  ficha de producto con CTA de WhatsApp y productos relacionados, header/
  footer reutilizables, responsive móvil/tablet/desktop, SEO básico
  (title, meta description, Open Graph).
- **Sprint 3:** CRUD de productos en el admin (`productos.php`) — alta, edición,
  borrado, subida y recorte de imágenes, marcar destacado/activo.

## Validación

Todos los archivos PHP deben pasar sin errores de sintaxis:

```bash
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```
