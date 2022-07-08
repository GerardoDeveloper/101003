
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
        $result = $this->findURLreplace($idMisFichadas, $result["descripcion"]);
        setLog($result[0], $result[1], false);
        return $result;
    }

    /**
     * /**
     * Busca y reemplaza enlaces de URL y les agrega el tag '<a>'
     *
     * @param integer $idMisFichadas Id del item seleccionado.
     * @param string $text Texto donde se buscará.
     *
     * @return string
     */
    private function findURLreplace($idMisFichadas, $text)
    {
        $arrayTextLink = array(
            array(
                "Nueva intranet home",
                "GeovictoriaApp",
            ),
            "Reporte de asistencia",
        );

        $arrayLinks = array();
        $c = 0;
        $strCount = substr_count($text, "\${");

        if ($strCount !== 0) {
            for ($i = 0; $i < $strCount; $i++) {
                if (count($arrayLinks) === 0) {
                    $text = "<p>$text</p>";
                    $text = str_replace("\\n", "<br>", $text);
                    $inicioURL = stripos($text, "\${");
                    $substr = substr($text, $inicioURL + 2);
                    $finalURL = stripos($substr, "}");
                    $substr2 = substr($substr, 0, $finalURL);
                    $findURL = "\${" . $substr2 . "}";
                    $textToShow = ($idMisFichadas !== 3) ? $this->evaluateSelected($arrayTextLink, $idMisFichadas) : $substr2;

                    // Sí es la opción "¿Qué hago si no puedo ingresar con mi usuario y contraseña?"
                    if ($idMisFichadas !== 3) {
                        $anchor = "<a href=\"$substr2\" target=\"_blank\"><u>$textToShow</u></a>";
                    } else {
                        // Concatena el texto para abrir el email en el dispositivo.
                        $substr2 = "mailto:" . $substr2;
                        $anchor = "<a href=\"$substr2\" target=\"_blank\"><u>$textToShow</u></a>";
                    }

                    $textoFinal = str_replace($findURL, $anchor, $text);

                    array_push($arrayLinks, array(
                        "link" => $textoFinal,
                    ));

                    $c++;
                } else {
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
                        $textToShow = (isset($arrayTextLink[0][$c])) ? $arrayTextLink[0][$c] : $substr2;
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
            $textoFinal = "<p>$text</p>";
        }

        return $textoFinal;
    }

    /**
     * Evalua el id seleccionado y retorna el texto según el id.
     *
     * @param array $array Array que contiene los textos a mostrar en los enlaces.
     * @param integer $idSelected Id del item seleccionado.
     * @return string
     */
    private function evaluateSelected($array, $idSelected)
    {
        $lengthArray = count($array);

        if ($lengthArray > 0) {
            switch ($idSelected) {
                case 1:
                    $textToShow = $array[0][0];
                    return $textToShow;
                    break;
                case 5:
                    $textToShow = $array[1];
                    return $textToShow;
                    break;
            }
        }
    }
}

/**
 * La variable superglobal $_REQUEST obtiene el valor de las variables tanto por GET como por POST.
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
        $idMisFichadas = intval($_REQUEST["idMisFichadas"]);

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
        $idMisFichadas = intval($_REQUEST["idMisFichadas"]);
        $conId = base64_decode($_REQUEST["conId"]);
    } else {
        return 0;
    }

    $fecha_fin = date("Y/m/d H:i:s");
    $controller = FormularioController::getInstance();
    $r = $controller->updateFormulario($conId, $fecha_fin, $idMisFichadas);

    if ($r[0] == 1) {
        if (isset($_REQUEST["conId"]) && !empty($_REQUEST["conId"])) {
            $_SESSION["conid"] = $_REQUEST["conId"];
        }
        setLog(200, $r[1], true);
    } else {
        setLog(0, $r[1]);
    }

    return $r[0];
}

?>