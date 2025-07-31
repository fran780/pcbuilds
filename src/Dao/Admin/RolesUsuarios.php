<?php
namespace Dao\Admin;

use Dao\Table;

class RolesUsuarios extends Table {
  public static function getRolesUsuarios(
    string $partialUserCod = "",
    string $partialRoleCod = "",
    string $estado = "",
    string $orderBy = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 10
  ) {
    $sqlstr = "SELECT ru.usercod, ru.rolescod, ru.roleuserest, ru.roleuserfch, ru.roleuserexp
               FROM roles_usuarios ru
               LEFT JOIN roles r ON ru.rolescod = r.rolescod
               LEFT JOIN usuario u ON ru.usercod = u.usercod";

    $sqlstrCount = "SELECT COUNT(*) as count
                    FROM roles_usuarios ru
                    LEFT JOIN roles r ON ru.rolescod = r.rolescod
                    LEFT JOIN usuario u ON ru.usercod = u.usercod";

    $conditions = [];
    $params = [];

    if ($partialUserCod !== "") {
      $conditions[] = "ru.usercod LIKE :partialUserCod";
      $params["partialUserCod"] = "%" . $partialUserCod . "%";
    }

    if ($partialRoleCod !== "") {
      $conditions[] = "ru.rolescod LIKE :partialRoleCod";
      $params["partialRoleCod"] = "%" . $partialRoleCod . "%";
    }

    if ($estado !== "") {
      $conditions[] = "ru.roleuserest = :estado";
      $params["estado"] = $estado;
    }

    if (count($conditions) > 0) {
      $where = " WHERE " . implode(" AND ", $conditions);
      $sqlstr .= $where;
      $sqlstrCount .= $where;
    }

    $validOrderBy = ["ru.usercod", "ru.rolescod"];
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
      "roles_usuarios" => $records,
      "total" => $totalRecords,
      "page" => $page,
      "itemsPerPage" => $itemsPerPage
    ];
  }

  public static function getRolUsuarioByIds(string $usercod, string $rolescod) {
  $sqlstr = "SELECT ru.usercod, ru.rolescod, ru.roleuserest, ru.roleuserfch, ru.roleuserexp
             FROM roles_usuarios ru
             WHERE ru.usercod = :usercod AND ru.rolescod = :rolescod";
  $params = compact("usercod", "rolescod");
  return self::obtenerUnRegistro($sqlstr, $params);
}
  public static function insertRolUsuario(
  string $usercod,
  string $rolescod,
  string $roleuserest = 'ACT',
  ?string $roleuserfch = null,
  ?string $roleuserexp = null
) {
  if (self::getRolUsuarioByIds($usercod, $rolescod)) return 0;

  $sqlstr = "INSERT INTO roles_usuarios
              (usercod, rolescod, roleuserest, roleuserfch, roleuserexp)
             VALUES
              (:usercod, :rolescod, :roleuserest, :roleuserfch, :roleuserexp)";
  $params = compact("usercod", "rolescod", "roleuserest", "roleuserfch", "roleuserexp");
  return self::executeNonQuery($sqlstr, $params);
}

  public static function updateRolUsuario(
  string $usercod,
  string $rolescod,
  string $roleuserest,
  ?string $roleuserfch = null,
  ?string $roleuserexp = null
) {
  $sqlstr = "UPDATE roles_usuarios SET
               roleuserest = :roleuserest,
               roleuserfch = :roleuserfch,
               roleuserexp = :roleuserexp
             WHERE usercod = :usercod AND rolescod = :rolescod";
  $params = compact("usercod", "rolescod", "roleuserest", "roleuserfch", "roleuserexp");
  return self::executeNonQuery($sqlstr, $params);
}

  public static function deleteRolUsuario(string $usercod, string $rolescod) {
    $sqlstr = "DELETE FROM roles_usuarios
               WHERE usercod = :usercod AND rolescod = :rolescod";
    $params = compact("usercod", "rolescod");
    return self::executeNonQuery($sqlstr, $params);
  }

    // Devuelve lista de todos los roles disponibles
  public static function getRoles() {
    $sqlstr = "SELECT rolescod, rolesdsc FROM roles WHERE rolesest != 'INA'";
    return self::obtenerRegistros($sqlstr, []);
  }

  // Devuelve lista de todos los usuarios disponibles
  public static function getUsuarios() {
    $sqlstr = "SELECT usercod, username FROM usuario WHERE userest != 'INA'";
    return self::obtenerRegistros($sqlstr, []);
}
}