<?php

namespace Controllers\Checkout;

use Controllers\PrivateController;
use Dao\Orders\Orders;
use Utilities\Security;
use Utilities\Context;
use Utilities\Paging;

class History extends PrivateController
{
    private $orderId = "";
    private $orderStatus = "ALL";
    private $shippingStatus = "";
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $orders = [];
    private $total = 0;
    private $pages = 1;
    private $viewData = [];


    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();

        $userId = Security::getUserId();
        $result = Orders::getAll(
            $userId,
            $this->orderId,
            $this->orderStatus === "ALL" ? "" : $this->orderStatus,
            $this->shippingStatus === "ALL" ? "" : $this->shippingStatus,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->orders = $result["orders"];
        error_log(print_r($this->orders, true));

        $this->total = $result["total"];
        $this->pages = max(1, ceil($this->total / $this->itemsPerPage));
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        foreach ($this->orders as &$order) {
            $date = new \DateTime($order["orderdate"]);
            $order["orderdate"] = $date->format("Y-m-d");
            $order["statusClass"] = $this->getStatusClass($order["order_status"]);
            $order["shippingStatusClass"] = $this->getShippingStatusClass($order["shipping_status"]);
            $order["total"] = number_format($order["total"], 2);
            $order["currency"] = $order["currency"] ?? "USD"; 
        }

        $this->setParamsToContext();
        $this->prepareViewData();

        \Views\Renderer::render("paypal/history", $this->viewData);
    }

    private function getParams(): void
    {
        $this->orderId = $_GET["orderid"] ?? $this->orderId;
        $this->orderStatus = $_GET["status"] ?? $this->orderStatus;
        if (!in_array($this->orderStatus, ["ALL", "COMPLETED", "PENDING", "FAILED"])) {
            $this->orderStatus = "ALL";
        }
        $this->shippingStatus = $_GET["shippingStatus"] ?? $this->shippingStatus;
        if (!in_array($this->shippingStatus, ["ALL", "SHIPPED", "PENDING", "DELIVERED"])) {
            $this->shippingStatus = "ALL";
        }
        $this->pageNumber = intval($_GET["pageNum"] ?? $this->pageNumber);
        $this->itemsPerPage = intval($_GET["itemsPerPage"] ?? $this->itemsPerPage);
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
    }

    private function getParamsFromContext(): void
    {
        $this->orderId = Context::getContextByKey("history_orderid");
        $this->orderStatus = Context::getContextByKey("history_status") ?: $this->orderStatus;
        $this->shippingStatus = Context::getContextByKey("history_shippingStatus") ?: $this->shippingStatus;
        $this->pageNumber = intval(Context::getContextByKey("history_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("history_itemsPerPage"));
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
        if ($this->orderStatus === "") {
            $this->orderStatus = "ALL";
        }
        if ($this->shippingStatus === "") {
            $this->shippingStatus = "ALL";
        }
    }

    private function setParamsToContext(): void
    {
        Context::setContext("history_orderid", $this->orderId, true);
        Context::setContext("history_orderstatus", $this->orderStatus, true);
        Context::setContext("history_shippingStatus", $this->shippingStatus, true);
        Context::setContext("history_page", $this->pageNumber, true);
        Context::setContext("history_itemsPerPage", $this->itemsPerPage, true);
    }

    private function prepareViewData(): void
    {
        $this->viewData["orderid"] = $this->orderId;
        $this->viewData["status"] = $this->orderStatus;
        $statusKey = "status_" . $this->orderStatus;
        $this->viewData[$statusKey] = "selected";
        $this->viewData["shippingStatus"] = $this->shippingStatus;
        $shippingStatusKey = "shippingStatus_" . $this->shippingStatus;
        $this->viewData[$shippingStatusKey] = "selected";
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["orders"] = $this->orders;
        $this->viewData["pagination"] = Paging::getPagination(
            $this->total,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Checkout_History",
            "Checkout_History"
        );
    }

    private function getStatusClass(string $status): string
    {
        $map = [
            "COMPLETED" => "text-success",
            "PENDING" => "text-warning",
            "FAILED" => "text-danger"
        ];
        return $map[$status] ?? "";
    }

    private function getShippingStatusClass(string $status): string
    {
        $map = [
            "SHIPPED" => "text-success",
            "PENDING" => "text-warning",
            "DELIVERED" => "text-info"
        ];
        return $map[$status] ?? "";
    }
}