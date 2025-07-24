<?php
namespace Controllers;
use \Utilities\Site;
use \Views\Renderer;
use \Dao\Productos\ProductosDAO;

class Tienda extends PublicController
{
    public function run() :void
    {
        Site::addLink("public/css/paginas/tienda.css");
        $viewData = [];
        $viewData["getAllProducts"] = ProductosDAO::getAllProducts();
        Renderer::render("paginas/tienda", $viewData);
    }
}
?>
