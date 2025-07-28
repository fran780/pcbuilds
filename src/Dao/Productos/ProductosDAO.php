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

    public static function getAllBrands() {
        $sqlstr = "SELECT id_marca, nombre_marca FROM marca_producto ORDER BY nombre_marca ASC";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getAllCategories() {
        $sqlstr = "SELECT id_categoria, nombre_categoria FROM categoria_producto ORDER BY nombre_categoria ASC";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getAllProductsPaginated(
    string $partialName = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 8,
    string $orderBy = "",
    array $id_marca = [],
    array $id_categoria = [] 
    )
    {
        if ($itemsPerPage < 1) {
            $itemsPerPage = 8;
        }

        $sqlstr = "SELECT m.nombre_marca, p.id_producto, p.nombre_producto, p.descripcion, p.precio, p.imagen, p.stock
            FROM producto p
            INNER JOIN marca_producto m ON p.id_marca = m.id_marca
            INNER JOIN estado_producto e ON p.id_estado = e.id_estado
            WHERE e.estado = 'ACT'";

        $sqlstrCount = "SELECT COUNT(*) as count
            FROM producto p
            INNER JOIN marca_producto m ON p.id_marca = m.id_marca
            INNER JOIN estado_producto e ON p.id_estado = e.id_estado
            WHERE e.estado = 'ACT'";

        $conditions = [];
        $params = [];

        if ($partialName != "") {
            $conditions[] = "p.nombre_producto LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if (!empty($id_marca)) {
            $placeholders = [];
            foreach ($id_marca as $index => $marcaId) {
                $key = "id_marca_" . $index;
                $placeholders[] = ":" . $key;
                $params[$key] = $marcaId;
            }
            $conditions[] = "p.id_marca IN (" . implode(",", $placeholders) . ")";
        }

        if (!empty($id_categoria)) {
            $placeholders = [];
            foreach ($id_categoria as $index => $catId) {
                $key = "id_categoria_" . $index;
                $placeholders[] = ":" . $key;
                $params[$key] = $catId;
            }
            $conditions[] = "p.id_categoria IN (" . implode(",", $placeholders) . ")";
        }

        if (count($conditions) > 0) {
            $sqlstr .= " AND " . implode(" AND ", $conditions);
            $sqlstrCount .= " AND " . implode(" AND ", $conditions);
        }

        if (!in_array($orderBy, ["nombre_producto", "precio", ""])) {
            throw new \Exception("Error Processing Request: OrderBy has invalid value");
        }

        if ($orderBy != "") {
            $sqlstr .= " ORDER BY " . $orderBy;
            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        }

        $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
        $pagesCount = ceil($numeroDeRegistros / $itemsPerPage);
        if ($page > $pagesCount - 1) {
            $page = max(0, $pagesCount - 1);
        }

        $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);
        return [
            "products" => $registros,
            "total" => $numeroDeRegistros,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];

        //Se pone la logica para poder añadir un producto paginado al carrito

        
    }

    public static function getProductoById($id)
    {
        $sql = "SELECT * FROM producto WHERE id_producto = :id_producto";
        return self::obtenerUnRegistro($sql, ["id_producto" => $id]);
    }

}
?>