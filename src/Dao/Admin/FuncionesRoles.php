<?php
namespace Dao\Admin;

use Dao\Table;

class FuncionesRoles extends Table {

  public static function getFuncionesRoles(
    string $partialFnCod = "",
    string $partialFnDsc = "",
    string $rolescod = "",
    string $orderBy = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 10
  ) {
    $sqlstr = "SELECT fr.rolescod, fr.fncod, fr.fnrolest, fr.fnexp,
                      f.fndsc, f.fnest, f.fntyp, r.rolesdsc
               FROM funciones_roles fr
               LEFT JOIN funciones f ON fr.fncod = f.fncod
               LEFT JOIN roles r ON fr.rolescod = r.rolescod";

    $sqlstrCount = "SELECT COUNT(*) as count
                    FROM funciones_roles fr
                    LEFT JOIN funciones f ON fr.fncod = f.fncod
                    LEFT JOIN roles r ON fr.rolescod = r.rolescod";

    $conditions = [];
    $params = [];

    if ($partialFnCod !== "") {
      $conditions[] = "fr.fncod LIKE :partialFnCod";
      $params["partialFnCod"] = "%" . $partialFnCod . "%";
    }

    if ($partialFnDsc !== "") {
      $conditions[] = "f.fndsc LIKE :partialFnDsc";
      $params["partialFnDsc"] = "%" . $partialFnDsc . "%";
    }

    if ($rolescod !== "") {
      $conditions[] = "fr.rolescod = :rolescod";
      $params["rolescod"] = $rolescod;
    }

    if (count($conditions) > 0) {
      $whereClause = " WHERE " . implode(" AND ", $conditions);
      $sqlstr .= $whereClause;
      $sqlstrCount .= $whereClause;
    }

    $validOrderBy = ["fr.fncod", "fr.fnexp", "f.fndsc", "fr.rolescod"];
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
      "funciones_roles" => $records,
      "total" => $totalRecords,
      "page" => $page,
      "itemsPerPage" => $itemsPerPage
    ];
  }

  public static function getFuncionRolByIds(string $rolescod, string $fncod) {
    $sqlstr = "SELECT fr.rolescod, fr.fncod, fr.fnrolest, fr.fnexp,
                      f.fndsc, f.fnest, f.fntyp, r.rolesdsc
               FROM funciones_roles fr
               LEFT JOIN funciones f ON fr.fncod = f.fncod
               LEFT JOIN roles r ON fr.rolescod = r.rolescod
               WHERE fr.rolescod = :rolescod AND fr.fncod = :fncod";
    $params = ["rolescod" => $rolescod, "fncod" => $fncod];
    return self::obtenerUnRegistro($sqlstr, $params);
  }

  public static function insertFuncionRol(
  string $rolescod,
  string $fncod,
  string $fnrolest = 'ACT',
  ?string $fnexp = null
) {
  // Verificación previa para evitar duplicado
  $existing = self::getFuncionRolByIds($rolescod, $fncod);
  if ($existing) {
    // Ya existe el registro, no se hace la inserción
    return 0;
  }

  $sqlstr = "INSERT INTO funciones_roles
                (rolescod, fncod, fnrolest, fnexp)
             VALUES (:rolescod, :fncod, :fnrolest, :fnexp)";
  $params = compact('rolescod', 'fncod', 'fnrolest', 'fnexp');
  return self::executeNonQuery($sqlstr, $params);
}

  public static function updateFuncionRol(
    string $rolescod,
    string $fncod,
    string $fnrolest,
    ?string $fnexp = null
  ) {
    $sqlstr = "UPDATE funciones_roles SET
                  fnrolest = :fnrolest,
                  fnexp = :fnexp
               WHERE rolescod = :rolescod AND fncod = :fncod";
    $params = compact('rolescod', 'fncod', 'fnrolest', 'fnexp');
    return self::executeNonQuery($sqlstr, $params);
  }

  public static function deleteFuncionRol(string $rolescod, string $fncod) {
    $sqlstr = "DELETE FROM funciones_roles
               WHERE rolescod = :rolescod AND fncod = :fncod";
    $params = compact('rolescod', 'fncod');
    return self::executeNonQuery($sqlstr, $params);
  }

  
}