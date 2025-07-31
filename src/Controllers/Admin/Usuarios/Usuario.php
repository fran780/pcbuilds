<?php
namespace Controllers\Admin\Usuarios;

use Controllers\PrivateController;
use Dao\Admin\Usuarios as UsuariosDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Admin_Usuarios_Usuarios";

class Usuario extends PrivateController
{
    private array $viewData;
    private array $modes;
    private array $userTypes;
    private array $userStates;

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "mode" => "",
            "usercod" => 0,
            "useremail" => "",
            "username" => "",
            "userpswd" => "",
            "userfching" => date("Y-m-d\TH:i"),
            "userest" => "ACT",
            "usertipo" => "NOR",
            "FormTitle" => "",
            "readonly" => "",
            "showCommitBtn" => true,
            "errors" => []
        ];

        $this->modes = [
            "INS" => "Nuevo Usuario",
            "UPD" => "Editar Usuario: %s",
            "DEL" => "Eliminar Usuario: %s",
            "DSP" => "Detalle del Usuario: %s"
        ];

        $this->userTypes = ["Administrador", "Cliente"];
        $this->userStates = ["ACT", "INA", "BLQ"];
    }

    public function run(): void
    {
        $this->getQueryParamsData();
        if ($this->viewData["mode"] !== "INS") {
            $this->getDataFromDB();
        }
        if ($this->isPostBack()) {
            $this->getBodyData();
            if ($this->viewData["mode"] === "DEL" || $this->validateData()) {
                $this->processData();
            }
        }
        $this->prepareViewData();
        Renderer::render("admin/usuarios/usuario", $this->viewData);
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
            $this->throwError("Modo no especificado", "Falta mode");
        }

        $this->viewData["mode"] = $_GET["mode"];
        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError("Modo inválido", "Valor desconocido: " . $this->viewData["mode"]);
        }

        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["usercod"]) || !is_numeric($_GET["usercod"])) {
                $this->throwError("Código de usuario inválido", "usercod ausente/no numérico");
            }
            $this->viewData["usercod"] = intval($_GET["usercod"]);
        }
    }

    private function getDataFromDB()
    {
        $usuario = UsuariosDAO::getUsuarioById($this->viewData["usercod"]);
        if (!$usuario) {
            $this->throwError("Usuario no encontrado", "ID " . $this->viewData["usercod"] . " no existe");
        }
        foreach ($usuario as $key => $value) {
            $this->viewData[$key] = $value;
        }
    }

    private function getBodyData()
    {
        $fields = [
            "usercod", "useremail", "username", "userpswd", "userfching",
            "userest", "usertipo", "xsrtoken"
        ];
        foreach ($fields as $field) {
            if (!isset($_POST[$field])) {
                $this->innerError($field, "Campo requerido");
                $_POST[$field] = "";
            }
        }

        if (intval($_POST["usercod"]) !== $this->viewData["usercod"] && $this->viewData["mode"] !== "INS") {
            $this->throwError("ID de usuario no coincide");
        }

        if ($_POST["xsrtoken"] !== $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError("Token inválido");
        }

        $this->viewData["useremail"] = $_POST["useremail"];
        $this->viewData["username"] = $_POST["username"];
        $this->viewData["userpswd"] = $_POST["userpswd"];
        $this->viewData["userfching"] = $_POST["userfching"];
        $this->viewData["userest"] = $_POST["userest"];
        $this->viewData["usertipo"] = $_POST["usertipo"];
    }

    private function validateData(): bool
    {
        $v = &$this->viewData;
        if (!Validators::IsValidEmail($v["useremail"])) $this->innerError("useremail", "Email requerido");
        if (Validators::IsEmpty($v["username"])) $this->innerError("username", "Nombre requerido");

        if ($v["mode"] === "INS") {
    
    if (Validators::IsEmpty($v["userpswd"])) {
        $this->innerError("userpswd", "Contraseña requerida");
    } elseif (!Validators::IsValidPassword($v["userpswd"])) {
        $this->innerError("userpswd", "Contraseña débil: debe tener mayúscula, minúscula, número y carácter especial");
    }
}

        if (!in_array($v["userest"], $this->userStates)) $this->innerError("userest", "Estado inválido");
        if (!in_array($v["usertipo"], $this->userTypes)) $this->innerError("usertipo", "Tipo de usuario inválido");

        return count($v["errors"]) === 0;
    }

    private function processData()
    {
        $v = $this->viewData;
        switch ($v["mode"]) {
            case "INS":
                if (UsuariosDAO::insertUser(
                    $v["useremail"],
                    $v["username"],
                    $v["userpswd"],
                    $v["userest"],
                    $v["usertipo"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Usuario creado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo crear el usuario");
                }
                break;

           case "UPD":
                $userpswdParam = !empty($v["userpswd"])
                ? password_hash($v["userpswd"], PASSWORD_DEFAULT)
                : null;
                if (UsuariosDAO::updateUsuario(
                    $v["usercod"],
                    $v["useremail"],
                    $v["username"],
                    $userpswdParam,
                    $v["userest"],
                    $v["usertipo"]
                ))

                {
                    Site::redirectToWithMsg(LIST_URL, "Usuario actualizado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo actualizar el usuario");
                }
                break;

            case "DEL":
                if (UsuariosDAO::deleteUsuario($v["usercod"]) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Usuario eliminado exitosamente");
                } else {
                    $this->innerError("global", "No se pudo eliminar el usuario");
                }
                break;
        }
    }

    private function prepareViewData()
    {
        $this->viewData["FormTitle"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["username"] ?? ""
        );

        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $arr) {
                $this->viewData[$scope . "_error"] = implode(", ", $arr);
            }
        }

        if (in_array($this->viewData["mode"], ["DSP", "DEL"])) {
            $this->viewData["readonly"] = "readonly";
        }

        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["showCommitBtn"] = false;
        }

        $this->viewData["xsrtoken"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsrtoken"] = $this->viewData["xsrtoken"];
    }
}