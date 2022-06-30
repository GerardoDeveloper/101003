
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$d = "hilos/101003/webviews/mis_fichadas";
include_once $_SERVER['DOCUMENT_ROOT'] . "/$d/model/formulario.model.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/$d/inc/functions.php";

class FormularioController
{
    private static $instancia;
    private $modelInstancia;
    private $formulario;

    public function __construct()
    {
        try {
            $this->modelInstancia = FormularioModel::getInstance();
        } catch (Exception $e) {
            $error = "Error!: " . $e->getMessage();

            die();
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;

            self::$instancia = new $miclase;
        }

        return self::$instancia;
    }

    public function getTiposLicencia()
    {
        $res = $this->modelInstancia->getTiposLicencia();
        $options = "";
        if (count($res) > 0) {
            foreach ($res as $key => $value) {
                $clave = $value["id"];
                $valor = $value["descripcion"];
                $options .= "<option value='$clave'>$valor</option>\n";
            }
        }
        return $options;
    }

    public function getDetallesLicencia($licencia)
    {
        $res = $this->modelInstancia->getDetallesLicencia($licencia);
        setLog($res[0], $res[1], false);
        return $res;
    }

    public function insertFormulario($identificador, $fecha_inicio, $origen)
    {
        $res = $this->modelInstancia->insertFormulario($identificador, $fecha_inicio, $origen);
        setLog($res[0], $res[1], false);
        return $res;
    }

    public function updateFormulario($identificador, $fecha_fin, $licencia)
    {
        $res = $this->modelInstancia->updateFormulario($identificador, $fecha_fin, $licencia);
        setLog($res[0], $res[1], false);
        return $res;
    }
}

if (isset($_POST["function"]) and !empty($_POST["function"])) {
    $function = $_POST["function"];
} elseif (isset($_GET["function"]) and !empty($_GET["function"])) {
    $function = $_GET["function"];
} else {
    $function = null;
}

if ($function != null) {
    switch (strtolower($function)) {
        case "updateformulario":
            echo updateFormulario();
            break;
        case "getdetalleslicencia":
            echo getDetallesLicencia();
            break;
    }
}

function getDetallesLicencia()
{
    if (isset($_POST["licencia"]) && !empty($_POST["licencia"])) {
        $licencia = $_POST["licencia"];
        $controller = FormularioController::getInstance();
        $r = $controller->getDetallesLicencia($licencia);
        return json_encode($r);
    } else {
        return false;
    }
}

function updateFormulario()
{
    $fecha = new DateTime();
    $timestamp = $fecha->getTimestamp();

    if (isset($_POST["licencia"]) and !empty($_POST["licencia"])) {
        $licencia = $_POST["licencia"];
        $userid = base64_decode($_POST["userid"]);
    } else if (isset($_GET["licencia"]) and !empty($_GET["licencia"])) {
        $licencia = $_GET["licencia"];
        $userid = base64_decode($_GET["userid"]);
    } else {
        return 0;
    }

    $fecha_fin = date("Y/m/d H:i:s");
    $controller = FormularioController::getInstance();
    $r = $controller->updateFormulario($userid, $fecha_fin, $licencia);

    if ($r[0] == 1) {
        if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
            $_SESSION["conid"] = $_POST["conid"];
        }
        setLog(200, $r[1], true);
    } else {
        setLog(0, $r[1]);
    }

    return $r[0];
}
?>