<?php
namespace Controllers;

use \Utilities\Site;
use \Views\Renderer;
use \Dao\Productos\ProductosDAO;
use Controllers\PublicController;
use Utilities\Context;
use Utilities\Paging;

class Tienda extends PublicController
{
    private $partialName = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 24;
    private $viewData = [];
    private $products = [];
    private $productsCount = 0;
    private $pages = 0;
    private $selectedBrand = "";
    private $selectedCategory = "";

    public function run(): void
    {
        Site::addLink("public/css/paginas/tienda.css");
        $this->getParamsFromContext();
        $this->getParams();

        $tmpProducts = ProductosDAO::getAllProductsPaginated(
            $this->partialName,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage,
            $this->orderBy,
            $this->selectedBrand !== "" ? [$this->selectedBrand] : [],
            $this->selectedCategory !== "" ? [$this->selectedCategory] : []
        );



        $this->products = $tmpProducts["products"];
        $this->productsCount = $tmpProducts["total"];
        $this->pages = $this->productsCount > 0 ? ceil($this->productsCount / $this->itemsPerPage) : 1;

        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        $this->setParamsToContext();
        $this->setParamsToDataView();

        Renderer::render("paginas/tienda", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialName = $_GET["partialName"] ?? $this->partialName;
        $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["nombre_producto", "precio", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
        if ($this->orderBy === "clear") {
            $this->orderBy = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;

        $this->selectedBrand = $_GET["id_marca"] ?? "";
        $this->selectedCategory = $_GET["id_categoria"] ?? "";

    }

    private function getParamsFromContext(): void
    {
        $this->partialName = Context::getContextByKey("products_partialName");
        $this->orderBy = Context::getContextByKey("products_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("products_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("products_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("products_itemsPerPage"));
        $this->selectedBrand = Context::getContextByKey("products_selectedBrand") ?? [];
        $this->selectedCategory = Context::getContextByKey("products_selectedCategory") ?? [];
        
        $this->selectedBrand = Context::getContextByKey("products_selectedBrand") ?? "";
        $this->selectedCategory = Context::getContextByKey("products_selectedCategory") ?? "";

        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 8;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("products_partialName", $this->partialName, true);
        Context::setContext("products_orderBy", $this->orderBy, true);
        Context::setContext("products_orderDescending", $this->orderDescending, true);
        Context::setContext("products_page", $this->pageNumber, true);
        Context::setContext("products_itemsPerPage", $this->itemsPerPage, true);
        Context::setContext("products_selectedBrand", $this->selectedBrand, true);
        Context::setContext("products_selectedCategory", $this->selectedCategory, true);

    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialName"] = $this->partialName;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["productsCount"] = $this->productsCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["products"] = $this->products;
        
        $this->viewData["selectedBrand"] = $this->selectedBrand;
        $this->viewData["selectedCategory"] = $this->selectedCategory;

        $categories = ProductosDAO::getAllCategories();
        $this->viewData["categories"] = $categories;
        foreach ($categories as $cat) {
            $key = "category_" . $cat["id_categoria"];
            $this->viewData[$key] = ($cat["id_categoria"] == $this->selectedCategory) ? "selected" : "";
        }

        $brands = ProductosDAO::getAllBrands();
        $this->viewData["brands"] = $brands;
        foreach ($brands as $brand) {
            $key = "brand_" . $brand["id_marca"];
            $this->viewData[$key] = ($brand["id_marca"] == $this->selectedBrand) ? "selected" : "";
        }

        $queryParams = [
            "page" => "Tienda",
            "partialName" => $this->partialName,
            "orderBy" => $this->orderBy,
            "orderDescending" => $this->orderDescending ? "1" : "0",
            "itemsPerPage" => $this->itemsPerPage
        ];

        $queryParams["id_marca"] = $this->selectedBrand;
        $queryParams["id_categoria"] = $this->selectedCategory;


        $baseUrl = "index.php?" . http_build_query($queryParams);

        $pagination = Paging::getPagination(
            $this->productsCount,
            $this->itemsPerPage,
            $this->pageNumber,
            $baseUrl,
            "Tienda"
        );
        $this->viewData["pagination"] = $pagination;
    }
}
?>