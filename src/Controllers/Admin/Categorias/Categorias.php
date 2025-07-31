<?php
namespace Controllers\Admin\Categorias;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Admin\Categorias as DaoCategorias;
use Views\Renderer;

class Categorias extends PrivateController
{
    private array $viewData = [];
    private string $partialName = "";
    private string $orderBy = "";
    private bool $orderDescending = false;
    private int $pageNumber = 1;
    private int $itemsPerPage = 10;
    private int $totalCount = 0;
    private int $pages = 1;
    private string $estado = "";  // ACT, INA o vacío para todos
    private array $categorias = [];

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

        $tmp = DaoCategorias::getCategorias(
            $this->partialName,
            $this->orderBy,
            $this->estado,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );

        $this->categorias = $tmp["categorias"];
        $this->totalCount = $tmp["total"];
        $this->pages = $this->totalCount > 0 ? ceil($this->totalCount / $this->itemsPerPage) : 1;

        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        $this->setParamsToContext();
        $this->setParamsToViewData();
        Renderer::render("admin/categorias/categorias", $this->viewData);
    }

    private function getParamsFromRequest(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->orderBy = $_GET["orderBy"] ?? $this->orderBy;
        if (!in_array($this->orderBy, ["id_categoria", "nombre_categoria", ""])) {
            $this->orderBy = "";
        }
        $this->estado = $_GET["estado"] ?? $this->estado;
            if (!in_array($this->estado, ["ACT", "INA", ""])) 
                {
                $this->estado = "";
            }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? max(1, intval($_GET["pageNum"])) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? max(1, intval($_GET["itemsPerPage"])) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("admin_categorias_partialName", $this->partialName);
        $this->estado = Context::getContextByKey("admin_categorias_estado", $this->estado);
        $this->orderBy = Context::getContextByKey("admin_categorias_orderBy", $this->orderBy);
        $this->orderDescending = boolval(Context::getContextByKey("admin_categorias_orderDescending", $this->orderDescending));
        $this->pageNumber = intval(Context::getContextByKey("admin_categorias_page", $this->pageNumber));
        $this->itemsPerPage = intval(Context::getContextByKey("admin_categorias_itemsPerPage", $this->itemsPerPage));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("admin_categorias_partialName", $this->partialName, true);
        Context::setContext("admin_categorias_estado", $this->estado, true);
        Context::setContext("admin_categorias_orderBy", $this->orderBy, true);
        Context::setContext("admin_categorias_orderDescending", $this->orderDescending, true);
        Context::setContext("admin_categorias_page", $this->pageNumber, true);
        Context::setContext("admin_categorias_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToViewData(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["categoriasCount"] = $this->totalCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["categorias"] = $this->categorias;

        $this->viewData["estado"] = $this->estado;
        $estadoKey = "estado_" . ($this->estado === "" ? "TODOS" : $this->estado);
        $this->viewData[$estadoKey] = "selected";

        // ordenamiento visual
        if ($this->orderBy !== "") {
            $key = "Order" . ucfirst($this->orderBy);
            $noOrderKey = "OrderBy" . ucfirst($this->orderBy);
            $this->viewData[$noOrderKey] = true;
            if ($this->orderDescending) {
                $key .= "Desc";
            }
            $this->viewData[$key] = true;
        }

        // paginación
        $this->viewData["pagination"] = Paging::getPagination(
            $this->totalCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Admin_Categorias_Categorias",
            "Admin_Categorias_Categorias"
        );
    }
}