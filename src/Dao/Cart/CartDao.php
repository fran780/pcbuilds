<?php

namespace Dao\Cart;

class CartDAO extends \Dao\Table
{

    // Function to get a product by its ID
    // Returns an array with the product details
    // El cual se añadira a la carretilla sea anónima o autorizada
    public static function getProductosDisponibles()
    {
        $sqlAllProductosActivos = "SELECT * from producto where id_estado in ('1');";
        $productosDisponibles = self::obtenerRegistros($sqlAllProductosActivos, array());

        //Sacar el stock de productos con carretilla autorizada
        $deltaAutorizada = \Utilities\Cart\CartFns::getAuthTimeDelta();
        $sqlCarretillaAutorizada = "select id_producto, sum(crrctd) as reserved
            from carretilla where TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            group by id_producto;";
        $prodsCarretillaAutorizada = self::obtenerRegistros(
            $sqlCarretillaAutorizada,
            array("delta" => $deltaAutorizada)
        );
        //Sacar el stock de productos con carretilla no autorizada
        $deltaNAutorizada = \Utilities\Cart\CartFns::getUnAuthTimeDelta();
        $sqlCarretillaNAutorizada = "select id_producto, sum(crrctd) as reserved
            from carretillaanon where TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            group by id_producto;";
        $prodsCarretillaNAutorizada = self::obtenerRegistros(
            $sqlCarretillaNAutorizada,
            array("delta" => $deltaNAutorizada)
        );
        $productosCurados = array();
        foreach ($productosDisponibles as $producto) {
            if (!isset($productosCurados[$producto["id_producto"]])) {
                $productosCurados[$producto["id_producto"]] = $producto;
            }
        }
        foreach ($prodsCarretillaAutorizada as $producto) {
            if (isset($productosCurados[$producto["id_producto"]])) {
                $productosCurados[$producto["id_producto"]]["stock"] = max(
                    0,
                    $productosCurados[$producto["id_producto"]]["stock"] - $producto["reserved"]
                );
            }
        }
        foreach ($prodsCarretillaNAutorizada as $producto) {
            if (isset($productosCurados[$producto["id_producto"]])) {
                $productosCurados[$producto["id_producto"]]["stock"] = max(
                    0,
                    $productosCurados[$producto["id_producto"]]["stock"] - $producto["reserved"]
                );
            }
        }
        $productosDisponibles = null;
        $prodsCarretillaAutorizada = null;
        $prodsCarretillaNAutorizada = null;
        return $productosCurados;
    }

    public static function getProductoDisponible($productId)
    {
        $sqlAllProductosActivos = "SELECT * from producto where id_estado in ('1') and id_producto=:productId;";
        $productosDisponibles = self::obtenerRegistros($sqlAllProductosActivos, array("productId" => $productId));

        //Sacar el stock de productos con carretilla autorizada
        $deltaAutorizada = \Utilities\Cart\CartFns::getAuthTimeDelta();
        $sqlCarretillaAutorizada = "select id_producto, sum(crrctd) as reserved
            from carretilla where id_producto=:productId and TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            group by id_producto;";
        $prodsCarretillaAutorizada = self::obtenerRegistros(
            $sqlCarretillaAutorizada,
            array("productId" => $productId, "delta" => $deltaAutorizada)
        );
        //Sacar el stock de productos con carretilla no autorizada
        $deltaNAutorizada = \Utilities\Cart\CartFns::getUnAuthTimeDelta();
        $sqlCarretillaNAutorizada = "select id_producto, sum(crrctd) as reserved
            from carretillaanon where id_producto = :productId and TIME_TO_SEC(TIMEDIFF(now(), crrfching)) <= :delta
            group by id_producto;";
        $prodsCarretillaNAutorizada = self::obtenerRegistros(
            $sqlCarretillaNAutorizada,
            array("productId" => $productId, "delta" => $deltaNAutorizada)
        );
        $productosCurados = array();
        foreach ($productosDisponibles as $producto) {
            if (!isset($productosCurados[$producto["id_producto"]])) {
                $productosCurados[$producto["id_producto"]] = $producto;
            }
        }
        foreach ($prodsCarretillaAutorizada as $producto) {
            if (isset($productosCurados[$producto["id_producto"]])) {
                $productosCurados[$producto["id_producto"]]["stock"] = max(
                    0,
                    $productosCurados[$producto["id_producto"]]["stock"] - $producto["reserved"]
                );
            }
        }
        foreach ($prodsCarretillaNAutorizada as $producto) {
            if (isset($productosCurados[$producto["id_producto"]])) {
                $productosCurados[$producto["id_producto"]]["stock"] = max(
                    0,
                    $productosCurados[$producto["id_producto"]]["stock"] - $producto["reserved"]
                );
            }
        }
        $productosDisponibles = null;
        $prodsCarretillaAutorizada = null;
        $prodsCarretillaNAutorizada = null;
        return $productosCurados[$productId];
    }

