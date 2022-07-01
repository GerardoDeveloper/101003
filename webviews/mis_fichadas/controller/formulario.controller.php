
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include_once $_SERVER['DOCUMENT_ROOT'] . "/hilos/101003/webviews/mis_fichadas/model/formulario.model.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/hilos/101003/webviews/mis_fichadas/inc/functions.php";

class FormularioController
{
    /**
     * Propiedades de clase.
     */
    private static $instancia;
    private $modelInstancia;

    /**
     * Constructor de la clase.
     */
    private function __construct()
    {
        try {
            $this->modelInstancia = FormularioModel::getInstance();
        } catch (Exception $e) {
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/error.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            die();
        }
    }

    /**
     * Obtiene la instancia de si misma de la clase utilizando el patrón singleton.
     *
     * @return __CLASS__
     */
    public static function getInstance()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;

            self::$instancia = new $miclase;
        }

        return self::$instancia;
    }

    /**
     * Inserta los registros por defecto del formulario.
     *
     * @param string $identificador N° que identifica al usuario.
     * @param datetime $fecha_inicio Fecha en la que se abrio el formulario en el frontend.
     * @param string $origen El origen desde el cual fue abierto.
     * @return array
     */
    public function insertFormulario($identificador, $fecha_inicio, $origen)
    {
        $res = $this->modelInstancia->insertFormulario($identificador, $fecha_inicio, $origen);
        setLog($res[0], $res[1], false);
        return $res;
    }

    /**
     * Actualiza los datos del formulario.
     *
     * @param string $identificador N° que identifica al usuario.
     * @param datetime $fecha_fin Fecha en la que se termina de actualizar los datos restantes del formulario.
     * @param string $idMisFichadas El origen desde el cual fue abierto.
     * @return array
     */
    public function updateFormulario($identificador, $fecha_fin, $idMisFichadas)
    {
        $res = $this->modelInstancia->updateFormulario($identificador, $fecha_fin, $idMisFichadas);
        setLog($res[0], $res[1], false);
        return $res;
    }

    /**
     * btiene los datos de Mis Fichadas.
     *
     * @return array
     */
   public function getMisFichadas()
    {
        $result = $this->modelInstancia->getMisFichadas();
        $options = "";
        $lengthResult = count($result);

        if ($lengthResult > 0) {
            foreach ($result as $key => $value) {
                $id = $value["id"];
                $descripcion = trim($value["descripcion_mis_fichadas"]);
                $options .= "<option value='$id'>$descripcion</option>\n";
            }
        }
        return $options;
    }

    /**
     * Obtiene los datos de los detalles de Mis Fichadas.
     *
     * @param integer $idMisFichadas Id de l fichada.
     * @return array
     */
    public function getDetallesMisFichadas($idMisFichadas)
    {
        $result = $this->modelInstancia->getDetallesMisFichadas($idMisFichadas);
        $result = $this->findURLreplace($result["descripcion"]);
        setLog($result[0], $result[1], false);
        return $result;
    }

    /**
     * Busca y reemplaza enlaces de URL y les agrega el tag '<a>'
     *
     * @param [strong] $text Texto donde se buscará.
     * @return string
     */
    private function findURLreplace($text)
    {
        $needle = "\${";
        $inicioURL = stripos($text, $needle);
        $substr = substr($text, $inicioURL + 5);
        $finalURL = stripos($substr, "}");
        $substr2 = substr($substr, 0, $finalURL);
        $findURL = "\${" . $substr2 . "}";
        $anchor = "<a href=\"$substr2\" target=\"_blank\"><strong><u>$substr2</strong></u></a>";
        $textoFinal = str_replace($findURL, $anchor, $text);

        return $textoFinal;
    }
}

/**
 * La variable superglobal $_REQUEST obtiene el valor las variables tanto por POST como por GET.
 */
$function = ($_REQUEST["function"]) ? $_REQUEST["function"] : null;

if ($function != null) {
    switch (strtolower($function)) {
        case "updateformulario":
            echo updateFormulario();
            break;
        case "getdetallesmisfichadas":
            echo getDetallesMisFichadas();
            break;
    }
}

/**
 * Obtiene los detalles de Mis Fichadas.
 *
 * @return json|false
 */
function getDetallesMisFichadas()
{
    if (isset($_REQUEST["idMisFichadas"]) && !empty($_REQUEST["idMisFichadas"])) {
        $idMisFichadas = $_REQUEST["idMisFichadas"];
        $controller = FormularioController::getInstance();
        $result = $controller->getDetallesMisFichadas($idMisFichadas);

        return json_encode($result);
    } else {
        return false;
    }
}

/**
 * Actualiza los datos del formulario.
 *
 * @return integer
 */
function updateFormulario()
{
    $fecha = new DateTime();
    $timestamp = $fecha->getTimestamp();

    if (isset($_REQUEST["idMisFichadas"]) and !empty($_REQUEST["idMisFichadas"])) {
        $idMisFichadas = $_REQUEST["idMisFichadas"];
        $userid = base64_decode($_REQUEST["userid"]);
    } else {
        return 0;
    }

    $fecha_fin = date("Y/m/d H:i:s");
    $controller = FormularioController::getInstance();
    $r = $controller->updateFormulario($userid, $fecha_fin, $idMisFichadas);

    if ($r[0] == 1) {
        if (isset($_REQUEST["conid"]) && !empty($_REQUEST["conid"])) {
            $_SESSION["conid"] = $_REQUEST["conid"];
        }
        setLog(200, $r[1], true);
    } else {
        setLog(0, $r[1]);
    }

    return $r[0];
}
?>