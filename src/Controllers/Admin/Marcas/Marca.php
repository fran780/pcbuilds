<?php
namespace Controllers\Admin\Marcas;

use Controllers\PrivateController;
use Dao\Admin\Marcas as MarcasDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_Marcas_Marcas";

class Marca extends PrivateController
{
    private array $viewData;
    private array $modes;

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "id_marca" => 0,
            "nombre_marca" => "",
            "estado_marca" => "ACT",
            "estado_marca_ACT" => "",
            "estado_marca_INA" => "",
            "FormTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "errors" => []
        ];

        $this->modes = [
            "INS" => "Nueva Marca",
            "UPD" => "Editar Marca: %s",
            "DEL" => "Eliminar Marca: %s",
            "DSP" => "Detalle de Marca: %s"
        ];
    }

    public function run(): void
    {
        $this->getQueryParams();

        if ($this->viewData["mode"] !== "INS") {
            $this->getDataFromDB();
        }

        if ($this->isPostBack()) {
            $this->getBodyData();
            if ($this->viewData["mode"] === "DEL" || $this->validateData()) {
                $this->processData();
            }
        }

        $this->prepareViewData();
        Renderer::render("admin/marcas/marca", $this->viewData);
    }

    private function throwError(string $message, string $log = "")
    {
        if ($log) error_log("Marca - " . $log);
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function innerError(string $scope, string $msg)
    {
        $this->viewData["errors"][$scope][] = $msg;
    }

    private function getQueryParams(): void
    {
        if (!isset($_GET["mode"]) || !isset($this->modes[$_GET["mode"]])) {
            $this->throwError("Modo inválido o no especificado");
        }
        $this->viewData["mode"] = $_GET["mode"];

        if ($_GET["mode"] !== "INS") {
            if (!isset($_GET["id_marca"]) || !is_numeric($_GET["id_marca"])) {
                $this->throwError("ID inválido");
            }
            $this->viewData["id_marca"] = intval($_GET["id_marca"]);
        }
    }

    private function getDataFromDB(): void
    {
        $marca = MarcasDAO::getMarcaById($this->viewData["id_marca"]);
        if (!$marca) {
            $this->throwError("Marca no encontrada", "ID " . $this->viewData["id_marca"]);
        }

        $this->viewData["nombre_marca"] = $marca["nombre_marca"];
        $this->viewData["estado_marca"] = $marca["estado_marca"] ?? "ACT";
    }

    private function getBodyData(): void
    {
        if (!isset($_POST["xsrtoken"]) || $_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError("Token CSRF inválido");
        }

        $this->viewData["nombre_marca"] = $_POST["nombre_marca"] ?? "";
        $this->viewData["estado_marca"] = $_POST["estado_marca"] ?? "ACT";

        if ($this->viewData["mode"] !== "INS") {
            $postedId = intval($_POST["id_marca"] ?? 0);
            if ($postedId !== $this->viewData["id_marca"]) {
                $this->throwError("ID no coincide", "Esperado " . $this->viewData["id_marca"] . ", recibido " . $postedId);
            }
        }
    }

    private function validateData(): bool
    {
       if (Validators::IsEmpty($this->viewData["nombre_marca"])) {
            $this->innerError("nombre_marca", "Nombre requerido");
        } elseif (
            $this->viewData["mode"] === "INS" &&
            MarcasDAO::existePorDescripcion($this->viewData["nombre_marca"])
        ) {
            $this->innerError("nombre_marca", "Ya existe una marca con este nombre");
        }

        if (!in_array($this->viewData["estado_marca"], ["ACT", "INA"])) {
            $this->innerError("estado_marca", "Estado inválido");
        }

       

        return count($this->viewData["errors"]) === 0;
    }

    private function processData(): void
    {
        $v = $this->viewData;

        switch ($v["mode"]) {
            case "INS":
                if (MarcasDAO::insertMarca($v["nombre_marca"], $v["estado_marca"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Marca creada exitosamente");
                } else {
                    $this->innerError("global", "No se pudo crear la marca");
                }
                break;

            case "UPD":
                if (MarcasDAO::updateMarca($v["id_marca"], $v["nombre_marca"], $v["estado_marca"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Marca actualizada");
                } else {
                    $this->innerError("global", "No se pudo actualizar la marca");
                }
                break;

            case "DEL":
                if (MarcasDAO::deleteMarca($v["id_marca"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Marca eliminada");
                } else {
                    $this->innerError("global", "No se pudo eliminar la marca");
                }
                break;
        }
    }

    private function prepareViewData(): void
    {
        $v = &$this->viewData;

        $v["FormTitle"] = sprintf($this->modes[$v["mode"]], $v["nombre_marca"] ?? "");

        foreach ($v["errors"] as $scope => $arr) {
            $v[$scope . "_error"] = implode(", ", $arr);
        }

        if ($v["mode"] === "DSP" || $v["mode"] === "DEL") {
            $v["readonly"] = "readonly";
            if ($v["mode"] === "DSP") $v["showCommitBtn"] = false;
        }

        $v["estado_marca_ACT"] = ($v["estado_marca"] === "ACT") ? "selected" : "";
        $v["estado_marca_INA"] = ($v["estado_marca"] === "INA") ? "selected" : "";

        // Prevención de borrado si hay productos asociados
        if ($v["mode"] === "DEL") {
            $productosCount = \Dao\Admin\Products::countByMarca($v["id_marca"]);
            $v["productos_count"] = $productosCount;
            $v["disableConfirmBtn"] = ($productosCount > 0);
            $v["showWarningMsg"] = ($productosCount > 0);
            $v["showCommitBtn"] = ($productosCount == 0);
        }

        $v["timestamp"] = time();
        $v["xsrtoken"] = hash("sha256", json_encode($v));
        $_SESSION[$this->name . "-xsrtoken"] = $v["xsrtoken"];
    }
}