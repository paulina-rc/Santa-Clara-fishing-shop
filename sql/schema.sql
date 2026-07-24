-- Tienda de Pesca Santa Clara — esquema de base de datos (Sprint 1)
-- Cargar con: mysql -u <usuario> -p tienda_pesca < sql/schema.sql

SET NAMES utf8mb4;

-- --------------------------------------------------------------------------
-- categorias
-- --------------------------------------------------------------------------
CREATE TABLE categorias (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(60)  NOT NULL,
    nombre     VARCHAR(80)  NOT NULL,
    orden      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    activa     TINYINT(1)   NOT NULL DEFAULT 1,
    creada_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categorias (slug, nombre, orden) VALUES
    ('canas',      'Cañas',      1),
    ('carretes',   'Carretes',   2),
    ('senuelos',   'Señuelos',   3),
    ('ropa',       'Ropa',       4),
    ('accesorios', 'Accesorios', 5),
    ('ofertas',    'Ofertas',    6);

-- --------------------------------------------------------------------------
-- productos
-- --------------------------------------------------------------------------
CREATE TABLE productos (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    categoria_id   INT UNSIGNED NOT NULL,
    nombre         VARCHAR(150) NOT NULL,
    marca          VARCHAR(80)  DEFAULT NULL,
    descripcion    TEXT,
    precio         DECIMAL(10,2) NOT NULL,
    imagen         VARCHAR(255) DEFAULT NULL,
    destacado      TINYINT(1)   NOT NULL DEFAULT 0,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_productos_categoria (categoria_id),
    KEY idx_productos_activo (activo),
    KEY idx_productos_destacado (destacado),
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- admins
-- --------------------------------------------------------------------------
CREATE TABLE admins (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario        VARCHAR(60)  NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    nombre         VARCHAR(100) NOT NULL,
    ultimo_login   DATETIME DEFAULT NULL,
    creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admins_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
