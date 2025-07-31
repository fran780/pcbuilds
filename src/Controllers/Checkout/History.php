<?php

namespace Controllers\Checkout;

use Controllers\PrivateController;
use Dao\Orders\Orders as OrdenesDAO;
use Views\Renderer;
use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Checkout_History";

class Order extends PrivateController
{
    private array $viewData;
    private array $modes;

    public function __construct()
    {
        parent::__construct();

        $this->viewData = [
            "mode" => "",
            "orderId" => 0,
            "order_status" => "",
            "shipping_status" => "",
            "order_date" => "",
            "userName" => "",
            "modeDsc" => "",
            "errors" => [],
            "cancelLabel" => "Cancelar",
            "showConfirm" => true,
            "readonly" => "",
            "readonlyShipping" => "" 
        ];

        $this->modes = [
            "UPD" => "Actualizar estado de envío para la orden #%s",
        ];
    }

    public function run(): void
    {
        $this->getQueryParamsData();

        $this->getDataFromDB();

        if ($this->isPostBack()) {
            $this->getBodyData();
            if ($this->validateData()) {
                $this->processData();
            }
        }

        $this->prepareViewData();

        Renderer::render("modules/orders/order", $this->viewData);
    }

    private function throwError(string $message, string $logMessage = "")
    {
        if (!empty($logMessage)) {
            error_log(sprintf("%s - %s", $this->name, $logMessage));
        }
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function innerError(string $scope, string $message)
    {
        if (!isset($this->viewData["errors"][$scope])) {
            $this->viewData["errors"][$scope] = [$message];
        } else {
            $this->viewData["errors"][$scope][] = $message;
        }
    }

    private function getQueryParamsData()
    {
        if (!isset($_GET["mode"])) {
            $this->throwError("Error: Modo no definido.", "Falta parámetro mode");
        }

        $this->viewData["mode"] = $_GET["mode"];

        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError("Error: Modo inválido.", "Modo inválido: " . $this->viewData["mode"]);
        }

        if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
            $this->throwError("Error: ID no válido.", "ID faltante o inválido");
        }

        $this->viewData["orderId"] = intval($_GET["id"]);
    }

    private function getDataFromDB()
    {
        $order = OrdenesDAO::getById($this->viewData["orderId"]);
        if ($order) {
            $this->viewData["order_status"] = $order["order_status"];
            $this->viewData["shipping_status"] = $order["shipping_status"];
            $this->viewData["order_date"] = $order["order_date"];
            $this->viewData["userName"] = $order["userName"] ?? "";
        } else {
            $this->throwError("Orden no encontrada.", "ID: " . $this->viewData["orderId"]);
        }
    }

    private function getBodyData()
    {
        if (!isset($_POST["orderId"]) || intval($_POST["orderId"]) !== $this->viewData["orderId"]) {
            $this->throwError("Error: ID inconsistente.");
        }

        if (!isset($_POST["shipping_status"])) {
            $this->throwError("Error: Falta el estado de envío.");
        }

        if (!isset($_POST["xsrtoken"]) || $_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError("Error de seguridad: Token inválido.");
        }

        $this->viewData["shipping_status"] = trim($_POST["shipping_status"]);
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["shipping_status"])) {
            $this->innerError("shipping_status", "El estado de envío es requerido.");
        }

        return count($this->viewData["errors"]) === 0;
    }

    private function processData()
    {
        if (OrdenesDAO::updateShippingStatus(
            $this->viewData["orderId"],
            $this->viewData["shipping_status"]
        ) > 0) {
            Site::redirectToWithMsg(LIST_URL, "Estado de envío actualizado correctamente.");
        } else {
            $this->innerError("global", "No se pudo actualizar el estado de envío.");
        }
    }

    private function prepareViewData()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["orderId"]
        );

        // Solo el campo shipping_status es editable
        $this->viewData["readonly"] = "readonly";
        $this->viewData["readonlyShipping"] = ""; 

        // Marcar opciones seleccionadas
        $this->viewData["shipping_status_CAMINO"] = $this->viewData["shipping_status"] === "En camino" ? "selected" : "";
        $this->viewData["shipping_status_RECOGER"] = $this->viewData["shipping_status"] === "Listo para recoger" ? "selected" : "";

        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData["errors_" . $scope] = $errorsArray;
            }
        }

        $this->viewData["timestamp"] = time();
        $this->viewData["xsrtoken"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsrtoken"] = $this->viewData["xsrtoken"];
    }
}