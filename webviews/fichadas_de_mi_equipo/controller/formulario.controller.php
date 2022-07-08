
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include_once $_SERVER['DOCUMENT_ROOT'] . "/hilos/101003/webviews/fichadas_de_mi_equipo/model/formulario.model.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/hilos/101003/webviews/fichadas_de_mi_equipo/inc/functions.php";

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

    public function insertFormulario($identificador, $fecha_inicio, $origen)
    {
        $this->getDetallesFichadasDeMiEquipo(3);
        $res = $this->modelInstancia->insertFormulario($identificador, $fecha_inicio, $origen);
        setLog($res[0], $res[1], false);
        return $res;
    }

    /**
     * Actualiza los datos del formulario.
     *
     * @param string $identificador   N° que identifica al usuario.
     * @param datetime $fecha_fin     Fecha en la que se termina de actualizar los datos restantes del formulario.
     * @param integer $idFichadasDeMiEquipo  Id de la fichada del equipo.
     * @return void
     */
    public function updateFormulario($identificador, $fecha_fin, $idFichadasDeMiEquipo)
    {
        $res = $this->modelInstancia->updateFormulario($identificador, $fecha_fin, $idFichadasDeMiEquipo);
        setLog($res[0], $res[1], false);
        return $res;
    }

    /**
     * Obtiene los datos de las fichadas del equipo.
     *
     * @return array
     */
    public function getFichadasDeMiEquipo()
    {
        try {
            $result = $this->modelInstancia->getFichadasDeMiEquipo();
            $options = "";
            $lengthResult = count($result);

            if ($lengthResult > 0) {
                foreach ($result as $key => $value) {
                    $id = $value["id"];
                    $descripcion = trim($value["descripcion_fichadas_de_mi_equipo"]);
                    $options .= "<option value='$id'>$descripcion</option>\n";
                }
            }

            return $options;
        } catch (Exception $e) {

            return "";
        }
    }

    public function getDetallesFichadasDeMiEquipo($idFichadasDeMiEquipo)
    {
        try {
            $result = $this->modelInstancia->getDetallesFichadasDeMiEquipo($idFichadasDeMiEquipo);
            $result = $this->findURLreplace($idFichadasDeMiEquipo, $result["descripcion"]);
            setLog($result[0], $result[1], false);

            return $result;
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return [];
        }
    }

    private function findURLreplace($idFichadasDeMiEquipo, $text)
    {
        try {
            $idFichadasDeMiEquipo = intval($idFichadasDeMiEquipo);

            $arrayTextLink = array(
                "item1" => "Reporte de asistencia", // --> Items 1 del select
                "item2" => "Justificación de atrasos", // --> Items 2 del select
                "item3" => "Justificación de adelantos", // --> Items 3 del select
            );

            $arrayLinks = array();
            $c = 0;
            $strCount = substr_count($text, "\${"); // Contamos la cantidad de enlaces que hay dentro del texto.

            if ($strCount !== 0) {
                for ($i = 0; $i < $strCount; $i++) {

                    // Sí hay un sólo enlace dentro de la cadena.
                    if (count($arrayLinks) === 0) {
                        $text = "<p>$text</p>";
                        $text = str_replace("\\n", "<br>", $text);
                        $inicioURL = stripos($text, "\${");
                        $substr = substr($text, $inicioURL + 2);
                        $finalURL = stripos($substr, "}");
                        $substr2 = substr($substr, 0, $finalURL);
                        $findURL = "\${" . $substr2 . "}";
                        $index = ($this->evaluateSelected($idFichadasDeMiEquipo) !== -1) ?  $this->evaluateSelected($idFichadasDeMiEquipo): -1;
                        $a = $arrayTextLink[$index];
                        $textToShow = (isset($arrayTextLink[$index])) ? $arrayTextLink[$index] : $substr2;
                        $anchor = "<a href=\"$substr2\" target=\"_blank\"><u>$textToShow</u></a>";
                        $textoFinal = str_replace($findURL, $anchor, $text);

                        array_push($arrayLinks, array(
                            "link" => $textoFinal,
                        ));
                    } else {
                        // Sí hay varios enlaces dentro de la cadena.
                        foreach ($arrayLinks as $key => $value) {
                            end($arrayLinks); // Mueve el puntero interno al final del array.
                            $key = key($arrayLinks); // Obtiene la clave del elemento apuntada por el puntero interno.

                            $link = $arrayLinks[$key]["link"];
                            $link = str_replace("\\n", "<br>", $link);
                            $inicioURL = stripos($link, "\${");
                            $substr = substr($link, $inicioURL + 2);
                            $finalURL = stripos($substr, "}");
                            $substr2 = substr($substr, 0, $finalURL);
                            $findURL = "\${" . $substr2 . "}";
                            $index = ($this->evaluateSelected($idFichadasDeMiEquipo) !== -1) ?  $this->evaluateSelected($idFichadasDeMiEquipo): -1;
                            $textToShow = (isset($arrayTextLink[$index][$c])) ? $arrayTextLink[$index][$c] : $substr2;
                            $anchor = "<a href=\"$substr2\" target=\"_blank\"><u>$textToShow</u></a>";
                            $textoFinal = str_replace($findURL, $anchor, $link);

                            array_push($arrayLinks, array(
                                "link" => $textoFinal,
                            ));

                            unset($arrayLinks[0]); // Elimina el 1er indice, porque se repite el texto.
                            $c++;
                        }
                    }
                }

                end($arrayLinks); // Mueve el puntero interno al final del array.
                $key = key($arrayLinks); // Obtiene la clave del elemento apuntada por el puntero interno.
                $textoFinal = $arrayLinks[$key]["link"];

            } else {
                $text = "<p>$text</p>";
                $text = str_replace("\\n", "<br>", $text);
                $textoFinal = $text;
            }

            return $textoFinal;
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return "";
        }
    }

    /**
     * Retorna el item del arrayLinks según el id seleccionado.
     *
     * @param integer $idSelected Id del item seleccionado.
     * @return string
     */
    private function evaluateSelected($idSelected)
    {
        try {
            if (isset($idSelected) && $idSelected !== 0) {
                switch ($idSelected) {
                    case 1:
                        $index = "item1";
                        return $index;

                        break;
                    case 2:
                        $index = "item2";
                        return $index;

                        break;
                    case 3:
                        $index = "item3";
                        return $index;

                        break;
                    default:
                        return -1;
                }
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return "";
        }
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
        case "getdetallesfichadasdemiequipo":
            echo getDetallesFichadasDeMiEquipo();
            break;
    }
}

