<?php
// ---------- Configuración conexión MySQL ----------
$host = "localhost";
$db = "pcbuilds";
$user = "root";
$pass = "";

$tabla = $argv[1] ?? null;
if (!$tabla) die("Uso: php generarWW.php nombre_tabla\n");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Error conexión BD: " . $e->getMessage() . "\n");
}

$cols = $pdo->query("DESC `$tabla`")->fetchAll(PDO::FETCH_ASSOC);
if (!$cols) die("Tabla $tabla no encontrada.\n");

// ---------- Funciones Globales ----------
function toPascalCase(string $text): string {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $text)));
}
function alias(string $field): string {
    return ucwords(str_replace('_', ' ', $field));
}
function isDateType(string $type): bool {
    return (bool) preg_match('/^(date|datetime|timestamp)/i', $type);
}

function isNumberType(string $type): bool {
    $patterns = ['int', 'decimal', 'float', 'double', 'numeric'];
    foreach ($patterns as $p) {
        if (preg_match("/$p/i", $type)) {
            return true;
        }
    }
    return false;
}

$tablaPascal = toPascalCase($tabla);

$dirs = [
    "src/Controllers/Admin/$tablaPascal",
    "src/Dao/Admin/$tablaPascal",
    "src/Views/templates/Admin/$tablaPascal"
];
foreach ($dirs as $d) if (!is_dir($d)) mkdir($d, 0777, true);

// ---------- Detectar llave primaria (PK) ----------
$pk = null;
foreach ($cols as $c) if ($c['Key'] === 'PRI') { $pk = $c['Field']; break; }
if (!$pk) die("No se encontró clave primaria en $tabla\n");

$insertFields = [];
foreach ($cols as $col) {
    if ($col['Extra'] !== 'auto_increment') {
        $insertFields[] = $col;
    }
}

$updateFields = [];
foreach ($cols as $col) {
    if ($col['Key'] !== 'PRI') {
        $updateFields[] = $col;
    }
}

// ---------- Modelo DAO ----------
$daoClass = $tablaPascal;
$daoNamespace = "Dao\\$tablaPascal";

$insertParams = implode(', ', array_map(fn($c) => '$' . $c['Field'], $insertFields));
$insertPlaceholders = implode(', ', array_map(fn($c) => ':' . $c['Field'], $insertFields));
$insertFieldsList = implode(', ', array_column($insertFields, 'Field'));

$insertBindings = [];
foreach ($insertFields as $f) {
    $insertBindings[] = "'{$f['Field']}' => \${$f['Field']}";
}
$insertBindingsStr = implode(', ', $insertBindings);

$updateParams = implode(', ', array_map(fn($c) => '$' . $c['Field'], $updateFields));
$updateSet = implode(', ', array_map(fn($c) => "{$c['Field']} = :{$c['Field']}", $updateFields));
$updateBindings = [];
foreach ($updateFields as $f) {
    $updateBindings[] = "'{$f['Field']}' => \${$f['Field']}";
}
$updateBindingsStr = implode(', ', $updateBindings);

$daoContent = <<<PHP
<?php
namespace Dao\Admin;

use Dao\Table;

class $daoClass extends Table {

