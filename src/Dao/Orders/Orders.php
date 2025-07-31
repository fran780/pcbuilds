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

class Orders extends Table{
    
     public static function getAll(
        int $usercod,
        string $orderId = "",
        string $orderStatus = "",    
        string $shippingStatus = "",
        int $page = 0,
        int $itemsPerPage = 10
     )
    {
        $baseSql = "SELECT o.orderid, o.usercod, o.order_status, o.shipping_status, o.orderdate, o.total, o.currency
                    FROM ordenes o
                    LEFT JOIN transactions t ON o.transactionId = t.transactionId
                    WHERE o.usercod = :usercod";
        $baseSqlCount = "SELECT COUNT(*) as total FROM ordenes WHERE usercod = :usercod";
        $conditions = [];
        $params = ["usercod" => $usercod];
        if ($orderId !== "") {
            $conditions[] = "o.orderid LIKE :orderid";
            $params["orderid"] = "%" . $orderId . "%";
        }
        if ($orderStatus !== "") {
            $conditions[] = "o.order_status = :order_status";
            $params["order_status"] = $orderStatus;
        }
        if ($shippingStatus !== "") {
            $conditions[] = "o.shipping_status = :shipping_status";
            $params["shipping_status"] = $shippingStatus;
        }
        if (count($conditions) > 0) {
            $where = " AND " . implode(" AND ", $conditions);
            $baseSql .= $where;
            $baseSqlCount .= $where;
        }

        $total = intval(self::obtenerUnRegistro($baseSqlCount, $params)["total"]);
        $pagesCount = max(ceil($total / $itemsPerPage), 1);
        if ($page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }
        if ($page < 0) {
            $page = 0;
        }
        $baseSql .= " ORDER BY o.orderdate DESC LIMIT " . ($page * $itemsPerPage) . ", " . $itemsPerPage;

        $orders = self::obtenerRegistros($baseSql, $params);

        return [
            "orders" => $orders,
            "total" => $total,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getByUserId(int $usercod)
    {
        $sql = "SELECT o.orderid, o.usercod, o.order_status, o.orderdate, o.shipping_status, t.currency
                FROM ordenes o
                LEFT JOIN transactions t ON o.transactionId = t.transactionId
                WHERE o.usercod = :usercod
                ORDER BY o.orderdate ASC";
        return self::obtenerRegistros($sql, ["usercod" => $usercod]);
    }

    public static function getByOrderId(int $orderid)
    {
        $sql = "SELECT orderItemId, id_producto, cantidad, precio FROM ordenes_detalle WHERE orderid = :orderid";
        return self::obtenerRegistros($sql, ["orderid" => $orderid]);
    }


      public static function insertOrder(array $data)
    {
       $sql = "INSERT INTO ordenes (usercod, order_status, shipping_status, orderdate, total, currency) 
        VALUES (:usercod, :order_status, :shipping_status, :orderdate, :total, :currency)";

        self::executeNonQuery($sql, $data);
        return self::getLastInsertId(); 
    }

    public static function deleteOrder(int $orderId)
    {
        $sql = "DELETE FROM ordenes WHERE orderid = :orderid";
        return self::executeNonQuery($sql, ["orderid" => $orderId]);
    }

    public static function getById(int $orderid)
    {
        $sql = "SELECT o.orderid, o.usercod, o.order_status, o.shipping_status, o.orderdate, u.username, pt.currency
                FROM ordenes o
                LEFT JOIN usuario u ON o.usercod = u.usercod
                LEFT JOIN transactions pt ON o.transactionId = pt.transactionId
                WHERE o.orderid = :orderid";
        return self::obtenerUnRegistro($sql, ["orderid" => $orderid]);
    }

    public static function updateShippingStatus($orderid, $shipping_status)
    {
        $sql = "UPDATE ordenes SET shipping_status = :shipping_status WHERE orderid = :orderid";
        return self::executeNonQuery($sql, [
            "orderid" => $orderid,
            "shipping_status" => $shipping_status
        ]);
    }

    public static function updatePaypalTransaction(int $orderid, int $paypalTransactionId)
    {
        $sql = "UPDATE ordenes SET transactionId = :transactionId, order_status = 'Pagado' WHERE orderid = :orderid";
        return self::executeNonQuery($sql, [
            "transactionId" => $paypalTransactionId,
            "orderid" => $orderid
        ]);
    }




}