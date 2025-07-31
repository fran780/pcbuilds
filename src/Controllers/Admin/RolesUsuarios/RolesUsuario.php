<?php
namespace Controllers\Admin\RolesUsuarios;

use Controllers\PrivateController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Admin\RolesUsuarios as DaoRolesUsuarios;
use Views\Renderer;

class RolesUsuario extends PrivateController
{
    private array $viewData = [];
    private string $partialUserCod = "";
    private string $partialRoleCod = "";
    private string $estado = "";
    private string $orderBy = "";
    private bool $orderDescending = false;
    private int $pageNumber = 1;
    private int $itemsPerPage = 10;
    private int $asignacionesCount = 0;
    private int $pages = 1;
    private array $asignaciones = [];

    public function __construct()
    {
        parent::__construct();
        $this->viewData["isNewEnabled"] = parent::isFeatureAutorized($this->name . "\\new");
        $this->viewData["isUpdateEnabled"] = parent::isFeatureAutorized($this->name . "\\update");
        $this->viewData["isDeleteEnabled"] = parent::isFeatureAutorized($this->name . "\\delete");
    }

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParamsFromRequest();

        $tmp = DaoRolesUsuarios::getRolesUsuarios(
            $this->partialUserCod,
            $this->partialRoleCod,
            $this->estado,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );

        $this->asignaciones = $tmp["roles_usuarios"];
        $this->asignacionesCount = $tmp["total"];
        $this->pages = $this->asignacionesCount > 0 ? ceil($this->asignacionesCount / $this->itemsPerPage) : 1;

        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("Admin/RolesUsuarios/rolesusuarios", $this->viewData);    }

    private function getParamsFromRequest(): void
    {
        $this->partialUserCod = $_GET["partialUserCod"] ?? $this->partialUserCod;
        $this->partialRoleCod = $_GET["partialRoleCod"] ?? $this->partialRoleCod;
        $this->estado = $_GET["estado"] ?? $this->estado;
        $this->orderBy = $_GET["orderBy"] ?? $this->orderBy;

        if (!in_array($this->orderBy, ["usercod", "rolescod"])) {
            $this->orderBy = "";
        }

        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? max(1, intval($_GET["pageNum"])) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? max(1, intval($_GET["itemsPerPage"])) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialUserCod = Context::getContextByKey("admin_rolesusuarios_partialUserCod", $this->partialUserCod);
        $this->partialRoleCod = Context::getContextByKey("admin_rolesusuarios_partialRoleCod", $this->partialRoleCod);
        $this->estado = Context::getContextByKey("admin_rolesusuarios_estado", $this->estado);
        $this->orderBy = Context::getContextByKey("admin_rolesusuarios_orderBy", $this->orderBy);
        $this->orderDescending = boolval(Context::getContextByKey("admin_rolesusuarios_orderDescending", $this->orderDescending));
        $this->pageNumber = intval(Context::getContextByKey("admin_rolesusuarios_page", $this->pageNumber));
        $this->itemsPerPage = intval(Context::getContextByKey("admin_rolesusuarios_itemsPerPage", $this->itemsPerPage));

        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("admin_rolesusuarios_partialUserCod", $this->partialUserCod, true);
        Context::setContext("admin_rolesusuarios_partialRoleCod", $this->partialRoleCod, true);
        Context::setContext("admin_rolesusuarios_estado", $this->estado, true);
        Context::setContext("admin_rolesusuarios_orderBy", $this->orderBy, true);
        Context::setContext("admin_rolesusuarios_orderDescending", $this->orderDescending, true);
        Context::setContext("admin_rolesusuarios_page", $this->pageNumber, true);
        Context::setContext("admin_rolesusuarios_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialUserCod"] = $this->partialUserCod;
        $this->viewData["partialRoleCod"] = $this->partialRoleCod;
        $this->viewData["estado"] = $this->estado;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["asignacionesCount"] = $this->asignacionesCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["asignaciones"] = $this->asignaciones;

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

        $this->viewData["pagination"] = Paging::getPagination(
            $this->asignacionesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Admin_RolesUsuarios_RolesUsuario",
            "Admin_RolesUsuarios_RolesUsuario"
        );
    }
}
?>