  public static function get${daoClass}s(
    string \$partialName = "",
    string \$orderBy = "",
    bool \$orderDescending = false,
    int \$page = 0,
    int \$itemsPerPage = 10
  ) {
    \$sqlstr = "SELECT * FROM `$tabla`";
    \$sqlstrCount = "SELECT COUNT(*) as count FROM `$tabla`";
    \$conditions = [];
    \$params = [];

    if (\$partialName !== "") {
      \$conditions[] = "`nombre_$tabla` LIKE :partialName";
      \$params["partialName"] = "%" . \$partialName . "%";
    }

    if (count(\$conditions) > 0) {
      \$where = " WHERE " . implode(" AND ", \$conditions);
      \$sqlstr .= \$where;
      \$sqlstrCount .= \$where;
    }

    \$validOrderBy = ["$pk"];
    if (\$orderBy !== "" && in_array(\$orderBy, \$validOrderBy)) {
      \$sqlstr .= " ORDER BY " . \$orderBy;
      if (\$orderDescending) {
        \$sqlstr .= " DESC";
      }
    }

    \$totalRecords = self::obtenerUnRegistro(\$sqlstrCount, \$params)["count"];
    \$pagesCount = ceil(\$totalRecords / \$itemsPerPage);
    if (\$page > \$pagesCount - 1) {
      \$page = max(0, \$pagesCount - 1);
    }

    \$sqlstr .= " LIMIT " . (\$page * \$itemsPerPage) . ", " . \$itemsPerPage;
    \$records = self::obtenerRegistros(\$sqlstr, \$params);

    return [
      "${tabla}s" => \$records,
      "total" => \$totalRecords,
      "page" => \$page,
      "itemsPerPage" => \$itemsPerPage
    ];
  }

  public static function get${daoClass}ById(int \$id) {
    return self::obtenerUnRegistro("SELECT * FROM `$tabla` WHERE `$pk` = :id", ["id" => \$id]);
  }

  public static function insert${daoClass}($insertParams) {
    \$sql = "INSERT INTO `$tabla` ($insertFieldsList) VALUES ($insertPlaceholders)";
    return self::executeNonQuery(\$sql, [$insertBindingsStr]);
  }

  public static function update${daoClass}(int \$id, $updateParams) {
    \$sql = "UPDATE `$tabla` SET $updateSet WHERE `$pk` = :id";
    return self::executeNonQuery(\$sql, array_merge([$updateBindingsStr], ["id" => \$id]));
  }

  public static function delete${daoClass}(int \$id) {
    return self::executeNonQuery("DELETE FROM `$tabla` WHERE `$pk` = :id", ["id" => \$id]);
  }

}

PHP;

file_put_contents("src/Dao/Admin/$tablaPascal.php", $daoContent);
echo "Modelo generado: src/Dao/Admin/$tablaPascal.php\n";

// ---------- Controlador Lista ----------
$listController = <<<PHP
<?php
namespace Controllers\\$tablaPascal;

use Controllers\PrivateController;
use Dao\\Admin\\$tablaPascal as Dao;
use Views\Renderer;

class Lista extends PrivateController
{
    private array \$data = [];

    public function run(): void
    {
        \$this->data['records'] = Dao::getAll();
        Renderer::render('$tablaPascal/list', \$this->data);
    }
}

class_alias(Lista::class, 'Controllers\\\\$tablaPascal\\\\List');

PHP;


file_put_contents("src/Controllers/Admin/$tablaPascal/RolesUsuario.php", $listController);
echo "Controlador lista generado: src/Controllers/Admin/$tablaPascal/RolesUsuario.php\n";

// ---------- Controlador Form ----------
$fieldsInitArr = [];
$validationCode = [];

foreach ($cols as $col) {
    $field = $col['Field'];
    $type = strtolower($col['Type']);
    $aliasName = alias($field);
    if ($field !== $pk) {
        $fieldsInitArr[] = "            '$field' => '',";

        $validationLines = [];
        $validationLines[] = "            if (empty(trim(\$this->viewData['$field'] ?? ''))) {";
        $validationLines[] = "                \$this->viewData['errors']['$field'][] = 'El campo $aliasName es obligatorio.';";
        $validationLines[] = "            } else {";

        if (isDateType($type)) {
            $validationLines[] = "                if (!preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', trim(\$this->viewData['$field'])) || !strtotime(\$this->viewData['$field'])) {";
            $validationLines[] = "                    \$this->viewData['errors']['$field'][] = 'El campo $aliasName debe tener formato fecha válido YYYY-MM-DD o YYYY-MM-DD HH:MM:SS.';";
            $validationLines[] = "                }";
        } elseif (isNumberType($type)) {
            $validationLines[] = "                if (!is_numeric(\$this->viewData['$field'])) {";
            $validationLines[] = "                    \$this->viewData['errors']['$field'][] = 'El campo $aliasName debe ser un número válido.';";
            $validationLines[] = "                }";
        } else {
            $validationLines[] = "                if (preg_match('/^\d+$/', trim(\$this->viewData['$field']))) {";
            $validationLines[] = "                    \$this->viewData['errors']['$field'][] = 'El campo $aliasName no debe contener solo números.';";
            $validationLines[] = "                }";
        }

        $validationLines[] = "            }";

        $validationCode[] = implode("\n", $validationLines);
    }
}

