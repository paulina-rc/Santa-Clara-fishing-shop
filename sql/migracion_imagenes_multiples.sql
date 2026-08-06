-- Tienda de Pesca Santa Clara — migracion: multiples imagenes por producto
-- Cargar con: mysql -u <usuario> -p tienda_pesca < sql/migracion_imagenes_multiples.sql
--
-- ATENCION: esta migracion borra TODOS los productos existentes y elimina
-- la columna productos.imagen. Es intencional: los productos actuales son
-- de prueba, no hace falta preservarlos.
--
-- Despues de correrla, borrar a mano los archivos huerfanos que queden en
-- public/assets/uploads/ (dejar solo .gitkeep y .htaccess).

SET NAMES utf8mb4;

-- --------------------------------------------------------------------------
-- producto_imagenes
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS producto_imagenes (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id  INT UNSIGNED NOT NULL,
    archivo      VARCHAR(255) NOT NULL,
    orden        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    es_principal TINYINT(1)   NOT NULL DEFAULT 0,
    creada_en    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_producto_imagenes_producto (producto_id),
    KEY idx_producto_imagenes_principal (producto_id, es_principal),
    CONSTRAINT fk_producto_imagenes_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- Borrar productos de prueba (sin imagenes registradas todavia, nada que
-- se propague por el ON DELETE CASCADE de arriba)
-- --------------------------------------------------------------------------
DELETE FROM productos;

-- --------------------------------------------------------------------------
-- Eliminar el campo imagen antiguo (una sola foto por producto)
-- --------------------------------------------------------------------------
ALTER TABLE productos DROP COLUMN imagen;