    //Function para agregar un producto a la carretilla anónima
    //Si el producto ya existe, se actualiza la cantidad
    public static function addToAnonCart(
        int $productId,
        string $anonCod,
        int $amount,
        float $price
    ) {
        $validateSql = "SELECT * from carretillaanon where anoncod = :anoncod and id_producto = :id_producto";
        $producto = self::obtenerUnRegistro($validateSql, ["anoncod" => $anonCod, "id_producto" => $productId]);

        if ($producto) {
            if ($producto["crrctd"] + $amount <= 0) {
                $deleteSql = "DELETE from carretillaanon where anoncod = :anoncod and id_producto = :id_producto;";
                return self::executeNonQuery($deleteSql, ["anoncod" => $anonCod, "id_producto" => $productId]);
            } else {
                $updateSql = "UPDATE carretillaanon set crrctd = crrctd + :amount where anoncod = :anoncod and id_producto = :id_producto";
                return self::executeNonQuery($updateSql, ["anoncod" => $anonCod, "amount" => $amount, "id_producto" => $productId]);
            }
        } else {
            if ($amount > 0) {
                return self::executeNonQuery(
                    "INSERT INTO carretillaanon (anoncod, id_producto, crrctd, crrprc, crrfching) VALUES (:anoncod, :id_producto, :crrctd, :crrprc, NOW());",
                    ["anoncod" => $anonCod, "id_producto" => $productId, "crrctd" => $amount, "crrprc" => $price]
                );
            }
            return 0;
        }
    }


    //Function para obtener la carretilla anónima
    //Devuelve un array con los productos y sus cantidades
    public static function getAnonCart(string $anonCod)
    {
        return self::obtenerRegistros("SELECT a.*, b.crrctd, b.crrprc, b.crrfching FROM producto a inner join carretillaanon b on a.id_producto = b.id_producto where b.anoncod=:anoncod;", ["anoncod" => $anonCod]);
    }

    //Function para obtener la carretilla autorizada
    //Devuelve un array con los productos y sus cantidades
    public static function getAuthCart(int $usercod)
    {
        return self::obtenerRegistros("SELECT a.*, b.crrctd, b.crrprc, b.crrfching FROM producto a inner join carretilla b on a.id_producto = b.id_producto where b.usercod=:usercod;", ["usercod" => $usercod]);
    }

    //Function para agregar un producto a la carretilla autorizada
    //Si el producto ya existe, se actualiza la cantidad
    public static function addToAuthCart(
        int $productId,
        int $usercod,
        int $amount,
        float $price
    ) {
        $validateSql = "SELECT * from carretilla where usercod = :usercod and id_producto = :id_producto";
        $producto = self::obtenerUnRegistro($validateSql, ["usercod" => $usercod, "id_producto" => $productId]);
        if ($producto) {
            if ($producto["crrctd"] + $amount <= 0) {
                $deleteSql = "DELETE from carretilla where usercod = :usercod and id_producto = :id_producto;";
                return self::executeNonQuery($deleteSql, ["usercod" => $usercod, "id_producto" => $productId]);
            } else {
                $updateSql = "UPDATE carretilla set crrctd = crrctd + :amount where usercod = :usercod and id_producto = :id_producto";
                return self::executeNonQuery($updateSql, ["usercod" => $usercod, "amount" => $amount, "id_producto" => $productId]);
            }
        } else {
            if ($amount > 0) {
                return self::executeNonQuery(
                    "INSERT INTO carretilla (usercod, id_producto, crrctd, crrprc, crrfching) VALUES (:usercod, :id_producto, :crrctd, :crrprc, NOW());",
                    ["usercod" => $usercod, "id_producto" => $productId, "crrctd" => $amount, "crrprc" => $price]
                );
            }
            return 0;
        }
    }

