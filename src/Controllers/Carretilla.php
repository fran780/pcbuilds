<?php

namespace Controllers;

use Controllers\PublicController;
use Dao\Cart\CartDAO as Cart;
use Utilities\Security;
use Utilities\Cart\CartFns;
use Utilities\Site;

class Carretilla extends PublicController
{
    public function run(): void
    {
        Site::addLink("public/css/paginas/carretilla.css");
        $viewData = [];

        $userIsLogged = Security::isLogged();
        $userId = $userIsLogged ? Security::getUserId() : CartFns::getAnnonCartCode();

        // Obtener carretilla según tipo de usuario
        $carretilla = $userIsLogged
            ? Cart::getAuthCart($userId)
            : Cart::getAnonCart($userId);

        if ($this->isPostBack()) {
            if (isset($_POST["removeOne"]) || isset($_POST["addOne"])) {
                $productId = intval($_POST["productId"]);
                $productoDisp = Cart::getProductoDisponible($productId);
                $amount = isset($_POST["removeOne"]) ? -1 : 1;

                if ($amount === 1) {
                    if ($productoDisp["stock"] - $amount >= 0) {
                        if ($userIsLogged) {
                            Cart::addToAuthCart(
                                $productId,
                                $userId,
                                $amount,
                                $productoDisp["precio"]
                            );
                        } else {
                            Cart::addToAnonCart(
                                $productId,
                                $userId,
                                $amount,
                                $productoDisp["precio"]
                            );
                        }
                    }
                } else {
                    // Siempre que se reste, simplemente llamamos a addToCart con -1
                    if ($userIsLogged) {
                        Cart::addToAuthCart(
                            $productId,
                            $userId,
                            $amount,
                            $productoDisp["precio"]
                        );
                    } else {
                        Cart::addToAnonCart(
                            $productId,
                            $userId,
                            $amount,
                            $productoDisp["precio"]
                        );
                    }
                }

                // Refrescar carretilla
                $carretilla = $userIsLogged
                    ? Cart::getAuthCart($userId)
                    : Cart::getAnonCart($userId);
                $this->getCartCounter();
                Site::redirectTo("index.php?page=Carretilla");
            }
        }

        // Preparar datos para la vista
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

        $viewData["botonTexto"] = ($total > 0)
            ? "Vamos al Pago"
            : "No tienes productos en tu carretilla, agrega algunos!";
        $viewData["botonUrl"] = ($total > 0)
            ? "index.php?page=Checkout_Checkout"
            : "index.php?page=Index";
        $viewData["botonIcono"] = ($total > 0)
            ? "shopping-cart"
            : "store";

        \Views\Renderer::render("paginas/carretilla", $viewData);
    }
}