$fieldsInitStr = implode("\n", $fieldsInitArr);

$insertArgCall = implode(', ', array_map(fn($c) => "\$this->viewData['{$c['Field']}']", $insertFields));
$updateArgCall = implode(', ', array_map(fn($c) => "\$this->viewData['{$c['Field']}']", $updateFields));

$formController = <<<PHP
<?php
namespace Controllers\\$tablaPascal;

use Controllers\PublicController;
use Dao\\$tablaPascal\\$tablaPascal as Dao;
use Views\Renderer;
use Utilities\Site;

const LIST_URL = 'index.php?page={$tablaPascal}-List';

class Form extends PublicController
{
    private array \$viewData;
    private array \$modes;
    private string \$pk = '$pk';
    protected \$name = 'Form';

    public function __construct()
    {
        \$this->viewData = [
            'mode' => '',
            'id' => 0,
$fieldsInitStr
            'xsrtoken' => '',
            'errors' => []
        ];

        \$this->modes = [
            'INS' => 'Nuevo',
            'UPD' => 'Editar',
            'DEL' => 'Eliminar',
            'DSP' => 'Detalle'
        ];
    }

    public function run(): void
    {
        \$mode = \$_GET['mode'] ?? 'INS';
        \$id = \$_GET[\$this->pk] ?? 0;

        \$this->viewData['mode'] = \$mode;
        \$this->viewData['id'] = \$id;
        \$this->viewData['modeDsc'] = \$this->modes[\$mode] ?? '';
        \$this->viewData['mode_DSP'] = \$mode === 'DSP';

        if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset(\$_POST['xsrtoken']) || \$_POST['xsrtoken'] !== (\$_SESSION[\$this->name . '-xsrtoken'] ?? '')) {
                Site::redirectToWithMsg(LIST_URL, 'Token de seguridad inválido.');
                return;
            }

            foreach (\$_POST as \$k => \$v) {
                if (array_key_exists(\$k, \$this->viewData)) {
                    \$this->viewData[\$k] = \$v;
                }
            }

            \$this->viewData['errors'] = [];

PHP;

foreach ($validationCode as $codeBlock) {
    $formController .= $codeBlock . "\n\n";
}

$formController .= <<<PHP
            if (count(\$this->viewData['errors']) > 0) {
                foreach (\$this->viewData['errors'] as \$field => \$errs) {
                    \$this->viewData['errors_' . \$field] = \$errs;
                }
                Renderer::render('{$tablaPascal}/form', \$this->viewData);
                return;
            }

            switch (\$mode) {
                case 'INS':
                    Dao::insert($insertArgCall);
                    break;
                case 'UPD':
                    Dao::update(\$id, $updateArgCall);
                    break;
                case 'DEL':
                    Dao::delete(\$id);
                    break;
            }

            Site::redirectTo(LIST_URL);
        }

        if (in_array(\$mode, ['UPD', 'DEL', 'DSP']) && \$id) {
            \$record = Dao::getById(\$id);
            if (\$record) {
                foreach (\$record as \$k => \$v) {
                    \$this->viewData[\$k] = \$v;
                }
            }
        }

        \$this->viewData['readonly'] = in_array(\$mode, ['DEL', 'DSP']) ? 'readonly' : '';
        \$this->viewData['xsrtoken'] = hash('sha256', json_encode(\$this->viewData) . time());
        \$_SESSION[\$this->name . '-xsrtoken'] = \$this->viewData['xsrtoken'];

        Renderer::render('{$tablaPascal}/form', \$this->viewData);
    }
}

