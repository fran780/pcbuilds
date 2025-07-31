<?php
namespace Controllers\Admin\Marcas;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Admin\Marcas as DaoMarcas;
use Views\Renderer;

class Marcas extends PrivateController
{
    private array $viewData = [];
    private string $partialName = "";
    private string $orderBy = "";
    private bool $orderDescending = false;
    private int $pageNumber = 1;
    private int $itemsPerPage = 10;
    private int $totalCount = 0;
    private int $pages = 1;
    private string $estado = "";
    private array $marcas = [];

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

        $tmp = DaoMarcas::getMarcas(
            $this->partialName,
            $this->orderBy,
            $this->estado,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );

        $this->marcas = $tmp["marcas"];
        
        $this->totalCount = $tmp["total"];
        $this->pages = $this->totalCount > 0 ? ceil($this->totalCount / $this->itemsPerPage) : 1;

        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        $this->setParamsToContext();
        $this->setParamsToViewData();
        Renderer::render("admin/marcas/marcas", $this->viewData);
    }

    private function getParamsFromRequest(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->orderBy = $_GET["orderBy"] ?? $this->orderBy;
        if (!in_array($this->orderBy, ["id_marca", "nombre_marca", ""])) {
            $this->orderBy = "";
        }
        $this->estado = $_GET["estado"] ?? $this->estado;
        if (!in_array($this->estado, ["ACT", "INA", ""])) {
            $this->estado = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? max(1, intval($_GET["pageNum"])) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? max(1, intval($_GET["itemsPerPage"])) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("admin_marcas_partialName", $this->partialName);
        $this->estado = Context::getContextByKey("admin_marcas_estado", $this->estado);
        $this->orderBy = Context::getContextByKey("admin_marcas_orderBy", $this->orderBy);
        $this->orderDescending = boolval(Context::getContextByKey("admin_marcas_orderDescending", $this->orderDescending));
        $this->pageNumber = intval(Context::getContextByKey("admin_marcas_page", $this->pageNumber));
        $this->itemsPerPage = intval(Context::getContextByKey("admin_marcas_itemsPerPage", $this->itemsPerPage));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("admin_marcas_partialName", $this->partialName, true);
        Context::setContext("admin_marcas_estado", $this->estado, true);
        Context::setContext("admin_marcas_orderBy", $this->orderBy, true);
        Context::setContext("admin_marcas_orderDescending", $this->orderDescending, true);
        Context::setContext("admin_marcas_page", $this->pageNumber, true);
        Context::setContext("admin_marcas_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToViewData(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["marcasCount"] = $this->totalCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["marcas"] = $this->marcas;

        $this->viewData["estado"] = $this->estado;
        $estadoKey = "estado_" . ($this->estado === "" ? "TODOS" : $this->estado);
        $this->viewData[$estadoKey] = "selected";

        if ($this->orderBy !== "") {
            $key = "Order" . ucfirst($this->orderBy);
            $noOrderKey = "OrderBy" . ucfirst($this->orderBy);
            $this->viewData[$noOrderKey] = true;
            if ($this->orderDescending) {
                $key .= "Desc";
            }
            $this->viewData[$key] = true;
        }

        $this->viewData["pagination"] = Paging::getPagination(
            $this->totalCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Admin_Marcas_Marcas",
            "Admin_Marcas_Marcas"
        );
    }
}