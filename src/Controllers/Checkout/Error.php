<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
class Error extends PublicController
{
    public function run(): void
    {
        $viewData = array(
            "errorMessage" => "An error occurred during the checkout process. Please try again later."
        );

        // Render the error view
        \Views\Renderer::render("paypal/error", $viewData);
    }
}

?>
