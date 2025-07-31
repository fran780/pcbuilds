<?php
namespace Controllers\Admin\FuncionesRoles;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Admin\FuncionesRoles as DaoFuncionesRoles;
use Views\Renderer;

class FuncionesRoles extends PrivateController
{
    private array $viewData = [];
    private string $partialFnCod = "";
    private string $partialFnDsc = "";
    private string $rolescod = "";
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

        $tmp = DaoFuncionesRoles::getFuncionesRoles(
            $this->partialFnCod,
            $this->partialFnDsc,
            $this->rolescod,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );

        $this->funciones = $tmp["funciones_roles"];
        $this->funcionesCount = $tmp["total"];
        $this->pages = $this->funcionesCount > 0 ? ceil($this->funcionesCount / $this->itemsPerPage) : 1;

        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("admin/FuncionesRoles/funciones_roles", $this->viewData);
    }

    private function getParamsFromRequest(): void
    {
        $this->partialFnCod = $_GET["fncod"] ?? $this->partialFnCod;
        $this->partialFnDsc = $_GET["fndsc"] ?? $this->partialFnDsc;
        $this->rolescod = $_GET["rolescod"] ?? $this->rolescod;
        $this->orderBy = $_GET["orderBy"] ?? $this->orderBy;
        if (!in_array($this->orderBy, ["fr.fncod", "fr.fnexp", "fr.rolescod", ""])) {
            $this->orderBy = "";
        }

        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? max(1, intval($_GET["pageNum"])) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? max(1, intval($_GET["itemsPerPage"])) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialFnCod = Context::getContextByKey("admin_funciones_roles_fncod", $this->partialFnCod);
        $this->partialFnDsc = Context::getContextByKey("admin_funciones_roles_fndsc", $this->partialFnDsc);
        $this->rolescod = Context::getContextByKey("admin_funciones_roles_rolescod", $this->rolescod);
        $this->orderBy = Context::getContextByKey("admin_funciones_roles_orderBy", $this->orderBy);
        $this->orderDescending = boolval(Context::getContextByKey("admin_funciones_roles_orderDescending", $this->orderDescending));
        $this->pageNumber = intval(Context::getContextByKey("admin_funciones_roles_page", $this->pageNumber));
        $this->itemsPerPage = intval(Context::getContextByKey("admin_funciones_roles_itemsPerPage", $this->itemsPerPage));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("admin_funciones_roles_fncod", $this->partialFnCod, true);
        Context::setContext("admin_funciones_roles_fndsc", $this->partialFnDsc, true);
        Context::setContext("admin_funciones_roles_rolescod", $this->rolescod, true);
        Context::setContext("admin_funciones_roles_orderBy", $this->orderBy, true);
        Context::setContext("admin_funciones_roles_orderDescending", $this->orderDescending, true);
        Context::setContext("admin_funciones_roles_page", $this->pageNumber, true);
        Context::setContext("admin_funciones_roles_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["fncod"] = $this->partialFnCod;
        $this->viewData["fndsc"] = $this->partialFnDsc;
        $this->viewData["rolescod"] = $this->rolescod;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["funcionesCount"] = $this->funcionesCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["funciones"] = $this->funciones;

        // Marcar columnas ordenadas
        if ($this->orderBy !== "") {
            $orderByKey = "Order" . ucfirst(str_replace(".", "_", $this->orderBy));
            $orderByKeyNoOrder = "OrderBy" . ucfirst(str_replace(".", "_", $this->orderBy));
            $this->viewData[$orderByKeyNoOrder] = true;
            if ($this->orderDescending) {
                $orderByKey .= "Desc";
            }
            $this->viewData[$orderByKey] = true;
        }

        // Paginación
        $this->viewData["pagination"] = Paging::getPagination(
            $this->funcionesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Admin_FuncionesRoles_FuncionesRoles",
            "Admin_FuncionesRoles_FuncionesRoles"
        );
    }
}
?>