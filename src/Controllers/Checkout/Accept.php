<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
use Utilities\Security;
use Dao\Transactions\Transactions;

class Accept extends PublicController
{
    public function run(): void
    {
        $dataview = [];
        $token = $_GET["token"] ?? "";
        $session_token = $_SESSION["orderid"] ?? "";
        $orderId = $token;

        $result = null;


        // ✅ Si el token es válido y coincide con sesión, capturar la orden
        if ($token !== "" && $token === $session_token) {
            $PayPalRestApi = new \Utilities\PayPal\PayPalRestApi(
                \Utilities\Context::getContextByKey("PAYPAL_CLIENT_ID"),
                \Utilities\Context::getContextByKey("PAYPAL_CLIENT_SECRET")
            );

            $result = $PayPalRestApi->captureOrder($session_token);
            $fullDetails = $PayPalRestApi->getOrderDetails($session_token);

            $dataview["orderjson"] = json_encode($result, JSON_PRETTY_PRINT);

            // ✅ Solo si fue completada, se guarda y se limpia la sesión
            if ($result && isset($result->status) && $result->status === "COMPLETED") {
                $orderId = $result->id ?? $session_token;
                $orderFile = sprintf("orders/order_%s.json", $orderId);
                @file_put_contents($orderFile, json_encode($result, JSON_PRETTY_PRINT));

                // ✅ Finaliza carrito y limpia
                \Dao\Cart\CartDAO::finalizeCart(\Utilities\Security::getUserId());
                \Utilities\Context::setContext("CART_ITEMS", 0);
                unset($_SESSION["orderid"]);
            }
        }

        // ✅ Si no hay resultado, intentar cargar desde archivo (permite recargar)
        if (!$result && $token !== "") {
            $orderFile = sprintf("orders/order_%s.json", $token);
            if (file_exists($orderFile)) {
                $result = json_decode(file_get_contents($orderFile));
                $dataview["orderjson"] = json_encode($result, JSON_PRETTY_PRINT);
            }
        }

        // ❌ Si sigue sin haber orden válida, redirige
        if (!$result || !isset($result->status) || $result->status !== "COMPLETED") {
            header("Location: index.php");
            exit;
        }

        // ✅ Preparar datos para la vista
        $amount = "";
        $currency = "";
        $paypalFee = "";
        $netAmount = "";
        $formattedDate = "";
        $detail_per_product = [];

        if (isset($result->purchase_units[0]->payments->captures[0]->amount)) {
            $amount = $result->purchase_units[0]->payments->captures[0]->amount->value;
            $currency = $result->purchase_units[0]->payments->captures[0]->amount->currency_code;
        }

        if (isset($result->purchase_units[0]->payments->captures[0]->seller_receivable_breakdown)) {
            $breakdown = $result->purchase_units[0]->payments->captures[0]->seller_receivable_breakdown;
            $paypalFee = $breakdown->paypal_fee->value ?? "";
            $netAmount = $breakdown->net_amount->value ?? "";
        }

        if (isset($fullDetails->purchase_units[0]->items)) {
            foreach ($fullDetails->purchase_units[0]->items as $item) {
                $detail_per_product[] = [
                    "name" => $item->name ?? "",
                    "description" => $item->description ?? "",
                    "sku" => $item->sku ?? "",
                    "quantity" => $item->quantity ?? 0,
                    "unit_amount" => $item->unit_amount->value ?? 0,
                    "currency_code" => $item->unit_amount->currency_code ?? "",
                    "tax" => $item->tax->value ?? 0
                ];
            }
        }


        $rawDate = $result->purchase_units[0]->payments->captures[0]->update_time ?? "";
        if (!empty($rawDate)) {
            $dt = new \DateTime($rawDate);
            $dt->setTimezone(new \DateTimeZone("America/Tegucigalpa"));
            $meses = [
                "January" => "enero", "February" => "febrero", "March" => "marzo",
                "April" => "abril", "May" => "mayo", "June" => "junio",
                "July" => "julio", "August" => "agosto", "September" => "septiembre",
                "October" => "octubre", "November" => "noviembre", "December" => "diciembre"
            ];
            $mes = $meses[$dt->format("F")];
            $hora = str_replace(["AM", "PM"], ["a. m.", "p. m."], $dt->format("h:i A"));
            $formattedDate = $dt->format("d") . " de " . $mes . " de " . $dt->format("Y") . ", " . $hora;
        }

        $dataview["order"] = [
            "id" => $result->id ?? "",
            "update_time" => $formattedDate,
            "payer_name" => (isset($result->payer->name)) ?
                trim(($result->payer->name->given_name ?? "") . " " . ($result->payer->name->surname ?? "")) : "",
            "payer_email" => $result->payer->email_address ?? "",
            "amount" => $amount,
            "currency" => $currency,
            "paypal_fee" => $paypalFee,
            "net_amount" => $netAmount,
            "detail_per_product" => $detail_per_product,
        ];

        Transactions::addTransaction(
            \Utilities\Security::getUserId(),
            $orderId,
            $result->status ?? "",
            floatval($amount),
            $currency,
            json_encode($result)
        );

        \Views\Renderer::render("paypal/accept", $dataview);
    }
}