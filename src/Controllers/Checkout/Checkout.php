<?php

namespace Controllers\Checkout;

use Dao\Cart\CartDAO as Cart;
use Utilities\Security;
use Utilities\Site;
use Controllers\PrivateController;

class Checkout extends PrivateController
{
    public function run(): void
    {
        Site::addLink("public/css/paginas/checkout.css");

        $viewData = [];

        // ✅ Obtener la carretilla actual del usuario logueado
        $carretilla = Cart::getAuthCart(Security::getUserId());

        if ($this->isPostBack()) {
            // ✅ Cancelar compra y vaciar carretilla
            if (isset($_POST["cancelPurchase"])) {
                Cart::clearCart(Security::getUserId());
                \Utilities\Context::setContext("CART_ITEMS", 0);
                Site::redirectTo("index.php?page=Tienda");
                return;
            }

            // ✅ Verificar que la carretilla no esté vacía o con cantidades inválidas
            if (!is_array($carretilla) || count($carretilla) === 0) {
                Site::redirectTo("index.php?page=Carretilla");
                return;
            }

            $totalProductos = 0;
            foreach ($carretilla as $producto) {
                if ($producto["crrctd"] > 0) {
                    $totalProductos += $producto["crrctd"];
                }
            }

            if ($totalProductos <= 0) {
                Site::redirectTo("index.php?page=Carretilla");
                return;
            }

            $orderData = [];
            $orderData["usercod"] = Security::getUserId();
            $orderData["order_status"] = "PENDING"; 
            $orderData["shipping_status"] = "PENDING"; 
            $orderData["total"] = Cart::getTotalCart(Security::getUserId());
            $orderData["currency"] = "USD";
            $orderData["orderdate"] = date("Y-m-d H:i:s");


            //Aqui debo guardar la orden en la base de datos
            $orderId = \Dao\Orders\Orders::insertOrder($orderData);

            //Insertar cada item del Carrito en la tabla de ordenes_detalle
            foreach ($carretilla as $producto) {
                $orderItemData = [];
                $orderItemData["orderid"] = $orderId;
                $orderItemData["id_producto"] = $producto["id_producto"];
                $orderItemData["cantidad"] = $producto["crrctd"];
                $orderItemData["precio"] = $producto["crrprc"];

                \Dao\Orders\OrderItems::insertOrderItem($orderItemData);
            }

            

            // ✅ Procesar pago
            $PayPalOrder = new \Utilities\Paypal\PayPalOrder(
                "test" . (time() - 10000000),
                "http://localhost/negociosweb/Proyecto_pcbuildshonduras/index.php?page=Checkout_Error",
                "http://localhost/negociosweb/Proyecto_pcbuildshonduras/index.php?page=Checkout_Accept"
            );

            $viewData["carretilla"] = $carretilla;

            foreach ($viewData["carretilla"] as $producto) {
                $PayPalOrder->addItem(
                    $producto["nombre_producto"],
                    $producto["descripcion"],
                    $producto["id_producto"],
                    $producto["crrprc"],
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

                foreach ($response->links as $link) {
                    if ($link->rel == "approve") {
                        Site::redirectTo($link->href);
                    }
                }
            } else {
                error_log("Error: respuesta inesperada de PayPal");
                error_log(print_r($response, true));
                Site::redirectTo("index.php?page=Checkout_Error");
            }
            die();
        }

        // ✅ Preparar datos para la vista
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