<?php
namespace Dao\Admin;

use Dao\Table;

class Usuarios extends Table
{
    public static function getUsuarios(
        string $partialEmail = "",
        string $usertipo = "",
        string $userest = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT usercod, useremail, username, userfching, userest, usertipo FROM usuario";
        $sqlstrCount = "SELECT COUNT(*) as count FROM usuario";

        $conditions = [];
        $params = [];

        if ($partialEmail !== "") {
            $conditions[] = "useremail LIKE :partialEmail";
            $params["partialEmail"] = "%" . $partialEmail . "%";
        }
        if ($usertipo !== "") {
            $conditions[] = "usertipo = :usertipo";
            $params["usertipo"] = $usertipo;
        }
        if ($userest !== "") {
            $conditions[] = "userest = :userest";
            $params["userest"] = $userest;
        }

        if (count($conditions) > 0) {
            $where = " WHERE " . implode(" AND ", $conditions);
            $sqlstr .= $where;
            $sqlstrCount .= $where;
        }

        $validOrderBy = ["usercod", "useremail", "username", "userfching"];
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
            "users" => $records,
            "total" => $totalRecords,
            "page" => $page,
            "itemsPerPage" => $itemsPerPage
        ];
    }

    public static function getUsuarioById(int $usercod)
    {
        $sqlstr = "SELECT usercod, useremail, username, userfching, userest, usertipo FROM usuario WHERE usercod = :usercod";
        return self::obtenerUnRegistro($sqlstr, ["usercod" => $usercod]);
    }

    public static function insertUser(
        string $useremail,
        string $username,
        string $userpswd,
        string $userest,
        string $usertipo
    ) {
        $sqlstr = "INSERT INTO usuario 
          (useremail, username, userpswd, userfching, userest, usertipo)
          VALUES 
          (:useremail, :username, :userpswd, NOW(), :userest, :usertipo)";
        return self::executeNonQuery($sqlstr, [
            "useremail" => $useremail,
            "username" => $username,
            "userpswd" => $userpswd,
            "userest" => $userest,
            "usertipo" => $usertipo
        ]);
    }

   public static function updateUsuario(
    int $usercod,
    string $useremail,
    string $username,
    ?string $userpswd,
    string $userest,
    string $usertipo
) {
    $sqlstr = "UPDATE usuario SET
        useremail = :useremail,
        username = :username,
        userest = :userest,
        usertipo = :usertipo";

    $params = [
        "usercod" => $usercod,
        "useremail" => $useremail,
        "username" => $username,
        "userest" => $userest,
        "usertipo" => $usertipo
    ];

    if (!is_null($userpswd)) {
        $sqlstr .= ", userpswd = :userpswd";
        $params["userpswd"] = $userpswd;
    }

    $sqlstr .= " WHERE usercod = :usercod";

    return self::executeNonQuery($sqlstr, $params);
}

    public static function deleteUsuario(int $usercod)
    {
        $sqlstr = "DELETE FROM usuario WHERE usercod = :usercod";
        return self::executeNonQuery($sqlstr, ["usercod" => $usercod]);
    }

    public static function getTiposUsuario()
    {
        return [
            ["code" => "ADM", "description" => "Administrador"],
            ["code" => "CNS", "description" => "Consultor"],
            ["code" => "CLT", "description" => "Cliente"]
        ];
    }

    public static function getEstados()
    {
        return [
            ["code" => "ACT", "description" => "Activo"],
            ["code" => "INA", "description" => "Inactivo"],
            ["code" => "BLQ", "description" => "Bloqueado"]
        ];
    }

    public static function getAll(): array {
        $sqlstr = "SELECT usercod, username FROM usuario ORDER BY username";
        return self::obtenerRegistros($sqlstr, []);
    }
}