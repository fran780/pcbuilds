<?php
namespace Dao\Productos;
use Dao\Table;

class ProductosDAO extends Table {
    public static function getBestProducts() { 
    $sqlstr = "SELECT m.nombre_marca, p.nombre_producto, p.descripcion, p.precio, p.imagen FROM (SELECT *, ROW_NUMBER() OVER (PARTITION BY id_categoria ORDER BY id_producto ASC) AS fila FROM producto) AS p INNER JOIN marca_producto AS m ON p.id_marca = m.id_marca WHERE p.fila = 1 LIMIT 4";
    $params = [];
    $registros = self::obtenerRegistros($sqlstr, $params);
    return $registros;
    }

    public static function getAllProducts() { 
    $sqlstr = "SELECT m.nombre_marca, p.nombre_producto, p.descripcion, p.precio, p.imagen FROM (SELECT *, ROW_NUMBER() OVER (PARTITION BY id_categoria ORDER BY id_producto ASC) AS fila FROM producto) AS p INNER JOIN marca_producto AS m ON p.id_marca = m.id_marca";
    $params = [];
    $registros = self::obtenerRegistros($sqlstr, $params);
    return $registros;
    }
}
?>