PHP;

file_put_contents("src/Controllers/Admin/$tablaPascal/RolUsuario.php", $formController);
echo "Controlador formulario generado: src/Controllers/Admin/$tablaPascal/RolUsuario.php\n";

// ---------- Vista Lista ----------
$listView = <<<HTML
<h1 style="text-align: left;">Listado de $tablaPascal</h1>
<div class="WWList">
<table>
<thead>
<tr>
HTML;

foreach ($cols as $c) {
    $listView .= "    <th>" . alias($c['Field']) . "</th>\n";
}
$listView .= "    <th><a href=\"index.php?page={$tablaPascal}-Form&mode=INS\" style=\"text-decoration:none; color:inherit;\">Nuevo</a></th>\n</tr>\n</thead>\n<tbody>\n{{foreach records}}\n<tr>\n";

foreach ($cols as $c) {
    $field = $c['Field'];
    $listView .= "    <td>{{{$field}}}</td>\n";
}

$listView .= <<<HTML
    <td>
        <a href="index.php?page={$tablaPascal}-Form&mode=UPD&$pk={{{$pk}}}">Editar</a> |
        <a href="index.php?page={$tablaPascal}-Form&mode=DSP&$pk={{{$pk}}}">Ver</a> |
        <a href="index.php?page={$tablaPascal}-Form&mode=DEL&$pk={{{$pk}}}">Eliminar</a>
    </td>
</tr>
{{endfor records}}
</tbody>
</table>
</div>
HTML;

file_put_contents("src/Views/templates/Admin/$tablaPascal/rolesusuarios.view.tpl", $listView);
echo "Vista lista generada: src/Views/templates/Admin/$tablaPascal/rolesusuarios
.view.tpl\n";

// ---------- Vista Form ----------
$formView = <<<HTML
<h2 class="center">{{modeDsc}}</h2>
<form method="POST" action="index.php?page={$tablaPascal}-Form&mode={{mode}}&$pk={{id}}" style="max-width:400px; margin:0 auto;" class="width-full">
<input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
HTML;

foreach ($cols as $c) {
    $f = $c['Field'];
    $aliasName = alias($f);
    $readonlyAttr = $c['Key'] === 'PRI' ? 'readonly' : '{{readonly}}';

    $formView .= <<<HTML

<div class="form-group py-3">
    <label for="$f">$aliasName:</label>
    <input type="text" id="$f" name="$f" value="{{{$f}}}" class="width-full" $readonlyAttr />
    {{if errors_$f}}
        {{foreach errors_$f}}
            <div class="error">{{this}}</div>
        {{endfor errors_$f}}
    {{endif errors_$f}}
</div>
HTML;
}

$formView .= <<<HTML

<div class="py-3" style="display:flex; justify-content:center; gap:1rem;">
    {{if mode_DSP}}
        <a href="index.php?page={$tablaPascal}-List" class="secondary" style="flex:1; text-align:center; background:#6c757d; color:#fff; padding:0.75rem 1.5rem; font-weight:700; text-decoration:none;">Regresar</a>
    {{endif mode_DSP}}
    {{ifnot mode_DSP}}
        <button type="submit" class="primary" style="flex:1;">Confirmar</button>
        <a href="index.php?page={$tablaPascal}-List" class="secondary" style="flex:1; text-align:center; background:#6c757d; color:#fff; padding:0.75rem 1.5rem; font-weight:700; text-decoration:none;">Cancelar</a>
    {{endifnot mode_DSP}}
</div>
</form>
HTML;

file_put_contents("src/Views/templates/Admin/$tablaPascal/rolesusuario.view.tpl", $formView);
echo "Vista formulario generada: src/Views/templates/Admin/$tablaPascal/rolesusuario.view.tpl\n";
