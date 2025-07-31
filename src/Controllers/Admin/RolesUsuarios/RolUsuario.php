<?php
namespace Controllers\Admin\RolesUsuarios;

use Controllers\PrivateController;
use Dao\Admin\RolesUsuarios as RolesUsuariosDAO;
use Dao\Admin\Usuarios;
use Dao\Admin\Roles;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_RolesUsuarios_RolesUsuario";

class RolUsuario extends PrivateController
{
    private array $viewData;
    private array $modeNames;
    private array $estadoAsignacion = ["ACT", "INA"];

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "rolescod" => "",
            "usercod" => "",
            "username" => "",
            "roleuserest" => "ACT",
            "roleuserfch" => date("Y-m-d"),
            "roleuserexp" => date("Y-m-d", strtotime("+1 year")),
            "FormTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "errors" => []
        ];
        $this->modeNames = [
            "INS" => "Asignar nuevo rol",
            "UPD" => "Editar asignación: %s",
            "DEL" => "Eliminar asignación: %s",
            "DSP" => "Detalle de asignación: %s"
        ];
    }

    public function run(): void
    {
        $this->getQueryParams();

        if ($this->viewData["mode"] !== "INS") {
            $this->getDataFromDB();
        }

        if ($this->isPostBack()) {
            $this->getFormData();
            if ($this->viewData["mode"] === "DEL" || $this->validateData()) {
                $this->processForm();
            }
        }

        $this->prepareViewData();
        Renderer::render("Admin/RolesUsuarios/rolusuario", $this->viewData);
    }

    private function getQueryParams(): void
    {
        if (!isset($_GET["mode"])) Site::redirectToWithMsg(LIST_URL, "Modo no especificado");

        $this->viewData["mode"] = $_GET["mode"];
        if (!in_array($this->viewData["mode"], array_keys($this->modeNames))) {
            Site::redirectToWithMsg(LIST_URL, "Modo inválido");
        }

        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["rolescod"], $_GET["usercod"])) {
                Site::redirectToWithMsg(LIST_URL, "Datos clave no proporcionados");
            }
            $this->viewData["rolescod"] = $_GET["rolescod"];
            $this->viewData["usercod"] = $_GET["usercod"];
        }
    }

    private function getDataFromDB(): void
    {
        $record = RolesUsuariosDAO::getRolUsuarioByIds(
            $this->viewData["usercod"],
            $this->viewData["rolescod"]
        );

        if (!$record) {
            Site::redirectToWithMsg(LIST_URL, "Asignación no encontrada");
        }

        foreach ($record as $key => $value) {
            if (isset($this->viewData[$key])) {
                $this->viewData[$key] = $value;
            }
        }

        // Precargar nombre del usuario
        $usuario = Usuarios::getUsuarioById($this->viewData["usercod"]);
        $this->viewData["username"] = $usuario["username"] ?? "";
    }

    private function getFormData(): void
    {
        $fields = ["usercod", "rolescod", "roleuserest", "roleuserfch", "roleuserexp", "xsrtoken"];
        foreach ($fields as $field) {
    if (isset($_POST[$field]) && $_POST[$field] !== "") {
        $this->viewData[$field] = $_POST[$field];
    }
}

        if ($_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            Site::redirectToWithMsg(LIST_URL, "Token inválido");
        }

       if (!empty($this->viewData["usercod"]) && is_numeric($this->viewData["usercod"])) 
        {
            $user = Usuarios::getUsuarioById((int)$this->viewData["usercod"]);
            $this->viewData["username"] = $user["username"] ?? "";
        }
       
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["usercod"])) {
            $this->viewData["errors"]["usercod"][] = "Usuario requerido";
        }
        if (Validators::IsEmpty($this->viewData["rolescod"])) {
            $this->viewData["errors"]["rolescod"][] = "Rol requerido";
        }
        if (!in_array($this->viewData["roleuserest"], $this->estadoAsignacion)) {
            $this->viewData["errors"]["roleuserest"][] = "Estado inválido";
        }

        $fch = strtotime($this->viewData["roleuserfch"]);
        $exp = strtotime($this->viewData["roleuserexp"]);
        if (!$fch || !$exp) {
            $this->viewData["errors"]["roleuserfch"][] = "Fechas inválidas";
        } elseif ($exp < $fch) {
            $this->viewData["errors"]["roleuserexp"][] = "La expiración debe ser posterior";
        }

        return count($this->viewData["errors"]) === 0;
    }

    private function processForm(): void
    {
        $v = $this->viewData;
        switch ($v["mode"]) {
            case "INS":
                $ok = RolesUsuariosDAO::insertRolUsuario(
                    $v["usercod"], $v["rolescod"], $v["roleuserest"], $v["roleuserfch"], $v["roleuserexp"]
                );
                $msg = $ok > 0 ? "Rol asignado correctamente" : "No se pudo asignar";
                break;
            case "UPD":
                $ok = RolesUsuariosDAO::updateRolUsuario(
                    $v["usercod"], $v["rolescod"], $v["roleuserest"], $v["roleuserfch"], $v["roleuserexp"]
                );
                $msg = $ok > 0 ? "Asignación actualizada" : "No se pudo actualizar";
                break;
            case "DEL":
                $ok = RolesUsuariosDAO::deleteRolUsuario($v["usercod"], $v["rolescod"]);
                $msg = $ok > 0 ? "Asignación eliminada" : "No se pudo eliminar";
                break;
        }

        if ($ok) Site::redirectToWithMsg(LIST_URL, $msg);
        else $this->viewData["errors"]["global"][] = $msg;
    }

    private function prepareViewData(): void
    {
        $v = &$this->viewData;

        $v["FormTitle"] = sprintf($this->modeNames[$v["mode"]], "{$v["usercod"]} - {$v["rolescod"]}");

        if ($v["mode"] === "DSP") $v["showCommitBtn"] = false;
        if (in_array($v["mode"], ["DSP", "DEL"])) $v["readonly"] = "readonly";

        $this->viewData["disabled"] = ($this->viewData["mode"] === "UPD") ? "disabled" : "";

     // Cargar lista de usuarios
       $v["usuarios_list"] = array_map(function ($u) {
        $u["selected"] = ($u["usercod"] === $this->viewData["usercod"]) ? "selected" : "";
        return $u;
        }, \Dao\Admin\Usuarios::getAll());

        $v["roles_list"] = array_map(function ($r) {
            $r["selected"] = ($r["rolescod"] === $this->viewData["rolescod"]) ? "selected" : "";
            return $r;
        }, Roles::getRoles()["roles"]);

        $v["roleuserest_ACT"] = $v["roleuserest"] === "ACT" ? "selected" : "";
        $v["roleuserest_INA"] = $v["roleuserest"] === "INA" ? "selected" : "";

        $v["timestamp"] = time();
        $v["xsrtoken"] = hash("sha256", json_encode($v));
        $_SESSION[$this->name . "-xsrtoken"] = $v["xsrtoken"];
    }
}