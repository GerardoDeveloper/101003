<?php
    function insertarEntrante($iden, $palabra) {
        global $obj;

        $ahora = date("Y/m/d H:i:s");
        $idMedio = 4;	

        $command  = "INSERT INTO entrantes (numero, mensaje, fecha, procesado, fecha_salida, id_medio) VALUES (";
        $command .= "'$iden', '$palabra', '$ahora', 1, '$ahora', $idMedio);";
        $res = $obj->executeSentence($command);
    }

    function obtenerAleatoria($tipo) {
        global $obj;

        $res = $obj->getRespuestaAutomatica($tipo);
        return $res[rand(0, count($res)-1)]['descripcion'];
    }

    function actualizarLegajo($iden, $nombre, $apellido, $legajo) {
        global $obj;

        $sql = "UPDATE usuariosfinales SET nombre = '$nombre', apellido = '$apellido', legajo = '$legajo' WHERE identificador = '$iden';";
        $sql2 = "UPDATE usuariosfinalescompletos SET nombre = '$nombre', apellido = '$apellido', legajo = '$legajo' WHERE identificador = '$iden';";

        $obj->executeSentence($sql);
        $obj->executeSentence($sql2);
    }

    function tieneLegajo($iden) {
        global $obj;
        $res = $obj->executeQuery(
            "SELECT * FROM usuariosfinales WHERE identificador = '$iden' AND legajo > 0;"
        );

        return $res != null ? true : false;
    }

    function palabraSoloTexto($iden, $texto) {
        $data = Array (
            "recipient" => Array (
                "id" => $iden
            ),
            "message" => Array (
                "text" => $texto
            )
        );

        return $data;
    }
?>