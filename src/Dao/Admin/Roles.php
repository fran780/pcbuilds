<?php
namespace Dao\Admin;

use Dao\Table;

class Roles extends Table {

    public static function getRoles(
        string $partialName = "",
        string $estado = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT rolescod, rolesdsc, rolesest FROM roles";
        $sqlstrCount = "SELECT COUNT(*) as count FROM roles";

        $conditions = [];
        $params = [];

        if ($partialName !== "") {
            $conditions[] = "rolesdsc LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }
        if ($estado !== "") {
            $conditions[] = "rolesest = :estado";
            $params["estado"] = $estado;
        }

        if (count($conditions) > 0) {
            $where = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $where;
            $sqlstrCount .= $where;
        }

        $validOrderBy = ["rolescod", "rolesdsc", "rolesest"];
        if ($orderBy !== "" && in_array($orderBy, $validOrderBy)) {
            $sqlstr .= " ORDER BY " . $orderBy;
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
            "roles" => $records,
            "total" => $totalRecords,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getRolByCod(string $rolescod) {
        $sqlstr = "SELECT rolescod, rolesdsc, rolesest FROM roles WHERE rolescod = :rolescod";
        return self::obtenerUnRegistro($sqlstr, ["rolescod" => $rolescod]);
    }

    public static function insertRol(string $rolescod, string $rolesdsc, string $rolesest) {
        $sqlstr = "INSERT INTO roles (rolescod, rolesdsc, rolesest)
                   VALUES (:rolescod, :rolesdsc, :rolesest)";
        $params = compact("rolescod", "rolesdsc", "rolesest");
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateRol(string $rolescod, string $rolesdsc, string $rolesest) {
    $sqlstr = "UPDATE roles SET rolesdsc = :rolesdsc, rolesest = :rolesest WHERE rolescod = :rolescod";
    $params = compact("rolescod", "rolesdsc", "rolesest");

    $filas = self::executeNonQuery($sqlstr, $params);
    error_log("Update Rol - cod={$rolescod} - filas afectadas: {$filas}");
    return $filas;
}

    public static function deleteRol(string $rolescod) {
        $sqlstr = "DELETE FROM roles WHERE rolescod = :rolescod";
        return self::executeNonQuery($sqlstr, ["rolescod" => $rolescod]);
    }
//PARA QUE NO SE ELIMINE UN ROL QUE ESTÁ EN USO de alguna función
public static function countVinculosRol(string $rolescod){
    $sql = "
        SELECT (
            SELECT COUNT(*) FROM roles_usuarios WHERE rolescod = :rolescod
        ) +
        (
            SELECT COUNT(*) FROM funciones_roles WHERE rolescod = :rolescod
        ) AS total;
    ";
    $params = ["rolescod" => $rolescod];
    $row = self::obtenerUnRegistro($sql, $params);
    return intval($row["total"] ?? 0);
}
public static function getAll(): array {
  $sqlstr = "SELECT rolescod, rolesdsc AS roledescription FROM roles ORDER BY rolesdsc";
  return self::obtenerRegistros($sqlstr, []);
}
}