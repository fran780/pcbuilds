<?php

namespace Controllers\Checkout;

use Controllers\PrivateController;
use Dao\Transactions\Transactions;
use Utilities\Security;
use Utilities\Context;
use Utilities\Paging;

class History extends PrivateController
{
    private $orderId = "";
    private $status = "ALL";
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $transactions = [];
    private $total = 0;
    private $pages = 1;
    private $viewData = [];


    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();

        $userId = Security::getUserId();
        $result = Transactions::getTransactions(
            $userId,
            $this->orderId,
            $this->status === "ALL" ? "" : $this->status,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->transactions = $result["transactions"];
        $this->total = $result["total"];
        $this->pages = max(1, ceil($this->total / $this->itemsPerPage));
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }

        foreach ($this->transactions as &$txn) {
            $date = new \DateTime($txn["transdate"]);
            $txn["transdate"] = $date->format("Y-m-d");
            $txn["amount"] = number_format($txn["amount"], 2);
            $txn["statusClass"] = $this->getStatusClass($txn["transstatus"]);
        }

        $this->setParamsToContext();
        $this->prepareViewData();

        \Views\Renderer::render("paypal/history", $this->viewData);
    }

    private function getParams(): void
    {
        $this->orderId = $_GET["orderid"] ?? $this->orderId;
        $this->status = $_GET["status"] ?? $this->status;
        if (!in_array($this->status, ["ALL", "COMPLETED", "PENDING", "FAILED"])) {
            $this->status = "ALL";
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
        $this->status = Context::getContextByKey("history_status") ?: $this->status;
        $this->pageNumber = intval(Context::getContextByKey("history_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("history_itemsPerPage"));
        if ($this->pageNumber < 1) {
            $this->pageNumber = 1;
        }
        if ($this->itemsPerPage < 1) {
            $this->itemsPerPage = 10;
        }
        if ($this->status === "") {
            $this->status = "ALL";
        }
    }

    private function setParamsToContext(): void
    {
        Context::setContext("history_orderid", $this->orderId, true);
        Context::setContext("history_status", $this->status, true);
        Context::setContext("history_page", $this->pageNumber, true);
        Context::setContext("history_itemsPerPage", $this->itemsPerPage, true);
    }

    private function prepareViewData(): void
    {
        $this->viewData["orderid"] = $this->orderId;
        $this->viewData["status"] = $this->status;
        $statusKey = "status_" . $this->status;
        $this->viewData[$statusKey] = "selected";
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["transactions"] = $this->transactions;
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
}