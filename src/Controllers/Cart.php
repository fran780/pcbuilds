<?php
namespace Controllers;

use Dao\Cart\CartDAO;
use Dao\Productos\ProductosDAO;
use Utilities\Site;
use Utilities\Security;

class Cart extends PublicController
{
   public function run(): void
   {
        Site::addLink("public/css/product.css");

        if ($this->isPostBack()) {
            if (Security::isLogged()) {
                $usercod = Security::getUserId();
                //Traer el id del producto que fue pasado por boton anterior
                $productId = $this->getPostParam("id_producto");
                $producto = CartDAO::getProducto($productId);
                if ($producto["stock"] - 1 >= 0) {
                    Cart::addToAuthCart(
                        intval($_POST["id_producto"]),
                        $usercod,
                        1,
                        $producto["precio"]
                    );
                }
            }else {
                $cartAnonCod = CartFns::getAnnonCartCode();
                //Traer el id del producto que fue pasado por boton anterior
                $productId = $this->getPostParam("id_producto");
                $producto = CartDAO::getProducto($productId);
                if ($producto["stock"] - 1 >= 0) {
                    Cart::addToAnonCart(
                        intval($_POST["id_producto"]),
                        $cartAnonCod,
                        1,
                        $producto["precio"]
                    );
                }
            }
            $this->getCartCounter();
        }
        $viewData = [
            "producto" => $producto
        ];
        \Views\Renderer::render("paginas/cart", $viewData);

    }

}