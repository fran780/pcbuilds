<?php
namespace Controllers;
use \Utilities\Site;
use \Views\Renderer;
use \Dao\Contactanos\ContactanosDAO;

class Contactanos extends PublicController
{
    public function run() :void
    {
        Site::addLink("public/css/paginas/contactanos.css");
        $viewData = [];
        Renderer::render("paginas/contactanos", $viewData);
    }
}
?>
