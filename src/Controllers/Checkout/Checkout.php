<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
use Dao\Cart\CartDAO as Cart;
use Utilities\Security;

class Checkout extends PublicController
{
    public function run(): void
    {
        /*
        1) Mostrar el listado de productos a facturar y los detalles y totales de la proforma.
        2) Al dar click en Pagar
            2.1) Crear una orden de Paypal con los productos de la proforma.
            2.2) Redirigir al usuario a la página de Paypal para que complete el pago.
        
        */
        $viewData["carretilla"] = [];

        $carretilla = Cart::getAuthCart(Security::getUserId());
        if ($this->isPostBack()) {
            $processPayment = true;
            if (isset($_POST["removeOne"]) || isset($_POST["addOne"])) {
                $productId = intval($_POST["productId"]);
                $productoDisp = Cart::getProductoDisponible($productId);
                $amount = isset($_POST["removeOne"]) ? -1 : 1;
                if ($amount == 1) {
                    if ($productoDisp["stock"] - $amount >= 0) {
                        Cart::addToAuthCart(
                            $productId,
                            Security::getUserId(),
                            $amount,
                            $productoDisp["precio"]
                        );
                    }
                } else {
                    Cart::addToAuthCart(
                        $productId,
                        Security::getUserId(),
                        $amount,
                        $productoDisp["precio"]
                    );
                }
                $carretilla = Cart::getAuthCart(Security::getUserId());
                $processPayment = false;
            }

            if ($processPayment) {
                $PayPalOrder = new \Utilities\Paypal\PayPalOrder(
                    "test" . (time() - 10000000),
                    "http://localhost/Proyecto_pcbuildshonduras/index.php?page=Checkout_Error",
                    "http://localhost/Proyecto_pcbuildshonduras/index.php?page=Checkout_Accept"
                );

                foreach ($carretilla as $producto) {
                    $PayPalOrder->addItem(
                        $producto["nombre_producto"],
                        $producto["descripcion"],
                        $producto["id_producto"],
                        $producto["crrprc"], // Este debe ser número, no string formateado
                        0,
                        $producto["crrctd"],
                        "DIGITAL_GOODS"
                    );
                }


                $PayPalRestApi = new \Utilities\PayPal\PayPalRestApi(
                    \Utilities\Context::getContextByKey("PAYPAL_CLIENT_ID"),
                    \Utilities\Context::getContextByKey("PAYPAL_CLIENT_SECRET")
                );
                $PayPalRestApi->getAccessToken();
                $response = $PayPalRestApi->createOrder($PayPalOrder);

                if (isset($response->id)) {
                    $_SESSION["orderid"] = $response->id;
                }
                foreach ($response->links as $link) {
                    if ($link->rel == "approve") {
                        \Utilities\Site::redirectTo($link->href);
                    }
                }
                die();
            }
        }
        $finalCarretilla = [];
        $counter = 1;
        $total = 0;
        foreach ($carretilla as $prod) {
            $prod["row"] = $counter;
            $prod["subtotal"] = number_format($prod["crrprc"] * $prod["crrctd"], 2);
            $total += $prod["crrprc"] * $prod["crrctd"];
            $prod["crrprc"] = number_format($prod["crrprc"], 2);
            $finalCarretilla[] = $prod;
            $counter++;
        }
        $viewData["carretilla"] = $finalCarretilla;
        $viewData["total"] = number_format($total, 2);
        \Views\Renderer::render("paypal/checkout", $viewData);
    }
}