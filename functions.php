<?php
/**
 * Crea un archivo .log para hacer debug del código en el servidor.
 *
 * @param string $string Sólo el texto que se quiere imprimir.
 * @param string $data Array o Json con datos a imprimir
 * @param boolean $assoc True en caso de imprimir un array asociativo.
 * @return string
 */
function setLogDebug($string = "", $data = "", $assoc = false)
{
    try {
        $fileName = "testing.log";

        if (!empty($string)) {

            //Imprimirá el texto que se le pase por parámetro en la variable '$string'
            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . "/$fileName", $fecha . "\n\n" . $string . "\n\n", FILE_APPEND);
        } else if ($data != "" || $data != null && $assoc) {
            //Imprimirá un arrayAssoc con los datos que tenga '$data', sí la variable '$assoc' es true y
            //la variable '$data' no este vacía o sea nulla.
            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . "/$fileName", $fecha . "\n\n" . print_r($data, $assoc) . "\n\n", FILE_APPEND);
        } else {
            //Imprimirá el texto 'Datos vacios.' si no se le mando texto en '$string' o sí '$data' es vacia o nula.
            $default_text = "Datos vacios.";
            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . "/$fileName", $fecha . "\n\n" . $default_text . "\n\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        $message = $e->getMessage();

        $fecha = date("Y/m/d H:i:s");
        file_put_contents(__DIR__ . '/errorsFileLogs.log', $fecha . "\n\n" . $message . "\n\n", FILE_APPEND);
    }
}

function insertarEntrante($iden, $palabra)
{
    global $obj;

    $ahora = date("Y/m/d H:i:s");
    $idMedio = 4;

    $command = "INSERT INTO entrantes (numero, mensaje, fecha, procesado, fecha_salida, id_medio) VALUES (";
    $command .= "'$iden', '$palabra', '$ahora', 1, '$ahora', $idMedio);";
    $res = $obj->executeSentence($command);
}

function obtenerAleatoria($tipo)
{
    global $obj;

    $res = $obj->getRespuestaAutomatica($tipo);
    return $res[rand(0, count($res) - 1)]['descripcion'];
}

function actualizarLegajo($iden, $nombre, $apellido, $legajo)
{
    global $obj;

    $sql = "UPDATE usuariosfinales SET nombre = '$nombre', apellido = '$apellido', legajo = '$legajo' WHERE identificador = '$iden';";
    $sql2 = "UPDATE usuariosfinalescompletos SET nombre = '$nombre', apellido = '$apellido', legajo = '$legajo' WHERE identificador = '$iden';";

    $obj->executeSentence($sql);
    $obj->executeSentence($sql2);
}

function tieneLegajo($iden)
{
    global $obj;
    $res = $obj->executeQuery(
        "SELECT * FROM usuariosfinales WHERE identificador = '$iden' AND legajo > 0;"
    );

    return $res != null ? true : false;
}

function palabraSoloTexto($iden, $texto)
{
    $data = array(
        "recipient" => array(
            "id" => $iden,
        ),
        "message" => array(
            "text" => $texto,
        ),
    );

    return $data;
}

/**
 * Realiza el envío de email.
 *
 * @param [string] $name Nombre del usuario logedo.
 * @param [string] $lastName Apellido del usuario logedo.
 * @param [string] $legajo N° de legajo del usuario logedo.
 * @param [array] $arrayResultQuery Array que contendrá los datos del formulario.
 * @return void
 */
function sendEmail($name, $lastName, $legajo, $arrayResultQuery)
{
    global $obj;
    $lengthArrayResultQuery = count($arrayResultQuery);

    if ($lengthArrayResultQuery > 0) {
        $currentDate = date("Y/m/d H:i:s");

        foreach ($arrayResultQuery as $key => $value) {
            $fechaFin = date('d/m/Y H:i:s', strtotime($value["fecha_fin"])); // -> Se formatea fecha.
            $idTipoConsulta = intval($value["idtipoconsulta"]);
            $descripcion_tipoConsulta = $value["descripcion_tipoConsulta"];
            $descripcion_consulta = $value["descripcion_consulta"];

            $texto = "<b>Fecha de consulta:</b> $fechaFin <br />";
            $texto .= "<b>Nombre:</b> $name <br />";
            $texto .= "<b>Apellido:</b> $lastName <br />";
            $texto .= "<b>&#8470; de Legajo:</b> $legajo <br />";
            $texto .= "<b>Descripci&#243;n consulta:</b> $descripcion_consulta";

            // Armamos dinámicamente los Asuntos y los Destinatarios.
            $tipoConsulta = ARRAY_DESTINATARIOS[$idTipoConsulta];
            $tipoConsulta["asunto"] = $descripcion_tipoConsulta; // Le asigna el asunto.
            $asunto = $tipoConsulta["asunto"];
            $destinatarios = $tipoConsulta["destinatarios"];

            // Insertamos los datos en la tabla cdmails.
            $query = "INSERT INTO cd_mails (fecha, enviado, texto, asunto, destinatarios) ";
            $query .= "VALUES ('$currentDate', 0, '$texto', '$asunto', '$destinatarios')";
            $obj->executeSentence($query);
        }
    }
}
