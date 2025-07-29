<?php

namespace Controllers\Checkout;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Transactions\Transactions;
use Utilities\Security;
use Utilities\Site;

class HistoryDetail extends PrivateController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Transacción %s"
    ];
    private $readonly = "readonly";
    private $showCommitBtn = false;
    private $txn = [];

    public function run(): void
    {
        try {
            $this->getData();
            $this->setViewData();
            Renderer::render("paypal/history_detail", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Checkout_History",
                $ex->getMessage()
            );
        }
    }

    private function getData(): void
    {
        $this->mode = $_GET["mode"] ?? "DSP";
        $transactionId = intval($_GET["id"] ?? 0);

        if ($transactionId <= 0) {
            throw new \Exception("ID de transacción inválido");
        }

        $this->txn = Transactions::getById($transactionId);

        if (!$this->txn) {
            throw new \Exception("No se encontró la transacción");
        }

        if ($this->txn["usercod"] !== Security::getUserId()) {
            throw new \Exception("Transacción no autorizada para este usuario");
        }

        // Formatear la fecha
        $fecha = new \DateTime($this->txn["transdate"]);
        $this->txn["transdate"] = $fecha->format("Y-m-d");

        // Decodificar el JSON
        $this->txn["orderjson"] = json_decode($this->txn["orderjson"], true);
        $this->txn["json_pretty"] = json_encode($this->txn["orderjson"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function setViewData(): void
    {
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->txn["orderid"]
        );
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["txn"] = $this->txn;
    }
}