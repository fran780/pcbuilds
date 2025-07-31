<?php
namespace Controllers\Admin\Funciones;

use Controllers\PrivateController;
use Dao\Admin\Funciones as FuncionesDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_Funciones_Funciones";

class Funcion extends PrivateController
{
    private array $viewData = [];
    private array $modes = [
        "INS" => "Nuevo Permiso",
        "UPD" => "Editar Permiso: %s",
        "DEL" => "Eliminar Permiso: %s",
        "DSP" => "Detalle del Permiso: %s"
    ];

    private array $estados = ["ACT", "INA"];
    private array $tipos = ["CTR", "FNC", "MNU"];

    public function run(): void
    {
        $this->getQueryParams();
        if ($this->viewData["mode"] !== "INS") {
            $this->loadFuncion();
        }
        if ($this->isPostBack()) {
            $this->getFormData();
            if ($this->viewData["mode"] === "DEL") {
                $this->process(); 
            } elseif ($this->validate()) {
                $this->process();
            }
        }
        $this->prepareView();
        Renderer::render("admin/funciones/funcion", $this->viewData);
    }

    private function throwError(string $message, string $log = "")
    {
        if ($log !== "") error_log("Funcion.php - " . $log);
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
            if (!isset($_GET["fncod"])) {
                $this->throwError("Código de permiso requerido", "Missing fncod");
            }
            $this->viewData["fncod"] = $_GET["fncod"];
        }
    }

    private function loadFuncion()
    {
        $funcion = FuncionesDAO::getFuncionByCod($this->viewData["fncod"]);
        if (!$funcion) {
            $this->throwError("Permiso no encontrado", "Permiso '{$this->viewData["fncod"]}' no existe");
        }
        $this->viewData = array_merge($this->viewData, $funcion);
    }

    private function getFormData()
    {
        if ($this->viewData["mode"] === "INS") {
            if (!isset($_POST["fncod"]) || Validators::IsEmpty($_POST["fncod"])) {
                $this->innerError("fncod", "Código requerido");
            }
            $this->viewData["fncod"] = $_POST["fncod"] ?? "";
        } else {
            $this->viewData["fncod"] = $_GET["fncod"];
        }

        if ($_POST["xsrtoken"] !== ($_SESSION[$this->name . "-xsrtoken"] ?? "")) {
            $this->throwError("Token inválido", "XSR mismatch");
        }

        if ($this->viewData["mode"] !== "INS" &&
            $_POST["fncod"] !== $this->viewData["fncod"]
        ) {
            $this->throwError("Código de permiso no coincide");
        }

       $this->viewData["fndsc"] = $_POST["fndsc"] ?? "";
        $this->viewData["fnest"] = $_POST["fnest"] ?? "";
        $this->viewData["fntyp"] = $_POST["fntyp"] ?? "";
    }

    private function validate(): bool
    {
        if (Validators::IsEmpty($this->viewData["fncod"])) {
            $this->innerError("fncod", "Permiso requerido");
        }
        if (Validators::IsEmpty($this->viewData["fndsc"])) {
            $this->innerError("fndsc", "Descripción requerida");
        }
        if (!in_array($this->viewData["fnest"], $this->estados)) {
            $this->innerError("fnest", "Estado inválido");
        }
        if (!in_array($this->viewData["fntyp"], $this->tipos)) {
            $this->innerError("fntyp", "Tipo inválido");
        }
            // Validación de duplicado en modo inserción
        if ($this->viewData["mode"] === "INS") {
            $codigo = $this->viewData["fncod"];
            if (!Validators::IsEmpty($codigo) && FuncionesDAO::getFuncionByCod($codigo)) {
                $this->innerError("fncod", "Ya existe este permiso");
            }
        }

        return empty($this->viewData["errors"]);
    }

    private function process()
    {
        $v = $this->viewData;
        switch ($v["mode"]) {
            case "INS":
                if (FuncionesDAO::insertFuncion($v["fncod"], $v["fndsc"], $v["fnest"], $v["fntyp"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Permiso creado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo crear el permiso");
                }
                break;
            case "UPD":
                if (FuncionesDAO::updateFuncion($v["fncod"], $v["fndsc"], $v["fnest"], $v["fntyp"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Permiso actualizado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo actualizar el permiso");
                }
                break;
            case "DEL":
                if (FuncionesDAO::deleteFuncion($v["fncod"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Permiso eliminado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo eliminar el permiso");
                }
                break;
        }
    }

    private function prepareView()
    {
        $v = &$this->viewData;
        
        $v["FormTitle"] = sprintf($this->modes[$v["mode"]], $v["fncod"] ?? "");

        if (!empty($v["errors"])) {
            foreach ($v["errors"] as $field => $errs) {
                $v[$field . "_error"] = implode(", ", $errs);
            }
        }

        $v["readonly"] = ($v["mode"] === "DSP" || $v["mode"] === "DEL") ? "readonly" : "";
        $v["showCommitBtn"] = ($v["mode"] !== "DSP");
        $v["readonly_fncod"] = ($v["mode"] === "UPD" || $v["mode"] === "DSP" || $v["mode"] === "DEL") ? "readonly" : "";        
        $v["estado_ACT"] = (isset($v["fnest"]) && $v["fnest"] === "ACT") ? "selected" : "";
        $v["estado_INA"] = (isset($v["fnest"]) && $v["fnest"] === "INA") ? "selected" : "";
        $v["tipo_CTR"] = (isset($v["fntyp"]) && $v["fntyp"] === "CTR") ? "selected" : "";
        $v["tipo_FNC"] = (isset($v["fntyp"]) && $v["fntyp"] === "FNC") ? "selected" : "";
        $v["tipo_MNU"] = (isset($v["fntyp"]) && $v["fntyp"] === "MNU") ? "selected" : "";

        $v["xsrtoken"] = hash("sha256", json_encode($v));
        $_SESSION[$this->name . "-xsrtoken"] = $v["xsrtoken"];
    }
}