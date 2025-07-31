-- Active: 1750516697705@@127.0.0.1@3306@pcbuilds

CREATE TABLE `categoria_producto` (
    `id_categoria` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_categoria` VARCHAR(100) NOT NULL,
    `estado_categoria` VARCHAR(100) NOT NULL,

);

CREATE TABLE `marca_producto` (
    `id_marca` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_marca` VARCHAR(100) NOT NULL,
    `estado_marca` VARCHAR(100) NOT NULL,
);

CREATE TABLE `estado_producto` (
    `id_estado` INT AUTO_INCREMENT PRIMARY KEY,
    `estado` VARCHAR(3) NOT NULL
);

CREATE TABLE `producto` (
    `id_producto` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_producto` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `precio` DOUBLE(10, 2),
    `stock` INT DEFAULT 0,
    `imagen` VARCHAR(255),
    `id_categoria` INT,
    `id_marca` INT,
    `id_estado` INT,
    CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_producto` (`id_categoria`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_producto_marca` FOREIGN KEY (`id_marca`) REFERENCES `marca_producto` (`id_marca`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_producto_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado_producto` (`id_estado`) ON DELETE NO ACTION ON UPDATE NO ACTION
);