/**
 * Obtiene los detalles de las Fichadas de Equipo.
 *
 * @return json|false
 */
function getDetallesFichadasDeMiEquipo()
{
    try {
        if (isset($_REQUEST["idFichadasDeMiEquipo"]) && !empty($_REQUEST["idFichadasDeMiEquipo"])) {
            $idFichadasDeMiEquipo = $_REQUEST["idFichadasDeMiEquipo"];

            $controller = FormularioController::getInstance();
            $result = $controller->getDetallesFichadasDeMiEquipo($idFichadasDeMiEquipo);

            return json_encode($result);
        } else {
            return false;
        }
    } catch (Exception $e) {

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

    if (isset($_REQUEST["idFichadasDeMiEquipo"]) and !empty($_REQUEST["idFichadasDeMiEquipo"])) {
        $idFichadasDeMiEquipo = $_REQUEST["idFichadasDeMiEquipo"];
        $conId = base64_decode($_REQUEST["conId"]);
    } else {
        return 0;
    }

    $fecha_fin = date("Y/m/d H:i:s");
    $controller = FormularioController::getInstance();
    $result = $controller->updateFormulario($conId, $fecha_fin, $idFichadasDeMiEquipo);

    if ($result[0] == 1) {
        if (isset($_REQUEST["conId"]) && !empty($_REQUEST["conId"])) {
            $_SESSION["conid"] = $_REQUEST["conId"];
        }
        setLog(200, $result[1], true);
    } else {
        setLog(0, $result[1]);
    }

    return $result[0];
}

?>