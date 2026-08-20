# Tienda de Pesca Santa Clara

Sitio de catálogo con panel administrativo privado para una tienda de equipo de pesca deportiva en Santa Clara, San Carlos, Costa Rica.


**Sitio en producción:** [tiendapescasantaclara.com](https://tiendapescasantaclara.com)
**Diseño y desarrollo:** PR.DEV

---

## Estructura

```
Santa-Clara-fishing-shop/
├── config/
│   ├── config.example.php        → plantilla; copiar a config.php
│   └── config.php                → NO versionado (credenciales)
├── includes/                     → fuera del DocumentRoot
│   ├── db.php                    → conexión MySQLi ($mysqli)
│   ├── auth.php                  → sesiones, login/logout, CSRF, requerir_login()
│   ├── helpers.php               → e(), precio(), slugify(), url_whatsapp(), flash,
│   │                               tarjeta_producto(), imágenes de producto
│   └── productos_helpers.php     → validar_imagen(), borrar_imagen(),
│                                   guardar_imagenes_multiples()
├── public/                       → DocumentRoot de Apache
│   ├── .htaccess
│   ├── index.php                 → home (destacados)
│   ├── productos.php             → listado por categoría/subcategoría
│   ├── producto.php              → ficha de producto
│   ├── buscar.php                → buscador
│   ├── nosotros.php              → historia y contacto
│   ├── includes/
│   │   ├── header.php            → nav, meta tags, favicons
│   │   └── footer.php            → footer, WhatsApp / redes
│   ├── admin/
│   │   ├── login.php · logout.php · index.php · cuenta.php
│   │   ├── productos.php         → listado con filtros y paginación
│   │   ├── producto_nuevo.php · producto_editar.php
│   │   ├── producto_eliminar.php · producto_toggle.php   → endpoints POST
│   │   ├── foto_eliminar.php · foto_marcar_principal.php → endpoints POST
│   │   ├── subcategorias.php · subcategoria_nueva.php
│   │   ├── subcategoria_editar.php · subcategoria_eliminar.php
│   │   ├── subcategoria_toggle.php
│   │   └── subcategorias_json.php → subcategorías por categoría (para el form)
│   └── assets/
│       ├── css/  → admin.css, publico.css
│       ├── js/   → admin.js, publico.js
│       ├── img/  → logo, favicons, fotos del sitio
│       └── uploads/ → fotos de productos (escribible, sin ejecución de PHP)
├── sql/
│   ├── schema.sql
│   ├── migracion_subcategorias.sql
│   ├── migracion_imagenes_multiples.sql
│   └── crear_admin.php           → script de un solo uso
├── DEPLOY.md                     → guía de despliegue en VPS
└── README.md
```

### Modelo de datos

```
categorias ──< subcategorias
     │              │
     └──────< productos >──── producto_imagenes
                 │
              admins (independiente)
```


## Author

**Paulina Rojas** — [@paulina-rc](https://github.com/paulina-rc)

## License

MIT License. See [LICENSE](LICENSE) for details.