    //Function para mover los productos de la carretilla anónima a la carretilla autorizada para el momento de pago
    public static function moveAnonToAuth(
        string $anonCod,
        int $usercod
    ) {
        $sqlstr = "INSERT INTO carretilla (userCod, id_producto, crrctd, crrprc, crrfching)
        SELECT :usercod, id_producto, crrctd, crrprc, NOW() FROM carretillaanon where anoncod = :anoncod
        ON DUPLICATE KEY UPDATE carretilla.crrctd = carretilla.crrctd + carretillaanon.crrctd;";

        $deleteSql = "DELETE FROM carretillaanon where anoncod = :anoncod;";
        self::executeNonQuery($sqlstr, ["anoncod" => $anonCod, "usercod" => $usercod]);
        self::executeNonQuery($deleteSql, ["anoncod" => $anonCod]);
    }

    public static function getProducto($productId)
    {
        $sqlProducto = "SELECT * FROM producto WHERE id_producto = :id_producto;";
        $producto = self::obtenerUnRegistro($sqlProducto, ["id_producto" => $productId]);

        if (!$producto) {
            return null; // No se encontró el producto
        }

        // Calcular reservas en carretilla autorizada
        $deltaAutorizada = \Utilities\Cart\CartFns::getAuthTimeDelta();
        $sqlAutorizada = "SELECT SUM(crrctd) as reserved FROM carretilla 
                      WHERE id_producto = :id_producto AND TIME_TO_SEC(TIMEDIFF(NOW(), crrfching)) <= :delta;";
        $resAutorizada = self::obtenerUnRegistro($sqlAutorizada, [
            "id_producto" => $productId,
            "delta" => $deltaAutorizada
        ]);

        // Calcular reservas en carretilla anónima
        $deltaNoAutorizada = \Utilities\Cart\CartFns::getUnAuthTimeDelta();
        $sqlNoAutorizada = "SELECT SUM(crrctd) as reserved FROM carretillaanon 
                        WHERE id_producto = :id_producto AND TIME_TO_SEC(TIMEDIFF(NOW(), crrfching)) <= :delta;";
        $resNoAutorizada = self::obtenerUnRegistro($sqlNoAutorizada, [
            "id_producto" => $productId,
            "delta" => $deltaNoAutorizada
        ]);

        // Calcular stock real
        $reservados = intval($resAutorizada["reserved"] ?? 0) + intval($resNoAutorizada["reserved"] ?? 0);
        $producto["stock_disponible"] = max(0, $producto["stock"] - $reservados);
        $producto["stock"] = $producto["stock_disponible"];

        return $producto;
    }

    public static function getTotalCart(int $usercod)
    {
        $sql = "SELECT SUM(crrctd * crrprc) as total FROM carretilla WHERE usercod = :usercod;";
        $result = self::obtenerUnRegistro($sql, ["usercod" => $usercod]);
        return $result ? floatval($result["total"]) : 0.0;
    }


    public static function finalizeCart(int $usercod)
    {
        $itemsSql = "SELECT id_producto, crrctd FROM carretilla WHERE usercod = :usercod;";
        $items = self::obtenerRegistros($itemsSql, ["usercod" => $usercod]);

        foreach ($items as $item) {
            $updateSql = "UPDATE producto SET stock = stock - :crrctd WHERE id_producto = :id_producto;";
            self::executeNonQuery(
                $updateSql,
                [
                    "crrctd" => $item["crrctd"],
                    "id_producto" => $item["id_producto"]
                ]
            );
        }

        $deleteSql = "DELETE FROM carretilla WHERE usercod = :usercod;";
        self::executeNonQuery($deleteSql, ["usercod" => $usercod]);
    }
    public static function clearCart(int $usercod)
    {
        $deleteSql = "DELETE FROM carretilla WHERE usercod = :usercod;";
        self::executeNonQuery($deleteSql, ["usercod" => $usercod]);
    }
}
