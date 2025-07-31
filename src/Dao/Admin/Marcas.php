<?php
namespace Dao\Admin;

use Dao\Table;

class Marcas extends Table
{
    public static function getMarcas(
        string $partialName = "",
        string $orderBy = "",
        string $estado = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT 
                      m.id_marca, 
                      m.nombre_marca, 
                      m.estado_marca, 
                      COUNT(p.id_producto) AS productos_count
                   FROM marca_producto m
                   LEFT JOIN producto p ON m.id_marca = p.id_marca";

        $sqlstrCount = "SELECT COUNT(DISTINCT m.id_marca) as count
                        FROM marca_producto m
                        LEFT JOIN producto p ON m.id_marca = p.id_marca";

        $conditions = [];
        $params = [];

        if ($partialName !== "") {
            $conditions[] = "m.nombre_marca LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }

        if ($estado !== "") {
            $conditions[] = "m.estado_marca = :estado";
            $params["estado"] = $estado;
        }

        if (count($conditions) > 0) {
            $where = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $where;
            $sqlstrCount .= $where;
        }

        $sqlstr .= " GROUP BY m.id_marca";

        $validOrderBy = ["id_marca", "nombre_marca"];
        if ($orderBy !== "" && in_array($orderBy, $validOrderBy)) {
            $sqlstr .= " ORDER BY m." . $orderBy;
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
            "marcas" => $records,
            "total" => $totalRecords,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getMarcaById(int $idMarca): array
    {
        $sql = "SELECT id_marca, nombre_marca, estado_marca 
                FROM marca_producto 
                WHERE id_marca = :idMarca;";
        return self::obtenerUnRegistro($sql, ["idMarca" => $idMarca]);
    }

    public static function insertMarca(string $nombreMarca, string $estadoMarca): int
    {
        $sql = "INSERT INTO marca_producto (nombre_marca, estado_marca)
                VALUES (:nombreMarca, :estadoMarca);";
        return self::executeNonQuery($sql, [
            "nombreMarca" => $nombreMarca,
            "estadoMarca" => $estadoMarca
        ]);
    }

    public static function updateMarca(int $idMarca, string $nombreMarca, string $estadoMarca): int
    {
        $sql = "UPDATE marca_producto 
                SET nombre_marca = :nombreMarca, estado_marca = :estadoMarca 
                WHERE id_marca = :idMarca;";
        $params = [
            "idMarca" => $idMarca,
            "nombreMarca" => $nombreMarca,
            "estadoMarca" => $estadoMarca
        ];
        return self::executeNonQuery($sql, $params);
    }

    public static function deleteMarca(int $idMarca): int
    {
        $sql = "DELETE FROM marca_producto WHERE id_marca = :idMarca;";
        return self::executeNonQuery($sql, ["idMarca" => $idMarca]);
    }

    public static function existePorDescripcion(string $nombre_marca): bool
{
    $sql = "SELECT 1 FROM marca_producto WHERE TRIM(LOWER(nombre_marca)) = TRIM(LOWER(:nombre_marca)) LIMIT 1";
    return self::obtenerUnRegistro($sql, ["nombre_marca" => $nombre_marca]) !== [];
}
}