
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$d = "hilos/101003/webviews/otras_consultas_sobre_licencias";
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

    public function insertFormulario($identificador, $fecha_inicio, $origen)
    {
        $res = $this->modelInstancia->insertFormulario($identificador, $fecha_inicio, $origen);
        setLog($res[0], $res[1], false);
        return $res;
    }

    /**
     * actualiza los campos del formulario.
     *
     * @param [string] $identificador N° de identificación único que identifica al usuario.
     * @param [string] $fecha_fin Fecha en que se actualizaron los campos.
     * @param [integer] $idTipoConsulta Id del tipo de la consulta.
     * @param [string] $descripcion_consulta Descripción de la consulta.
     * @return array
     */
    public function updateFormulario($identificador, $fecha_fin, $descripcion_consulta)
    {
        $res = $this->modelInstancia->updateFormulario($identificador, $fecha_fin, $descripcion_consulta);
        setLog($res[0], $res[1]);
        return $res;
    }
}

session_start(); //Se inicia la session

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
    }
}

function updateFormulario()
{
    $fecha = new DateTime();
    $timestamp = $fecha->getTimestamp();

    if (isset($_POST["conid"]) and !empty($_POST["conid"]) and
        isset($_POST["descripcion_consulta"]) and !empty($_POST["descripcion_consulta"])) {
        $conid = base64_decode($_POST["conid"]);
        $descripcion_consulta = $_POST["descripcion_consulta"];
    } else if (isset($_GET["conid"]) and !empty($_GET["conid"]) and
        isset($_GET["descripcion_consulta"]) and !empty($_GET["descripcion_consulta"])) {
        $conid = base64_decode($_GET["conid"]);
        $descripcion_consulta = $_GET["descripcion_consulta"];
    } else {
        return 0;
    }

    $fecha_fin = date("Y/m/d H:i:s");
    $controller = FormularioController::getInstance();
    $result = $controller->updateFormulario($conid, $fecha_fin, $descripcion_consulta);

    if ($result[0] == 1) {
        if (isset($conid) && !empty($conid)) {
            $_SESSION["conid"] = $conid;
        }
        setLog(200, $result[1], true); // Envía la palabra al hilo.
    } else {
        setLog(0, $result[1]); // Escribe el mensaje de error en los logs.
    }

    return $result[0];
}
?>