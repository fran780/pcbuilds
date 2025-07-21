-- Active: 1750516697761@@127.0.0.1@3306@pcbuilds
CREATE TABLE `usuario` (
    `usercod` bigint(10) NOT NULL AUTO_INCREMENT,
    `useremail` varchar(80) DEFAULT NULL,
    `username` varchar(80) DEFAULT NULL,
    `userpswd` varchar(128) DEFAULT NULL,
    `userfching` datetime DEFAULT NULL,
    `userpswdest` char(3) DEFAULT NULL,
    `userpswdexp` datetime DEFAULT NULL,
    `userest` char(3) DEFAULT NULL,
    `useractcod` varchar(128) DEFAULT NULL,
    `userpswdchg` varchar(128) DEFAULT NULL,
    `usertipo` char(3) DEFAULT NULL COMMENT 'Tipo de Usuario, Normal, Consultor o Cliente',
    PRIMARY KEY (`usercod`),
    UNIQUE KEY `useremail_UNIQUE` (`useremail`),
    KEY `usertipo` (
        `usertipo`,
        `useremail`,
        `usercod`,
        `userest`
    )
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `roles` (
    `rolescod` varchar(128) NOT NULL,
    `rolesdsc` varchar(45) DEFAULT NULL,
    `rolesest` char(3) DEFAULT NULL,
    PRIMARY KEY (`rolescod`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `roles_usuarios` (
    `usercod` bigint(10) NOT NULL,
    `rolescod` varchar(128) NOT NULL,
    `roleuserest` char(3) DEFAULT NULL,
    `roleuserfch` datetime DEFAULT NULL,
    `roleuserexp` datetime DEFAULT NULL,
    PRIMARY KEY (`usercod`, `rolescod`),
    KEY `rol_usuario_key_idx` (`rolescod`),
    CONSTRAINT `rol_usuario_key` FOREIGN KEY (`rolescod`) REFERENCES `roles` (`rolescod`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `usuario_rol_key` FOREIGN KEY (`usercod`) REFERENCES `usuario` (`usercod`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `funciones` (
    `fncod` varchar(255) NOT NULL,
    `fndsc` varchar(255) DEFAULT NULL,
    `fnest` char(3) DEFAULT NULL,
    `fntyp` char(3) DEFAULT NULL,
    PRIMARY KEY (`fncod`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `funciones_roles` (
    `rolescod` varchar(128) NOT NULL,
    `fncod` varchar(255) NOT NULL,
    `fnrolest` char(3) DEFAULT NULL,
    `fnexp` datetime DEFAULT NULL,
    PRIMARY KEY (`rolescod`, `fncod`),
    KEY `rol_funcion_key_idx` (`fncod`),
    CONSTRAINT `funcion_rol_key` FOREIGN KEY (`rolescod`) REFERENCES `roles` (`rolescod`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `rol_funcion_key` FOREIGN KEY (`fncod`) REFERENCES `funciones` (`fncod`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `bitacora` (
    `bitacoracod` int(11) NOT NULL AUTO_INCREMENT,
    `bitacorafch` datetime DEFAULT NULL,
    `bitprograma` varchar(255) DEFAULT NULL,
    `bitdescripcion` varchar(255) DEFAULT NULL,
    `bitobservacion` mediumtext,
    `bitTipo` char(3) DEFAULT NULL,
    `bitusuario` bigint(18) DEFAULT NULL,
    PRIMARY KEY (`bitacoracod`)
) ENGINE = InnoDB AUTO_INCREMENT = 10 DEFAULT CHARSET = utf8;

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