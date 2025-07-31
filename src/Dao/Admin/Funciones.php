<?php
namespace Dao\Admin;

use Dao\Table;

class Funciones extends Table {

  public static function getFunciones(
    string $partialName = "",
    string $tipoFuncion = "",
    string $estado = "",
    string $orderBy = "",
    bool $orderDescending = false,
    int $page = 0,
    int $itemsPerPage = 10
  ) {
    $sqlstr = "SELECT f.fncod, f.fndsc, f.fnest, f.fntyp FROM funciones f ";
    $sqlstrCount = "SELECT COUNT(*) as count FROM funciones f ";

    $conditions = [];
    $params = [];

    if ($partialName !== "") {
      $conditions[] = "f.fndsc LIKE :partialName";
      $params["partialName"] = "%" . $partialName . "%";
    }
    if ($tipoFuncion !== "") {
      $conditions[] = "f.fntyp = :tipoFuncion";
      $params["tipoFuncion"] = $tipoFuncion;
    }
    if ($estado !== "") {
      $conditions[] = "f.fnest = :estado";
      $params["estado"] = $estado;
    }

    if (count($conditions) > 0) {
      $where = " WHERE " . implode(" AND ", $conditions);
      $sqlstr .= $where;
      $sqlstrCount .= $where;
    }

    $validOrderBy = ["fncod", "fndsc"];
    if ($orderBy !== "" && in_array($orderBy, $validOrderBy)) {
      $sqlstr .= " ORDER BY " . $orderBy;
      if ($orderDescending) $sqlstr .= " DESC";
    }

    $totalRecords = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
    $pagesCount = ceil($totalRecords / $itemsPerPage);
    $page = max(0, min($page, $pagesCount - 1));

    $sqlstr .= " LIMIT " . ($page * $itemsPerPage) . ", " . $itemsPerPage;
    $records = self::obtenerRegistros($sqlstr, $params);

    return [
      "funciones" => $records,
      "total" => $totalRecords,
      "page" => $page,
      "itemsPerPage" => $itemsPerPage
    ];
  }

  public static function getFuncionByCod(string $fncod) {
    $sqlstr = "SELECT fncod, fndsc, fnest, fntyp FROM funciones WHERE fncod = :fncod";
    return self::obtenerUnRegistro($sqlstr, ["fncod" => $fncod]);
  }

  public static function insertFuncion(string $fncod, string $fndsc, string $fnest, string $fntyp) {
    $sqlstr = "INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES (:fncod, :fndsc, :fnest, :fntyp)";
    $params = compact("fncod", "fndsc", "fnest", "fntyp");
    return self::executeNonQuery($sqlstr, $params);
  }

  public static function updateFuncion(string $fncod, string $fndsc, string $fnest, string $fntyp) {
    $sqlstr = "UPDATE funciones SET fndsc = :fndsc, fnest = :fnest, fntyp = :fntyp WHERE fncod = :fncod";
    $params = compact("fncod", "fndsc", "fnest", "fntyp");
    return self::executeNonQuery($sqlstr, $params);
  }

  public static function deleteFuncion(string $fncod) {
    $sqlstr = "DELETE FROM funciones WHERE fncod = :fncod";
    return self::executeNonQuery($sqlstr, ["fncod" => $fncod]);
  }

  public static function countEnUso(string $fncod): int {
    $sql = "SELECT COUNT(*) AS total FROM funciones_roles WHERE fncod = :fncod";
    $params = ["fncod" => $fncod];
    $row = self::obtenerUnRegistro($sql, $params);
    return intval($row["total"] ?? 0);
  }
}