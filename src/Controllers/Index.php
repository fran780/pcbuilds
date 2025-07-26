<?php
/**
 * PHP Version 7.2
 *
 * @category Public
 * @package  Controllers
 * @author   Orlando J Betancourth <orlando.betancourth@gmail.com>
 * @license  MIT http://
 * @version  CVS:1.0.0
 * @link     http://
 */


/**
 * Index Controller
 *
 * @category Public
 * @package  Controllers
 * @author   Orlando J Betancourth <orlando.betancourth@gmail.com>
 * @license  MIT http://
 * @link     http://
 */

namespace Controllers;
use \Utilities\Site;
use \Views\Renderer;
use \Dao\Productos\ProductosDAO;

class Index extends PublicController
{
    public function run() :void
    {
        Site::addLink("public/css/paginas/index.css");
        $viewData = [];
        $viewData["getBestProducts"] = ProductosDAO::getBestProducts();
        Renderer::render("index", $viewData);
    }
}
?>
