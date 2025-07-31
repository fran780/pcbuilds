<?php

namespace Controllers\Admin\Products;

use Controllers\PrivateController;
use Dao\Admin\Products as ProductsDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_Products_Products";

class Product extends PrivateController
{
    private array $viewData;
    private array $modes;
    private array $statusValues;
    private array $product = [];

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "id_producto" => 0,
            "nombre_producto" => "",
            "descripcion" => "",
            "precio" => 0.0,
            "stock" => 0,
            "imagen" => "",
            "id_categoria" => 0,
            "id_marca" => 0,
            "id_estado" => 1,
            "FormTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "errors" => [],
            "productStatus_act" => "",
            "productStatus_ina" => ""
        ];
        $this->modes = [
            "INS" => "Nuevo Producto",
            "UPD" => "Editar Producto: %s",
            "DEL" => "Eliminar Producto: %s",
            "DSP" => "Detalle del Producto: %s"
        ];
        $this->statusValues = [1, 2]; // ACT = 1, INA = 2
    }

    public function run(): void
    {
        $this->getQueryParamsData();
        if ($this->viewData["mode"] !== "INS") {
            $this->getDataFromDB();
        }
        if ($this->isPostBack()) {
            $this->getBodyData();
            if ($this->viewData["mode"] === "DEL") {
                $this->processData(); // no validar
            } elseif ($this->validateData()) {
                $this->processData();
            }
}
        $this->prepareViewData();
        Renderer::render("admin/products/product", $this->viewData);
    }

    private function throwError(string $message, string $logMessage = "")
    {
        if (!empty($logMessage)) {
            error_log(sprintf("%s - %s", $this->name, $logMessage));
        }
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function innerError(string $scope, string $message)
    {
        if (!isset($this->viewData["errors"][$scope])) {
            $this->viewData["errors"][$scope] = [$message];
        } else {
            $this->viewData["errors"][$scope][] = $message;
        }
    }

    private function getQueryParamsData()
    {
        if (!isset($_GET["mode"])) {
            $this->throwError("Modo de ejecución no especificado", "Falta el parámetro mode");
        }

        $this->viewData["mode"] = $_GET["mode"];
        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError("Modo inválido", "Valor desconocido en el parámetro mode: " . $this->viewData["mode"]);
        }

        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["id_producto"]) || !is_numeric($_GET["id_producto"])) {
                $this->throwError("ID de producto inválido", "ID ausente o no numérico");
            }
            $this->viewData["id_producto"] = intval($_GET["id_producto"]);
        }
    }

    private function getDataFromDB()
    {
        $tmpProducto = ProductsDAO::getProductById($this->viewData["id_producto"]);
        if (!$tmpProducto) {
            $this->throwError("Producto no encontrado", "ID " . $this->viewData["id_producto"] . " no existe");
        }

        foreach ($tmpProducto as $key => $value) {
            $this->viewData[$key] = $value;
        }
    }

    private function getBodyData()
    {
    $fields = ["id_producto", "nombre_producto", "descripcion", "precio", "stock", "imagen", "id_categoria", "id_marca", "id_estado", "xsrtoken"];

    // Agregar errores por campo si falta alguno
    foreach ($fields as $field) {
        if (!isset($_POST[$field])) {
            $this->innerError($field, "Campo requerido");
            $_POST[$field] = ""; // Para evitar undefined cuando los usamos abajo
        }
    }

    // Solo hacer redirect si el token no cuadra o el ID no coincide
    if (
        isset($_POST["id_producto"]) &&
        intval($_POST["id_producto"]) !== $this->viewData["id_producto"]
    ) {
        $this->throwError("ID de producto no coincide", "Esperado: " . $this->viewData["id_producto"] . " recibido: " . $_POST["id_producto"]);
    }

    if (
        isset($_POST["xsrtoken"]) &&
        $_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]
    ) {
        $this->throwError("Token inválido", "Esperado: " . $_SESSION[$this->name . "-xsrtoken"] . " recibido: " . $_POST["xsrtoken"]);
    }

    // Guardamos los datos en viewData para validación
    $this->viewData["nombre_producto"] = $_POST["nombre_producto"];
    $this->viewData["descripcion"] = $_POST["descripcion"];
    $this->viewData["precio"] = floatval($_POST["precio"]);
    $this->viewData["stock"] = intval($_POST["stock"]);
    $this->viewData["imagen"] = $_POST["imagen"];
    $this->viewData["id_categoria"] = intval($_POST["id_categoria"]);
    $this->viewData["id_marca"] = intval($_POST["id_marca"]);
    $this->viewData["id_estado"] = intval($_POST["id_estado"]);
}

    private function validateData(): bool
    {
        $v = &$this->viewData;

        if (Validators::IsEmpty($v["nombre_producto"])) $this->innerError("nombre_producto", "Nombre requerido");
        if (Validators::IsEmpty($v["descripcion"])) $this->innerError("descripcion", "Descripción requerida");
        if ($v["precio"] <= 0) $this->innerError("precio", "Precio debe ser mayor a cero");
       if ($v["stock"] <= 0) { $this->innerError("stock", "El Stock no puede ser menos de cero y si es cero considere poner el producto en estado inactivo.");}
        if (Validators::IsEmpty($v["imagen"])) $this->innerError("imagen", "Imagen requerida");
        if ($v["id_categoria"] <= 0) $this->innerError("id_categoria", "Seleccione una categoría válida");
        if ($v["id_marca"] <= 0) $this->innerError("id_marca", "Seleccione una marca válida");
        if (!in_array($v["id_estado"], $this->statusValues)) $this->innerError("id_estado", "Estado inválido");

        return count($this->viewData["errors"]) === 0;
    }

    private function processData()
    {
        $v = $this->viewData;
        switch ($v["mode"]) {
            case "INS":
                if (
                ProductsDAO::insertProduct(
                    $v["nombre_producto"],
                    $v["descripcion"],
                    $v["precio"],
                    $v["stock"],
                    $v["imagen"],
                    $v["id_categoria"],
                    $v["id_marca"],
                    $v["id_estado"]
                ) > 0
            ) {
                Site::redirectToWithMsg(LIST_URL, "Producto creado exitosamente");
            } else {
                $this->innerError("global", "No se pudo crear el producto");
            }
                break;
            case "UPD":
                if (
                    ProductsDAO::updateProduct(
                        $v["id_producto"],
                        $v["nombre_producto"],
                        $v["descripcion"],
                        $v["precio"],
                        $v["stock"],
                        $v["imagen"],
                        $v["id_categoria"],
                        $v["id_marca"],
                        $v["id_estado"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Producto actualizado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo actualizar el producto");
                }
                break;
            case "DEL":
                if (ProductsDAO::deleteProduct($v["id_producto"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Producto eliminado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo eliminar el producto");
                }
                break;
        }
    }


  private function prepareViewData()
{
    $this->viewData["FormTitle"] = sprintf(
        $this->modes[$this->viewData["mode"]],
        $this->viewData["nombre_producto"] ?? "");

    if (count($this->viewData["errors"]) > 0) {
        foreach ($this->viewData["errors"] as $scope => $arr) {
            $this->viewData[$scope . "_error"] = implode(", ", $arr);
        }
    }
    // Configurar modo y campos de solo lectura
    if ($this->viewData["mode"] === "DSP") {
        $this->viewData["showCommitBtn"] = false;
        $this->viewData["readonly"] = "readonly";
    }

    if ($this->viewData["mode"] === "DEL") {
        $this->viewData["readonly"] = "readonly";
    }

    // Categorías PARTE DE CODIGO INVESTIGADA
    $categorias = ProductsDAO::getCategorias();
    foreach ($categorias as &$cat) {
        $cat["selected"] = ($cat["id_categoria"] == $this->viewData["id_categoria"]) ? "selected" : "";
    }
    $this->viewData["categoria_producto_list"] = $categorias;

    // Marcas PARTE DE CODIGO INVESTIGADA
    $marcas = ProductsDAO::getMarcas();
    foreach ($marcas as &$marca) {
        $marca["selected"] = ($marca["id_marca"] == $this->viewData["id_marca"]) ? "selected" : "";
    }
    $this->viewData["marca_producto_list"] = $marcas;

    // Estado del producto PARTE DE CODIGO INVETIGADA
    $this->viewData["productStatus_act"] = ($this->viewData["id_estado"] == 1) ? "selected" : "";
    $this->viewData["productStatus_ina"] = ($this->viewData["id_estado"] == 2) ? "selected" : "";

    $this->viewData["timestamp"] = time();
        $this->viewData["xsrtoken"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsrtoken"] = $this->viewData["xsrtoken"];
}
}