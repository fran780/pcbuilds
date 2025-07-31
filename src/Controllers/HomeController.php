<?php

namespace Controllers;

use \Dao\Productos\ProductosDAO;
use \Views\Renderer as Renderer;
use \Utilities\Site as Site;

class HomeController extends PublicController
{
  public function run(): void
  {
     Site::addLink("public/css/paginas/index.css");
        $viewData = [];
        $viewData["getBestProducts"] = ProductosDAO::getBestProducts();
        Renderer::render("index", $viewData);
  }
}
?>