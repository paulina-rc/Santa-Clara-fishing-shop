-- Tienda de Pesca Santa Clara — migracion: subcategorias (segundo nivel de categorias)
-- Cargar con: mysql -u <usuario> -p tienda_pesca < sql/migracion_subcategorias.sql
--
-- IMPORTANTE: el INSERT final asume que los id de categorias son:
--   1=Cañas, 2=Carretes, 3=Señuelos, 4=Ropa, 5=Accesorios, 6=Ofertas
-- Verificar contra `SELECT id, slug, nombre FROM categorias ORDER BY orden;`
-- antes de correr. Si no coinciden, ajustar los categoria_id del INSERT.

SET NAMES utf8mb4;

-- --------------------------------------------------------------------------
-- subcategorias
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS subcategorias (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    categoria_id INT UNSIGNED NOT NULL,
    slug         VARCHAR(60)  NOT NULL,
    nombre       VARCHAR(100) NOT NULL,
    orden        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    activa       TINYINT(1)   NOT NULL DEFAULT 1,
    creada_en    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_subcategorias_cat_slug (categoria_id, slug),
    CONSTRAINT fk_subcategorias_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- productos: subcategoria opcional
-- --------------------------------------------------------------------------
ALTER TABLE productos
    ADD COLUMN subcategoria_id INT UNSIGNED NULL AFTER categoria_id,
    ADD KEY idx_productos_subcategoria (subcategoria_id),
    ADD CONSTRAINT fk_productos_subcategoria
        FOREIGN KEY (subcategoria_id) REFERENCES subcategorias (id)
        ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------------------------
-- Subcategorias iniciales segun acuerdo con el cliente
-- --------------------------------------------------------------------------
INSERT INTO subcategorias (categoria_id, slug, nombre, orden) VALUES
    -- Cañas (id=1)
    (1, 'spinning',    'Spinning',    1),
    (1, 'baitcasting', 'Baitcasting', 2),

    -- Carretes (id=2)
    (2, 'spinning',    'Spinning',    1),
    (2, 'baitcasting', 'Baitcasting', 2),

    -- Señuelos (id=3)
    (3, 'mar', 'Mar', 1),
    (3, 'rio', 'Río', 2),

    -- Ropa (id=4) — sin subcategorias por decision del cliente

    -- Accesorios (id=5)
    (5, 'alicates-grips',       'Alicates y Grips',      1),
    (5, 'oxigenadores',         'Oxigenadores',           2),
    (5, 'anzuelos',             'Anzuelos',               3),
    (5, 'bolsos-chalecos',      'Bolsos y Chalecos',      4),
    (5, 'cajas-pesca',          'Cajas de pesca',         5),
    (5, 'cuchillos-afiladores', 'Cuchillos y Afiladores', 6),
    (5, 'linea-trenzada',       'Línea Trenzada',         7),
    (5, 'lentes',               'Lentes',                 8);

    -- Ofertas (id=6) — sin subcategorias (aplica a productos rebajados de cualquier tipo)
