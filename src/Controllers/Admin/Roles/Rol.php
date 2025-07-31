<?php
namespace Controllers\Admin\Roles;

use Controllers\PrivateController;
use Dao\Admin\Roles as RolesDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_Roles_Roles";

class Rol extends PrivateController
{
    private array $viewData = [];
    private array $modes = [
        "INS" => "Nuevo Rol",
        "UPD" => "Editar Rol: %s",
        "DEL" => "Eliminar Rol: %s",
        "DSP" => "Detalle del Rol: %s"
    ];

    private array $estados = ["ACT", "INA"];

    public function run(): void
    {
        $this->getQueryParams();
        if ($this->viewData["mode"] !== "INS") {
            $this->loadRol();
        }
        if ($this->isPostBack()) {
            $this->getFormData();
            if ($this->viewData["mode"] === "DEL") {
                $this->process(); // sin validar
            } elseif ($this->validate()) {
                $this->process();
            }
        }
        $this->prepareView();
        Renderer::render("admin/roles/rol", $this->viewData);
    }

    private function throwError(string $message, string $log = "")
    {
        if ($log !== "") error_log("Rol.php - " . $log);
        Site::redirectToWithMsg(LIST_URL, $message);
    }

   private function innerError(string $scope, string $msg)
{
    if (!isset($this->viewData["errors"][$scope])) {
        $this->viewData["errors"][$scope] = [];
    }

    if (!in_array($msg, $this->viewData["errors"][$scope])) {
        $this->viewData["errors"][$scope][] = $msg;
    }
}

    private function getQueryParams()
    {
        if (!isset($_GET["mode"])) {
            $this->throwError("Modo no especificado", "Missing mode param");
        }

        $mode = $_GET["mode"];
        if (!isset($this->modes[$mode])) {
            $this->throwError("Modo inválido", "Unknown mode '$mode'");
        }

        $this->viewData["mode"] = $mode;

        if ($mode !== "INS") {
            if (!isset($_GET["rolescod"])) {
                $this->throwError("Código de rol requerido", "Missing rolescod");
            }
            $this->viewData["rolescod"] = $_GET["rolescod"];
        }
    }

    private function loadRol()
    {
        $rol = RolesDAO::getRolByCod($this->viewData["rolescod"]);
        if (!$rol) {
            $this->throwError("Rol no encontrado", "Rol '{$this->viewData["rolescod"]}' no existe");
        }
        $this->viewData = array_merge($this->viewData, $rol);
    }

    private function getFormData()
    {
        $fields = ["rolescod", "rolesdsc", "rolesest", "xsrtoken"];
        foreach ($fields as $f) {
            if ($this->viewData["mode"] === "INS") {
                if (!isset($_POST["rolescod"]) || Validators::IsEmpty($_POST["rolescod"])) {
                    $this->innerError("rolescod", "Código requerido");
                }
                $this->viewData["rolescod"] = $_POST["rolescod"] ?? "";
            } else {
                // En UPD/DEL/DSP el código ya viene de la query y no debe cambiar
                $this->viewData["rolescod"] = $_GET["rolescod"];
            }
        }

        if ($_POST["xsrtoken"] !== ($_SESSION[$this->name . "-xsrtoken"] ?? "")) {
            $this->throwError("Token inválido", "XSR mismatch");
        }

        if ($this->viewData["mode"] !== "INS" &&
            $_POST["rolescod"] !== $this->viewData["rolescod"]
        ) {
            $this->throwError("Código de rol no coincide");
        }

        $this->viewData["rolescod"] = $_POST["rolescod"];
        $this->viewData["rolesdsc"] = $_POST["rolesdsc"];
        $this->viewData["rolesest"] = $_POST["rolesest"];
    }

    private function validate(): bool
    {
        if (Validators::IsEmpty($this->viewData["rolescod"])) {
            $this->innerError("rolescod", "Código requerido");
        }
        if (Validators::IsEmpty($this->viewData["rolesdsc"])) {
            $this->innerError("rolesdsc", "Descripción requerida");
        }
        if (!in_array($this->viewData["rolesest"], $this->estados)) {
            $this->innerError("rolesest", "Estado inválido");
        }
        if ($this->viewData["mode"] === "INS") {
                    $codigo = $this->viewData["rolescod"];
                    if (!Validators::IsEmpty($codigo) && RolesDAO::getRolByCod($codigo)) {
                        $this->innerError("rolescod", "Ya existe este rol");
                    }
                }
        return empty($this->viewData["errors"]);
    }

    private function process()
    {
        $v = $this->viewData;
        switch ($v["mode"]) {
            case "INS":
                if (RolesDAO::insertRol($v["rolescod"], $v["rolesdsc"], $v["rolesest"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Rol creado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo crear el rol");
                }
                break;
            case "UPD":
                if (RolesDAO::updateRol($v["rolescod"], $v["rolesdsc"], $v["rolesest"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Rol actualizado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo actualizar el rol");
                }
                break;
            case "DEL":
                if (RolesDAO::deleteRol($v["rolescod"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Rol eliminado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo eliminar el rol");
                }
                break;
        }
    }

    private function prepareView()
    {
        $v = &$this->viewData;
        $v["FormTitle"] = sprintf($this->modes[$v["mode"]], $v["rolescod"] ?? "");

        if (!empty($v["errors"])) {
            foreach ($v["errors"] as $field => $errs) {
                $v[$field . "_error"] = implode(", ", $errs);
            }
        }


    $v["readonly"] = ($v["mode"] === "INS") ? "" : "readonly disabled";
        $v["readonly"] = ($v["mode"] === "DSP" || $v["mode"] === "DEL") ? "readonly" : "";
        
        $v["showCommitBtn"] = ($v["mode"] !== "DSP");
        $v["readonly_fncod"] = ($v["mode"] === "UPD" || $v["mode"] === "DSP" || $v["mode"] === "DEL") ? "readonly" : "";        


        // Pre-seleccionar estado
       $v["rolStatus_act"] = (isset($v["rolesest"]) && $v["rolesest"] === "ACT") ? "selected" : "";
        $v["rolStatus_ina"] = (isset($v["rolesest"]) && $v["rolesest"] === "INA") ? "selected" : "";
       
        if ($v["mode"] === "DEL") {
            $vinculosCount = \Dao\Admin\Roles::countVinculosRol($v["rolescod"]);
            $v["vinculos_count"] = $vinculosCount;
            $v["disableConfirmBtn"] = ($vinculosCount > 0);
            $v["showWarningMsg"] = ($vinculosCount > 0);
            $v["showCommitBtn"] = ($vinculosCount == 0);
        }
    
        $v["xsrtoken"] = hash("sha256", json_encode($v));
        $_SESSION[$this->name . "-xsrtoken"] = $v["xsrtoken"];
    }
}