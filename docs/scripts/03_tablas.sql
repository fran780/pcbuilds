CREATE TABLE `producto` (
    `id_producto` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_producto` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `precio` DOUBLE(10, 2),
    `stock` INT DEFAULT 0,
    `imagen` VARCHAR(255),
    `id_categoria` INT,
    `id_marca` INT,
    CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_producto` (`id_categoria`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_producto_marca` FOREIGN KEY (`id_marca`) REFERENCES `marca_producto` (`id_marca`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `categoria_producto` (
    `id_categoria` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_categoria` VARCHAR(100) NOT NULL
);

CREATE TABLE `marca_producto` (
    `id_marca` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_marca` VARCHAR(100) NOT NULL
);