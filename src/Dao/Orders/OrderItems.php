<?php
namespace Dao\Orders;

use Dao\Table;
/*
CREATE TABLE `ordenes` (
    `orderid` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `usercod` BIGINT(10) NOT NULL,
    `transactionId` BIGINT,
    `order_status` VARCHAR(50) NOT NULL DEFAULT 'Pendiente',
    `shipping_status` VARCHAR(50) NOT NULL DEFAULT 'Tomando Orden', -- Estado del envío (En camino, En tienda, etc.)
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
*/

class OrderItems extends Table
{
    public static function getByOrderId(int $orderId)
    {
        $sql = "SELECT od.orderItemid, od.id_producto, od.cantidad, od.precio, p.nombre_producto
                FROM ordenes_detalles od
                LEFT JOIN producto p ON od.id_producto = p.id_producto
                WHERE od.orderid = :orderid";
        return self::obtenerRegistros($sql, ["orderid" => $orderid]);
    }

    public static function getTotalByOrderId(int $orderid)
    {
        $sql = "SELECT SUM(cantidad * precio) AS total FROM ordenes_detalle WHERE orderid = :orderid";
        $result = self::obtenerUnRegistro($sql, ["orderid" => $orderid]);
        return $result ? $result['total'] : 0;
    }

    public static function insertOrderItem(array $data)
    {
        $sql = "INSERT INTO ordenes_detalle (orderid, id_producto, cantidad, precio) VALUES (:orderid, :id_producto, :cantidad, :precio)";
        return self::executeNonQuery($sql, $data);
    }

    public static function deleteByOrderId(int $orderid)
    {
        $sql = "DELETE FROM ordenes_detalle WHERE orderid = :orderid";
        return self::executeNonQuery($sql, ["orderid" => $orderid]);
    }
}
