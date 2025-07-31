<?php
namespace Dao\Admin;

use Dao\Table;

class Categorias extends Table
{
    public static function getCategorias(
    string $partialName = "",
    string $orderBy = "",
    string $estado = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 10
) {
    $sqlstr = "SELECT 
                  c.id_categoria, 
                  c.nombre_categoria, 
                  c.estado_categoria, 
                  COUNT(p.id_producto) AS productos_count
               FROM categoria_producto c
               LEFT JOIN producto p ON c.id_categoria = p.id_categoria";

    $sqlstrCount = "SELECT COUNT(DISTINCT c.id_categoria) as count
                    FROM categoria_producto c
                    LEFT JOIN producto p ON c.id_categoria = p.id_categoria";

    $conditions = [];
    $params = [];

    if ($partialName !== "") {
        $conditions[] = "c.nombre_categoria LIKE :partialName";
        $params["partialName"] = "%" . $partialName . "%";
    }
    if ($estado !== "") {
        $conditions[] = "c.estado_categoria = :estado";
        $params["estado"] = $estado;
    }

    if (count($conditions) > 0) {
        $where = " WHERE " . implode(" AND ", $conditions);
        $sqlstr .= $where;
        $sqlstrCount .= $where;
    }

    $sqlstr .= " GROUP BY c.id_categoria";

    $validOrderBy = ["id_categoria", "nombre_categoria"];
    if ($orderBy !== "" && in_array($orderBy, $validOrderBy)) {
        $sqlstr .= " ORDER BY c." . $orderBy;
        if ($orderDescending) {
            $sqlstr .= " DESC";
        }
    }

    $totalRecords = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
    $pagesCount = ceil($totalRecords / $itemsPerPage);
    if ($page > $pagesCount - 1) {
        $page = max(0, $pagesCount - 1);
    }

    $sqlstr .= " LIMIT " . ($page * $itemsPerPage) . ", " . $itemsPerPage;

    $records = self::obtenerRegistros($sqlstr, $params);
    return [
        "categorias" => $records,
        "total" => $totalRecords,
        "page" => $page,
        "itemsPerPage" => $itemsPerPage
    ];
}

   public static function getCategoriaById(int $idCategoria): array
{
    $sql = "SELECT id_categoria, nombre_categoria, estado_categoria 
            FROM categoria_producto 
            WHERE id_categoria = :idCategoria;";
    return self::obtenerUnRegistro($sql, ["idCategoria" => $idCategoria]);
}

    public static function insertCategoria(string $nombreCategoria, string $estadoCategoria): int
{
    $sql = "INSERT INTO categoria_producto (nombre_categoria, estado_categoria)
            VALUES (:nombreCategoria, :estadoCategoria);";
    
    return self::executeNonQuery($sql, [
        "nombreCategoria" => $nombreCategoria,
        "estadoCategoria" => $estadoCategoria
    ]);
}

    public static function updateCategoria(int $idCategoria, string $nombreCategoria, string $estadoCategoria): int
    {
        $sql = "UPDATE categoria_producto 
                SET nombre_categoria = :nombreCategoria, estado_categoria = :estadoCategoria 
                WHERE id_categoria = :idCategoria;";
        
        $params = [
            "idCategoria" => $idCategoria,
            "nombreCategoria" => $nombreCategoria,
            "estadoCategoria" => $estadoCategoria
        ];

        return self::executeNonQuery($sql, $params);
    }

    public static function deleteCategoria (int $idCategoria): int
    {
        $sql = "DELETE FROM categoria_producto WHERE id_categoria = :idCategoria;";
        return self::executeNonQuery($sql, ["idCategoria" => $idCategoria]);
    }

    public static function existePorDescripcion(string $nombre_categoria): bool
{
    $sql = "SELECT 1 FROM categoria_producto WHERE TRIM(LOWER(nombre_categoria)) = TRIM(LOWER(:nombre_categoria)) LIMIT 1";
    $result = self::obtenerUnRegistro($sql, ["nombre_categoria" => $nombre_categoria]);
    return isset($result) && !empty($result);
}
}