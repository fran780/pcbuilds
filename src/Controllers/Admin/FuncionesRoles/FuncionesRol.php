<?php
namespace Controllers\Admin\FuncionesRoles;

use Controllers\PrivateController;
use Dao\Admin\FuncionesRoles as FuncionesRolesDAO;
use Dao\Admin\Funciones;
use Dao\Admin\Roles;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_FuncionesRoles_FuncionesRoles";

class FuncionesRol extends PrivateController
{
    private array $viewData;
    private array $modeNames;
    private array $estadoAsignacion = ["ACT", "INA", "BLO"];

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "rolescod" => "",
            "fncod" => "",
            "fndsc" => "",
            "fntyp" => "",
            "fnest" => "",
            "fnrolest" => "ACT",
            "fnexp" => "",
            "FormTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "errors" => []
        ];
        $this->modeNames = [
            "INS" => "Asignar nueva función",
            "UPD" => "Editar asignación para función: %s",
            "DEL" => "Eliminar asignación de función: %s",
            "DSP" => "Detalle de función asignada: %s"
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
        Renderer::render("admin/FuncionesRoles/funcionrol", $this->viewData);
    }

    private function getQueryParams(): void
    {
        if (!isset($_GET["mode"])) Site::redirectToWithMsg(LIST_URL, "Modo no especificado");

        $this->viewData["mode"] = $_GET["mode"];
        if (!in_array($this->viewData["mode"], array_keys($this->modeNames))) {
            Site::redirectToWithMsg(LIST_URL, "Modo inválido");
        }

        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["rolescod"], $_GET["fncod"])) {
                Site::redirectToWithMsg(LIST_URL, "Datos clave no proporcionados");
            }
            $this->viewData["rolescod"] = $_GET["rolescod"];
            $this->viewData["fncod"] = $_GET["fncod"];
        }
    }

    private function getDataFromDB(): void
    {
        $record = FuncionesRolesDAO::getFuncionRolByIds(
            $this->viewData["rolescod"],
            $this->viewData["fncod"]
        );

        if (!$record) {
            Site::redirectToWithMsg(LIST_URL, "Asignación no encontrada");
        }

        foreach ($record as $key => $value) {
            if (isset($this->viewData[$key])) {
                $this->viewData[$key] = $value;
            }
        }
    }

    private function getFormData(): void
    {
        $fields = ["rolescod", "fncod", "fnrolest", "fnexp", "xsrtoken"];
        foreach ($fields as $field) {
            $this->viewData[$field] = $_POST[$field] ?? "";
        }

        if ($_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            Site::redirectToWithMsg(LIST_URL, "Token inválido");
        }

        // Precargar metadata para fncod seleccionado
        $fn = Funciones::getFuncionByCod($this->viewData["fncod"]);
        $this->viewData["fndsc"] = $fn["fndsc"] ?? "";
        $this->viewData["fntyp"] = $fn["fntyp"] ?? "";
        $this->viewData["fnest"] = $fn["fnest"] ?? "";
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["rolescod"])) {
            $this->viewData["errors"]["rolescod"][] = "Rol requerido";
        }
        if (Validators::IsEmpty($this->viewData["fncod"])) {
            $this->viewData["errors"]["fncod"][] = "Función requerida";
        }
        if (!in_array($this->viewData["fnrolest"], $this->estadoAsignacion)) {
            $this->viewData["errors"]["fnrolest"][] = "Estado asignación inválido";
        }
        
      if (!empty($this->viewData["fnexp"])) {
    $fechaTexto = trim($this->viewData["fnexp"]);

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fechaTexto)) {
        $this->viewData["errors"]["fnexp"][] = "Formato inválido. Usa YYYY-MM-DD";
    } else {
        $expDate = strtotime($fechaTexto);
        $today = strtotime(date("Y-m-d"));

        if (!$expDate) {
            $this->viewData["errors"]["fnexp"][] = "Fecha inválida";
        } elseif ($expDate < $today) {
            $this->viewData["errors"]["fnexp"][] = "La fecha de expiración debe ser futura";
        }
    }

}

        return count($this->viewData["errors"]) === 0;
    }

    private function processForm(): void
    {
        $v = $this->viewData;
        switch ($v["mode"]) {
            case "INS":
                $ok = FuncionesRolesDAO::insertFuncionRol(
                    $v["rolescod"], $v["fncod"], $v["fnrolest"], $v["fnexp"]
                );
                $msg = $ok > 0 ? "Función asignada exitosamente" : "No se pudo asignar";
                break;
            case "UPD":
                $ok = FuncionesRolesDAO::updateFuncionRol(
                    $v["rolescod"], $v["fncod"], $v["fnrolest"], $v["fnexp"]
                );
                $msg = $ok > 0 ? "Asignación actualizada" : "No se pudo actualizar";
                break;
            case "DEL":
                $ok = FuncionesRolesDAO::deleteFuncionRol($v["rolescod"], $v["fncod"]);
                $msg = $ok > 0 ? "Asignación eliminada" : "No se pudo eliminar";
                break;
        }

        if ($ok) Site::redirectToWithMsg(LIST_URL, $msg);
        else $this->viewData["errors"]["global"][] = $msg;
    }

    
private function prepareViewData(): void
    {
        $v = &$this->viewData;
        $v["FormTitle"] = sprintf($this->modeNames[$v["mode"]], $v["fncod"]);

        if ($v["mode"] === "DSP") $v["showCommitBtn"] = false;
        if (in_array($v["mode"], ["DSP", "DEL"])) $v["readonly"] = "readonly";

        $v["roles_list"] = array_map(function ($rol) {
        $rol["selected"] = ($rol["rolescod"] === $this->viewData["rolescod"]) ? "selected" : "";
        return $rol;
        }, Roles::getRoles()["roles"]);

        $v["funciones_list"] = array_map(function ($fn) {
            $fn["selected"] = ($fn["fncod"] === $this->viewData["fncod"]) ? "selected" : "";
            return $fn;
        }, Funciones::getFunciones()["funciones"]);


        $v["fnrolest_ACT"] = $v["fnrolest"] === "ACT" ? "selected" : "";
        $v["fnrolest_INA"] = $v["fnrolest"] === "INA" ? "selected" : "";
        $v["fnrolest_BLO"] = $v["fnrolest"] === "BLO" ? "selected" : "";

        $v["fntyp_CTR"] = $v["fntyp"] === "CTR" ? "selected" : "";
        $v["fntyp_FNC"] = $v["fntyp"] === "FNC" ? "selected" : "";
        $v["fntyp_ASPI"] = $v["fntyp"] === "ASPI" ? "selected" : "";

        $v["timestamp"] = time();
        $v["xsrtoken"] = hash("sha256", json_encode($v));
        $_SESSION[$this->name . "-xsrtoken"] = $v["xsrtoken"];
    }
}