<?php
namespace Controllers\Admin\Products;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Admin\Products as DaoProducts;
use Views\Renderer;

class Products extends PrivateController
{  
    private array $viewData = [];
    private string $partialName = "";
    private string $status = "";
    private string $orderBy = "";
    private bool $orderDescending = false;
    private int $pageNumber = 1;
    private int $itemsPerPage = 10;
    private int $productsCount = 0; 
    private int $pages = 1;
    private array $products = [];

    public function __construct()
    {
        parent::__construct();
        // permisos para botones de acción
        $this->viewData["isNewEnabled"] = parent::isFeatureAutorized($this->name . "\\new");
        $this->viewData["isUpdateEnabled"] = parent::isFeatureAutorized($this->name . "\update");
        $this->viewData["isDeleteEnabled"] = parent::isFeatureAutorized($this->name . "\delete");
    }

    public function run(): void
{
    $this->getParamsFromContext();
    $this->getParamsFromRequest();

    $tmp = DaoProducts::getProducts(
        $this->partialName,
        0 ,            
        0,            
        $this->idEstado,
        $this->orderBy,
        $this->orderDescending,
        $this->pageNumber - 1,
        $this->itemsPerPage
    );

    $this->products = $tmp["products"];
    $this->productsCount = $tmp["total"];
    $this->pages = $this->productsCount > 0 ? ceil($this->productsCount / $this->itemsPerPage) : 1;

    if ($this->pageNumber > $this->pages) {
        $this->pageNumber = $this->pages;
    }

    $this->setParamsToContext();
    $this->setParamsToDataView();
    Renderer::render("admin/products/products", $this->viewData);
}


 private int $idEstado = 0;

    private function getParamsFromRequest(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->orderBy = $_GET["orderBy"] ?? $this->orderBy;
        if (!in_array($this->orderBy, ["productId", "productName", "productPrice", ""])) {
            $this->orderBy = "";
        }

        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? max(1, intval($_GET["pageNum"])) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? max(1, intval($_GET["itemsPerPage"])) : $this->itemsPerPage;

       
        $this->idEstado = isset($_GET["estado"]) && is_numeric($_GET["estado"])
            ? intval($_GET["estado"])
            : 0;
    }


    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("admin_products_partialName", $this->partialName);
        $this->status = Context::getContextByKey("admin_products_status", $this->status);
        $this->orderBy = Context::getContextByKey("admin_products_orderBy", $this->orderBy);
        $this->orderDescending = boolval(Context::getContextByKey("admin_products_orderDescending", $this->orderDescending));
        $this->pageNumber = intval(Context::getContextByKey("admin_products_page", $this->pageNumber));
        $this->itemsPerPage = intval(Context::getContextByKey("admin_products_itemsPerPage", $this->itemsPerPage));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("admin_products_partialName", $this->partialName, true);
        Context::setContext("admin_products_status", $this->status, true);
        Context::setContext("admin_products_orderBy", $this->orderBy, true);
        Context::setContext("admin_products_orderDescending", $this->orderDescending, true);
        Context::setContext("admin_products_page", $this->pageNumber, true);
        Context::setContext("admin_products_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["status"] = $this->status;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["productsCount"] = $this->productsCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["products"] = $this->products;

        // Marcar columnas ordenadas
        if ($this->orderBy !== "") {
            $orderByKey = "Order" . ucfirst($this->orderBy);
            $orderByKeyNoOrder = "OrderBy" . ucfirst($this->orderBy);
            $this->viewData[$orderByKeyNoOrder] = true;
            if ($this->orderDescending) {
                $orderByKey .= "Desc";
            }
            $this->viewData[$orderByKey] = true;
        }

        // Marcar filtro seleccionado
        $statusKey = "status_" . ($this->status === "" ? "EMP" : $this->status);
        $this->viewData[$statusKey] = "selected";

        // Paginación
        $this->viewData["pagination"] = Paging::getPagination(
            $this->productsCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Admin_Products_Products",
            "Admin_Products_Products"
        );
    }
}
?>
