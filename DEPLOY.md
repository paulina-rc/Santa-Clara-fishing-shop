# Despliegue — Tienda de Pesca Santa Clara

Guía paso a paso para poner el sitio en producción en un VPS Ubuntu/Debian.

---

## 1. Pre-requisitos del VPS

- Ubuntu 22.04+ o Debian 12+
- Apache 2.4+
- PHP 8.1+ con la extensión `mysqli`
- MySQL 8+ (o MariaDB 10.6+)
- Un dominio apuntando (registro A) a la IP del servidor

Instalar todo con:

```bash
sudo apt update
sudo apt install apache2 php php-mysqli mysql-server certbot python3-certbot-apache
```

Verifica las versiones instaladas:

```bash
php -v
mysql --version
apache2 -v
```

---

## 2. Obtener el código en el servidor

```bash
sudo mkdir -p /var/www/tienda-pesca
sudo chown $USER:$USER /var/www/tienda-pesca
git clone <URL_DEL_REPOSITORIO> /var/www/tienda-pesca
```

---

## 3. Configuración de Apache

Habilita los módulos requeridos:

```bash
sudo a2enmod rewrite headers expires
sudo systemctl restart apache2
```

Crea el VirtualHost `/etc/apache2/sites-available/tienda-pesca.conf`:

```apache
<VirtualHost *:80>
    ServerName tiendapescasantaclara.com
    ServerAlias www.tiendapescasantaclara.com

    DocumentRoot /var/www/tienda-pesca/public

    <Directory /var/www/tienda-pesca/public>
        Options -Indexes -MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/tienda-pesca-error.log
    CustomLog ${APACHE_LOG_DIR}/tienda-pesca-access.log combined
</VirtualHost>
```

`AllowOverride All` es necesario para que `public/.htaccess` (reglas de
reescritura, bloqueo de carpetas sensibles, cabeceras de seguridad) surta
efecto.

Habilita el sitio y desactiva el default:

```bash
sudo a2ensite tienda-pesca.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

---

## 4. Setup de MySQL

Crea la base de datos y un usuario con permisos mínimos (solo sobre esa BD,
no root):

```sql
CREATE DATABASE tienda_pesca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tienda_pesca_user'@'localhost' IDENTIFIED BY 'una-contrasena-fuerte-y-unica';
GRANT SELECT, INSERT, UPDATE, DELETE ON tienda_pesca.* TO 'tienda_pesca_user'@'localhost';
FLUSH PRIVILEGES;
```

Carga el esquema:

```bash
mysql -u tienda_pesca_user -p tienda_pesca < /var/www/tienda-pesca/sql/schema.sql
```

---

## 5. Configuración de la aplicación

```bash
cd /var/www/tienda-pesca
cp config/config.example.php config/config.php
nano config/config.php
```

Edita en `config/config.php`:

- `DB_NAME`, `DB_USER`, `DB_PASS` con los valores creados en el paso 4.
- `BASE_URL` con el dominio real, **sin slash final**, ej:
  `https://tiendapescasantaclara.com` (una vez tengas HTTPS activo, ver
  paso 7 — mientras tanto puede quedar en `http://`).
- Datos de contacto (`WHATSAPP_NUMERO`, `INSTAGRAM_USUARIO`,
  `EMAIL_CONTACTO`, `DIRECCION`) si cambian respecto al valor por defecto.

---

## 6. Permisos

```bash
sudo chown -R www-data:www-data /var/www/tienda-pesca
sudo find /var/www/tienda-pesca -type d -exec chmod 755 {} \;
sudo find /var/www/tienda-pesca -type f -exec chmod 644 {} \;

sudo chmod 775 /var/www/tienda-pesca/public/assets/uploads
sudo chmod 640 /var/www/tienda-pesca/config/config.php
```

`uploads/` necesita ser escribible por el usuario del servidor web (Apache
sube las imágenes ahí); `config.php` contiene credenciales y debe quedar
ilegible para otros usuarios del sistema.

---

## 7. SSL con Certbot

```bash
sudo certbot --apache -d tiendapescasantaclara.com -d www.tiendapescasantaclara.com
```

Certbot ajusta el VirtualHost automáticamente para servir HTTPS y configura
la renovación automática. Una vez verificado que el sitio carga por HTTPS,
**descomenta el bloque de redirección** al inicio de `public/.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Esto fuerza que todo el tráfico HTTP se redirija a HTTPS. Recarga Apache:

```bash
sudo systemctl reload apache2
```

---

## 8. Crear el admin inicial

```bash
cd /var/www/tienda-pesca
nano sql/crear_admin.php
```

Edita las variables `$usuario`, `$password` y `$nombre` con las credenciales
reales del dueño de la tienda (contraseña de al menos 8 caracteres), luego
ejecútalo:

```bash
php sql/crear_admin.php
```

**Bórralo inmediatamente después de usarlo** — nunca debe quedar en el
servidor:

```bash
rm sql/crear_admin.php
```

---

## 9. Checklist post-despliegue

- [ ] El login (`/admin/login.php`) funciona con las credenciales creadas.
- [ ] Se puede subir un producto con imagen desde `/admin/producto_nuevo.php`.
- [ ] El producto recién creado aparece en el sitio público (home si está
      destacado, listado de su categoría y su ficha individual).
- [ ] El sitio fuerza HTTPS (una petición a `http://` redirige a `https://`).
- [ ] `/config`, `/includes` y `/sql` no son accesibles por HTTP — probar
      visitando, por ejemplo, `https://tudominio.com/config/config.php` y
      confirmar que responde 403/404, no el contenido del archivo.
- [ ] Los uploads no ejecutan PHP:
  - Subir un archivo con extensión `.php` como imagen debe ser **rechazado**
    por la validación de `validar_imagen()` (MIME real, no la extensión).
  - Como verificación adicional, coloca manualmente un archivo `.php` de
    prueba dentro de `public/assets/uploads/` y visítalo por HTTP — debe
    devolver 403 (bloqueado por `uploads/.htaccess`), no ejecutarse.
- [ ] `sql/crear_admin.php` fue borrado del servidor.

---

## No incluido en este sprint

- Sistema de logs de auditoría.
- Múltiples imágenes por producto.
- Variantes de producto.
- Notificaciones por email.
- Panel para editar categorías (por ahora se editan directo en la BD).
- Panel para editar textos del sitio (nombre del negocio, WhatsApp, etc. —
  viven en `config/config.php`).
