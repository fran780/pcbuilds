<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
class Error extends PublicController
{
    public function run(): void
    {
        //Vemos si hay una orden en la sesión
        if (isset($_SESSION["orderid"])) {
            $orderId = $_SESSION["orderid"];
            
            //Eliminamos los items de la Orden
            \Dao\Orders\Orders::deleteOrder($orderId);

            //Eliminamos la orden principal
            \Dao\Orders\OrderItems::deleteByOrderId($orderId);
        } 
        $viewData = array(
            "errorMessage" => "An error occurred during the checkout process. Please try again later.",
            "orderId" => $orderId
        );

        // Render the error view
        \Views\Renderer::render("paypal/error", $viewData);
    }
}

?>
