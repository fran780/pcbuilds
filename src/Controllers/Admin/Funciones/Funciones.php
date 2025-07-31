<?php
namespace Controllers\Admin\Funciones;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Admin\Funciones as DaoFunciones;
use Views\Renderer;

class Funciones extends PrivateController
{
    private array $viewData = [];
    private string $partialName = "";
    private string $tipoFuncion = "";
    private string $estado = "";
    private string $orderBy = "";
    private bool $orderDescending = false;
    private int $pageNumber = 1;
    private int $itemsPerPage = 10;
    private int $funcionesCount = 0;
    private int $pages = 1;
    private array $funciones = [];

    public function __construct()
    {
        parent::__construct();
        $this->viewData["isNewEnabled"] = parent::isFeatureAutorized($this->name . "\\new");
        $this->viewData["isUpdateEnabled"] = parent::isFeatureAutorized($this->name . "\update");
        $this->viewData["isDeleteEnabled"] = parent::isFeatureAutorized($this->name . "\delete");
    }

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParamsFromRequest();

        $tmp = DaoFunciones::getFunciones(
            $this->partialName,
            $this->tipoFuncion,
            $this->estado,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
   
        $this->funciones = $tmp["funciones"];
        $this->funcionesCount = $tmp["total"];
        $this->pages = max(1, ceil($this->funcionesCount / $this->itemsPerPage));
        $this->pageNumber = min($this->pageNumber, $this->pages);

        $this->setParamsToContext();
        $this->setParamsToDataView();

        Renderer::render("admin/funciones/funciones", $this->viewData);
    }

    private function getParamsFromRequest(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->tipoFuncion = $_GET["tipoFuncion"] ?? $this->tipoFuncion;
        $this->estado = $_GET["estado"] ?? $this->estado;
        $this->orderBy = $_GET["orderBy"] ?? $this->orderBy;

        if (!in_array($this->orderBy, ["fncod", "fndsc"])) {
            $this->orderBy = "";
        }

        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? max(1, intval($_GET["pageNum"])) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? max(1, intval($_GET["itemsPerPage"])) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("admin_funciones_partialName", $this->partialName);
        $this->tipoFuncion = Context::getContextByKey("admin_funciones_tipoFuncion", $this->tipoFuncion);
        $this->estado = Context::getContextByKey("admin_funciones_estado", $this->estado);
        $this->orderBy = Context::getContextByKey("admin_funciones_orderBy", $this->orderBy);
        $this->orderDescending = boolval(Context::getContextByKey("admin_funciones_orderDescending", $this->orderDescending));
        $this->pageNumber = max(1, intval(Context::getContextByKey("admin_funciones_page", $this->pageNumber)));
        $this->itemsPerPage = max(1, intval(Context::getContextByKey("admin_funciones_itemsPerPage", $this->itemsPerPage)));
    }

    private function setParamsToContext(): void
    {
        Context::setContext("admin_funciones_partialName", $this->partialName, true);
        Context::setContext("admin_funciones_tipoFuncion", $this->tipoFuncion, true);
        Context::setContext("admin_funciones_estado", $this->estado, true);
        Context::setContext("admin_funciones_orderBy", $this->orderBy, true);
        Context::setContext("admin_funciones_orderDescending", $this->orderDescending, true);
        Context::setContext("admin_funciones_page", $this->pageNumber, true);
        Context::setContext("admin_funciones_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["tipoFuncion"] = $this->tipoFuncion;
        $this->viewData["estado"] = $this->estado;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["funcionesCount"] = $this->funcionesCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["funciones"] = $this->funciones;

        if ($this->orderBy !== "") {
            $orderByKey = "Order" . ucfirst($this->orderBy);
            $orderByKeyNoOrder = "OrderBy" . ucfirst($this->orderBy);
            $this->viewData[$orderByKeyNoOrder] = true;
            if ($this->orderDescending) {
                $orderByKey .= "Desc";
            }
            $this->viewData[$orderByKey] = true;
        }

        $estadoKey = "estado_" . ($this->estado === "" ? "EMP" : $this->estado);
        $this->viewData[$estadoKey] = "selected";

        $tipoKey = "tipo_" . ($this->tipoFuncion === "" ? "EMP" : $this->tipoFuncion);
        $this->viewData[$tipoKey] = "selected";

        $this->viewData["pagination"] = Paging::getPagination(
            $this->funcionesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Admin_Funciones_Funciones",
            "Admin_Funciones_Funciones"
        );
    }
}
?>