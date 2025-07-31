<?php
namespace Controllers\Admin\Categorias;

use Controllers\PrivateController;
use Dao\Admin\Categorias as CategoriasDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_Categorias_Categorias";

class Categoria extends PrivateController
{
    private array $viewData;
    private array $modes;

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "id_categoria" => 0,
            "nombre_categoria" => "",
            "estado_categoria" => "ACT",
            "estado_categoria_ACT" => "",
            "estado_categoria_INA" => "",
            "FormTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "errors" => []
        ];

        $this->modes = [
            "INS" => "Nueva Categoría",
            "UPD" => "Editar Categoría: %s",
            "DEL" => "Eliminar Categoría: %s",
            "DSP" => "Detalle de Categoría: %s"
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

    if ($this->viewData["mode"] === "DEL") {
        $this->processData();
    } else {
        $isValid = $this->validateData();
        if ($isValid) {
            $this->processData();
        }
    }
}

        $this->prepareViewData();
        Renderer::render("admin/categorias/categoria", $this->viewData);
    }

    private function throwError(string $message, string $log = "")
    {
        if ($log) error_log("Categoria - " . $log);
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
            if (!isset($_GET["id_categoria"]) || !is_numeric($_GET["id_categoria"])) {
                $this->throwError("ID inválido");
            }
            $this->viewData["id_categoria"] = intval($_GET["id_categoria"]);
        }
    }

    private function getDataFromDB(): void
    {
        $cat = CategoriasDAO::getCategoriaById($this->viewData["id_categoria"]);
        if (!$cat) {
            $this->throwError("Categoría no encontrada", "ID " . $this->viewData["id_categoria"]);
        }

        $this->viewData["nombre_categoria"] = $cat["nombre_categoria"];
        $this->viewData["estado_categoria"] = $cat["estado_categoria"] ?? "ACT";
    }

    private function getBodyData(): void
    {
        if (!isset($_POST["xsrtoken"]) || $_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError("Token CSRF inválido");
        }

        $this->viewData["nombre_categoria"] = $_POST["nombre_categoria"] ?? "";
        $this->viewData["estado_categoria"] = $_POST["estado_categoria"] ?? "ACT";

        if ($this->viewData["mode"] !== "INS") {
            $postedId = intval($_POST["id_categoria"] ?? 0);
            if ($postedId !== $this->viewData["id_categoria"]) {
                $this->throwError("ID no coincide", "Esperado " . $this->viewData["id_categoria"] . ", recibido " . $postedId);
            }
        }
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["nombre_categoria"])) {
            $this->innerError("nombre_categoria", "Nombre requerido");
        } elseif (
            $this->viewData["mode"] === "INS" &&
            CategoriasDAO::existePorDescripcion($this->viewData["nombre_categoria"])
        ) {
            $this->innerError("nombre_categoria", "Ya existe una categoría con este nombre");
        }

        if (!in_array($this->viewData["estado_categoria"], ["ACT", "INA"])) {
            $this->innerError("estado_categoria", "Estado inválido");
        }

            return count($this->viewData["errors"]) === 0;
    }

    private function processData(): void
    {
        $v = $this->viewData;

        switch ($v["mode"]) {
            case "INS":
                if (CategoriasDAO::insertCategoria($v["nombre_categoria"], $v["estado_categoria"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Categoría creada exitosamente");
                } else {
                    $this->innerError("global", "No se pudo crear la categoría");
                }
                break;

            case "UPD":
                if (CategoriasDAO::updateCategoria($v["id_categoria"], $v["nombre_categoria"], $v["estado_categoria"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Categoría actualizada");
                } else {
                    $this->innerError("global", "No se pudo actualizar la categoría");
                }
                break;

            case "DEL":
                if (CategoriasDAO::deleteCategoria($v["id_categoria"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Categoría eliminada");
                } else {
                    $this->innerError("global", "No se pudo eliminar la categoría");
                }
                break;
        }
    }

    private function prepareViewData(): void
    {
        $v = &$this->viewData;

        $v["FormTitle"] = sprintf($this->modes[$v["mode"]], $v["nombre_categoria"] ?? "");

        foreach ($v["errors"] as $scope => $arr) {
            $v[$scope . "_error"] = implode(", ", $arr);
        }

        if ($v["mode"] === "DSP" || $v["mode"] === "DEL") {
            $v["readonly"] = "readonly";
            if ($v["mode"] === "DSP") $v["showCommitBtn"] = false;
        }

        $v["estado_categoria_ACT"] = ($v["estado_categoria"] === "ACT") ? "selected" : "";
        $v["estado_categoria_INA"] = ($v["estado_categoria"] === "INA") ? "selected" : "";

        //PARA EVITAR QUE SE ELIMINE UNA CATEGORIA QUE ESTÁ SIENDO USADA
       if ($v["mode"] === "DEL")
         {
            $productosCount = \Dao\Admin\Products::countByCategoria($v["id_categoria"]);
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