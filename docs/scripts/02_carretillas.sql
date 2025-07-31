CREATE TABLE
    `carretilla` (
        `usercod` BIGINT(10) NOT NULL,
        `id_producto` int(11) NOT NULL,
        `crrctd` INT(5) NOT NULL,
        `crrprc` DECIMAL(12, 2) NOT NULL,
        `crrfching` DATETIME NOT NULL,
        PRIMARY KEY (`usercod`, `id_producto`),
        INDEX `id_producto_idx` (`id_producto` ASC),
        CONSTRAINT `carretilla_user_key` FOREIGN KEY (`usercod`) REFERENCES `usuario` (`usercod`) ON DELETE NO ACTION ON UPDATE NO ACTION,
        CONSTRAINT `carretilla_prd_key` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION
    );

CREATE TABLE
    `carretillaanon` (
        `anoncod` varchar(128) NOT NULL,
        `id_producto` int (18) NOT NULL,
        `crrctd` int(5) NOT NULL,
        `crrprc` decimal(12, 2) NOT NULL,
        `crrfching` datetime NOT NULL,
        PRIMARY KEY (`anoncod`, `id_producto`),
        INDEX `id_producto_idx` (`id_producto` ASC),
        CONSTRAINT `carretillaanon_prd_key` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION
    );

CREATE TABLE `ordenes` (
    `orderid` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `usercod` BIGINT(10) NOT NULL,
    `transactionId` BIGINT,
    `order_status` VARCHAR(50) NOT NULL DEFAULT 'PENDING', -- Estado de la orden "COMPLETED", "PENDING", "FAILED"
    `shipping_status` VARCHAR(50) NOT NULL DEFAULT 'PENDING', -- "SHIPPED", "PENDING", "DELIVERED"
    `total` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(5) NOT NULL,
    `orderdate` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usercod`) REFERENCES `usuario` (`usercod`),
    FOREIGN KEY (`transactionId`) REFERENCES `transactions` (`transactionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ordenes_detalle` (
    `orderItemid` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `orderid` BIGINT NOT NULL,
    `id_producto` INT(11) NOT NULL,
    `cantidad` INT(5) NOT NULL,
    `precio` DECIMAL(12, 2) NOT NULL,
    `transdate` DATETIME NOT NULL,
    FOREIGN KEY (`orderid`) REFERENCES `ordenes` (`orderid`) ON DELETE CASCADE,
    FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`)
);
    

    CREATE TABLE `transactions` (
    `transactionId` BIGINT NOT NULL AUTO_INCREMENT,
    `usercod` BIGINT(10) NOT NULL,
    `orderid` VARCHAR(50) NOT NULL,
    `transdate` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `transstatus` VARCHAR(45) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(5) NOT NULL,
    `orderjson` JSON NOT NULL,
    PRIMARY KEY (`transactionId`),
    KEY `fk_transactions_user_idx` (`usercod`),
    CONSTRAINT `fk_transactions_user` FOREIGN KEY (`usercod`) REFERENCES `usuario`(`usercod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* * Trigger para actualizar el transactionId en la tabla ordenes
 * después de insertar una nueva transacción.
 */
DELIMITER $$
CREATE TRIGGER after_transaction_insert
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    DECLARE last_orderid BIGINT;
    
    -- Obtener el último orderid de la tabla ordenes
    SELECT orderid INTO last_orderid FROM ordenes ORDER BY orderid DESC LIMIT 1;

    -- Actualizar el último registro de ordenes con el transactionId recién insertado
    UPDATE ordenes SET transactionId = NEW.transactionId WHERE orderid = last_orderid;
END $$


INSERT INTO `funciones` (`fncod`, `fndsc`, `fnest`, `fntyp`) VALUES
('Controllers\\Checkout\\Checkout', 'Acceso al Checkout para clientes', 'ACT', 'CTR'),
('Controllers\\Checkout\\History', 'Listado Historial de Transacciones', 'ACT', 'CTR'),
('Controllers\\Checkout\\HistoryDetail', 'Formulario de Historial de Transacciones', 'ACT', 'CTR'),
('Menu_TransHist', 'Menu_Transacciones', 'ACT', 'MNU');


INSERT INTO `roles` (`rolescod`, `rolesdsc`, `rolesest`) VALUES
('ADMIN', 'Administrador', 'ACT'),
('PBL', 'Cliente', 'ACT'),
('ECI', 'Encargado de inventario', 'ACT');

INSERT INTO `funciones_roles` (`rolescod`, `fncod`, `fnrolest`, `fnexp`) VALUES
('PBL', 'Controllers\\Checkout\\Checkout', 'ACT', '2026-07-27 03:14:24'),
('PBL', 'Controllers\\Checkout\\History', 'ACT', '2026-07-27 03:14:24'),
('PBL', 'Controllers\\Checkout\\HistoryDetail', 'ACT', '2026-07-27 03:14:24'),
('PBL', 'Menu_TransHist', 'ACT', '2026-07-27 03:53:23');

DELIMITER $$

/* Procedimiento para agregar rol de cliente */
CREATE PROCEDURE addClientRol(IN p_usercod BIGINT)
BEGIN
    -- Asignar directamente el valor recibido
    DECLARE v_rolescod VARCHAR(10);
    SET v_rolescod = 'PBL';

    -- Insertar el rol para el usuario recién creado
    INSERT INTO roles_usuarios (
        usercod, rolescod, roleuserest, roleuserfch, roleuserexp
    )
    VALUES (
        p_usercod, v_rolescod, 'ACT', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR)
    );
END $$

DELIMITER ;
