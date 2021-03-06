<?php
sleep(0.15);
$nombre_db = "_" . $cuenta;
$obj = new tabla($nombre_db);
$token = $obj->getToken($cuenta);
$token = $token[0]['password'];
$fecha = new DateTime();
$timestamp = $fecha->getTimestamp();
$primerVezVida = $obj->primeraVezVida($sender);
$yamando = $obj->mandoHoy($sender);
$entro = 0;
$paso = 0;
$filesUrl = URL_FILES . "_$cuenta/";
$usoHorario = "-3";
$mensajeSiguiente = null;
$_nombre_ = utf8_decode($_nombre_);
$_apellido_ = utf8_decode($_apellido_);
$MESSAGE_URL = CALLBACK_PLATAFORM_POST_CHATBOTAMERICA;

if ($continuar == true) {
    require_once __DIR__ . "/functions.php";
    require_once __DIR__ . "/config.php";

    // setLogDebug("Entro al bot con la palabra: $palabra");

    $mailKeywords = array(
        "_derivar banco" => array(
            "asunto" => "¿En qué banco se abrió mi cuenta?",
            "destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com, control@labs357.com",
        ),
        "_ingreso" => array(
            "asunto" => "Problemas en el Ingreso a Plataforma",
            "destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com, control@labs357.com",
        ),
        "_visualizacion enviado" => array(
            "asunto" => "Puede acceder, pero no ve el recibo",
            "destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com, control@labs357.com",
        ),
        "_restablecer enviado" => array(
            "asunto" => "Restablecer contraseña de turecibo.com",
            "destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com, control@labs357.com",
        ),
        "_alta obra social" => array(
            "asunto" => "Alta - Plan de Salud",
            "destinatarios" => "eugenia.armando@sancorsalud.com.ar,arasosaguenaga@gmail.com, control@labs357.com",
        ),
    );

    $palabrasLegajo = array("_portada", "_pedir legajo", "_legajo invalido");

    if ($primerVezVida) {
        $palabra = "_portada";
        insertarEntrante($sender, $palabra);
    }
    // =======================================Se desabilita junto con las validaciones del DNI=============================
    else {
        $ultimosDos = $obj->getUltimos($sender, 2);

        if (!in_array($ultimosDos[1]["mensaje"], $palabrasLegajo)) {
            $tieneLegajo = tieneLegajo($sender);
            $palabra = $tieneLegajo ? $palabra : "_pedir legajo";
        }
    }

    // =======================================Se desabilita junto con las validaciones del DNI=============================
    //Solicitud del dni desde la bbdd
    if (!$primerVezVida) {
        if (in_array($ultimosDos[1]["mensaje"], $palabrasLegajo)) {
            $dni = utf8_encode($palabra);

            $query = "SELECT * FROM " . TABLE_NOMINA_DNI . " WHERE dni = '$dni' AND isEnabled = 1 ORDER BY id DESC LIMIT 1";
            $res = $obj->executeQuery($query);

            // Sí la consulta trae resultados.
            if (count($res) > 0) {
                $_nombre_ = utf8_decode($res[0]["nombre"]);
                $_apellido_ = utf8_decode($res[0]["apellido"]);

                actualizarLegajo($sender, $_nombre_, $_apellido_, intval($palabra));

                $_nombre_ = utf8_encode($_nombre_);
                $_apellido_ = utf8_encode($_apellido_);

                $palabra = "_portada b";

            } else {
                if ($palabra != "_form enviado 1" && $palabra != "_tambien puedo") {
                    $palabra = "_legajo invalido";
                }
            }
        }
    }

    // Palabra que envia el formulario 'Otras consultas'.
    if (strtolower($palabra) == "_otras consultas formulario") {
        $query = "SELECT
                    FORM_OC.fecha_fin,
                    FORM_OC.idtipoconsulta,
                    TC.descripcion AS descripcion_tipoConsulta,
                    FORM_OC.descripcion_consulta
                FROM
                    " . TABLE_FORMULRIO_OTRAS_CONSULTAS . " AS FORM_OC
                        LEFT OUTER JOIN
                    " . TABLE_FORMULRIO_TIPO_CONSULTAS . " AS TC ON FORM_OC.idtipoconsulta = TC.id
                WHERE
                    fecha_fin IS NOT NULL
                        AND identificador = '$sender'
                ORDER BY FORM_OC.id DESC
                LIMIT 1;";

        $resultQuery = $obj->executeQuery($query);
        $user = $obj->getUsuario($sender);
        $legajo = $user[0]["legajo"];
        $name = utf8_encode($_nombre_);
        $lastName = utf8_encode($_apellido_);
        sendEmail(trim($name), trim($lastName), $legajo, $resultQuery);

        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\n", "\n", $data);
        $data = json_decode($data, true);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        sendToMessenger($token, $jsonData);
        sleep(0.25);

        // Llamamos a la siguiente palabra.
        $palabra = "_tambien puedo";
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\n", "\n", $data);
        $data = json_decode($data, true);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    // Palabra que envia el formulario 'Otras consultas sobre licencias'.
    if (strtolower($palabra) == "_otras consultas sobre licencias formulario") {
        $query = "SELECT
                    *
                FROM
                    " . TABLE_FORMULRIO_OTRAS_CONSULTAS_SOBRE_LICENCIAS . "
                WHERE
                    fecha_fin IS NOT NULL
                        AND identificador = '$sender'
                ORDER BY id DESC
                LIMIT 1;";

        $resultQuery = $obj->executeQuery($query);
        $user = $obj->getUsuario($sender);
        $legajo = $user[0]["legajo"];
        $name = utf8_encode($_nombre_);
        $lastName = utf8_encode($_apellido_);
        sendEmailSobreLicencias(trim($name), trim($lastName), $legajo, $resultQuery);

        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\n", "\n", $data);
        $data = json_decode($data, true);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        sendToMessenger($token, $jsonData);
        sleep(1);

        // Llamamos a la siguiente palabra.
        $palabra = "_tambien puedo";
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\n", "\n", $data);
        $data = json_decode($data, true);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    if (strtolower($palabra) == "_form enviado 1") {
        $_nombre_ = utf8_encode($_nombre_);
        $_apellido_ = utf8_encode($_apellido_);

        $query = "SELECT * FROM " . TABLE_FORM_CAMBIAR_BANCO . " WHERE fecha_fin IS NOT NULL AND identificador = '$sender' ORDER BY id DESC LIMIT 1";
        $res = $obj->executeQuery($query);

        if ($res != null) {
            $ahora = date("Y/m/d H:i:s");
            $date = $res[0]["fecha_fin"];
            $empresa = $res[0]["empresa"];
            $apellidos = $res[0]["apellidos"];
            $nombres = $res[0]["nombres"];
            $dni = $res[0]["dni"];
            $banco = $res[0]['banco'];
            $cbu = $res[0]['cbu'];

            $asunto = "Solicitud de cambio de banco";
            $dest = DESTINATARIOS_BANCO_PRODUCTION;

            $texto = "<b>Empresa:</b> $empresa <br />";
            $texto .= "<b>Apellido/s:</b> $apellidos <br />";
            $texto .= "<b>Nombre/s:</b> $nombres <br />";
            $texto .= "<b>DNI:</b> $dni <br />";
            $texto .= "<b>Banco:</b> <br /> $banco <br />";
            $texto .= "<b>CBU:</b> <br /> $cbu <br />";

            $query = "INSERT INTO " . TABLE_CD_EMAIL . " (fecha, enviado, texto, asunto, destinatarios) ";
            $query .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";

            $res = $obj->executeSentence($query);
        }
    }

    // =======================================Se desabilita junto con las validaciones del DNI=============================
    if (strtolower($palabra) == "_pedir legajo") {
        insertarEntrante($sender, $palabra);

        $text = obtenerAleatoria(1);
        $data = palabraSoloTexto($sender, $text);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    // =======================================Se desabilita junto con las validaciones del DNI=============================
    if (strtolower($palabra) == "_legajo invalido") {
        insertarEntrante($sender, $palabra);

        $text = obtenerAleatoria(2);
        $data = palabraSoloTexto($sender, $text);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    //DialogFlow AI
    $resAdministrar = $obj->getAdministrar($palabra);
    $resAlias = $obj->getAlias($palabra);
    if (
        $resAdministrar == null &&
        $resAlias == null &&
        !in_array($palabra, $mailKeywords) &&
        !in_array($palabra, $palabrasLegajo)
    ) {
        $params = array(
            "query" => $palabra,
            "lang" => "es",
            "sessionId" => $sender,
            "v" => "20170712",
        );

        $params = http_build_query($params);
        $ch = curl_init(URL_AI . $params); // INITIALISE CURL

        $post = json_encode($params); // Create JSON string from data ARRAY
        $authorization = "Authorization: Bearer 2a67e00422fe424cae8287ca673cd2b8"; // **Prepare Autorisation Token**

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization)); // **Inject Token into Header**
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        if ($result["status"]["code"] == "200") {
            if (array_key_exists("result", $result)) {
                if (array_key_exists("intentName", $result["result"]["metadata"])) {
                    $payload = $result["result"]["metadata"]["intentName"];
                    $confidence = $result["result"]["score"];
                }
            }
        }

        if (!isset($payload)) {
            $ahora = date("Y/m/d H:i:s");

            $command = "INSERT INTO " . TABLE_HISTORIAL_NO_EXITOSAS . "(fecha, texto, identificador) VALUES ('$ahora', '$palabra', '$sender');";
            $res = $obj->executeSentence($command);

            $ultimosDos = $obj->getUltimos($sender, 2);
            if ($ultimosDos[1]["mensaje"] == "_no exitosa") {
                $consultaNegativa = $palabra;
                $palabra = "_negativa 2";
            } else {
                $usoHorario = "-3";
                $idMedio = 4;

                $command = "INSERT INTO " . TABLE_ENTRANTES . "(numero, mensaje, fecha, procesado, fecha_salida, id_medio, usohorario) VALUES (";
                $command .= "'$sender', '_no exitosa', '$ahora', 1, '$ahora', $idMedio, '$usoHorario');";
                $res = $obj->executeSentence($command);

                $randomText = obtenerAleatoria(3);
                $data = palabraSoloTexto($sender, $randomText);
                $data = getBurbuja($data, "_portada c", true);
                $data = addProperties($data);
                $data = utf8_converter($data);
                $jsonData = json_encode($data);
                $jsonData = normalizeJson($jsonData);
                $jsonData = str_replace("_br_", "\\n", $jsonData);

                echo $jsonData;
                die();
            }
        } else {
            $palabra = $payload;

            $resAdm = $obj->getAdministrar($palabra);

            if ($resAdm != null) {
                $idPalabraAnt = $resAdm[0]["msj_anterior"];

                if ($idPalabraAnt > 0) {
                    $resPalAnt = $obj->getAdministrarXId($idPalabraAnt);
                    $palabraAnt = $resPalAnt[0]["palabra"];

                    $keyword = urlencode($palabraAnt);
                    $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
                    $data = file_get_contents($urlJson1, false, null);
                    $data = str_replace("<PSID>", $sender, $data);
                    $data = json_decode($data, true);

                    $data = addProperties($data);
                    $data = utf8_converter($data);
                    $jsonData = json_encode($data);
                    $_nombre_ = utf8_encode($_nombre_);
                    $_apellido_ = utf8_encode($_apellido_);
                    $jsonData = normalizeJson($jsonData);
                    $jsonData = str_replace("\\\\\\\\n", "\n", $jsonData);
                    sendToMessenger($token, $jsonData);
                }

                $keyword = urlencode($palabra);
                $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
                $data = file_get_contents($urlJson1, false, null);
                $data = str_replace("<PSID>", $sender, $data);
                $data = json_decode($data, true);

                $data = addProperties($data);
                $data = utf8_converter($data);
                $jsonData = json_encode($data);
                $_nombre_ = utf8_encode($_nombre_);
                $_apellido_ = utf8_encode($_apellido_);
                $jsonData = normalizeJson($jsonData);
                $jsonData = str_replace("\\\\\\\\n", "\n", $jsonData);
                sleep(0.15);
                sendToMessenger($token, $jsonData);

                $idPalabraSig = $resAdm[0]["msj_siguiente"];
                if ($idPalabraSig > 0) {
                    $resPalSig = $obj->getAdministrarXId($idPalabraSig);
                    $mensajeSiguiente = $resPalSig[0]["palabra"];
                }
            }
        }

        if (isset($mensajeSiguiente)) {
            $palabra = $mensajeSiguiente;
            sleep(0.15);
        } else {
            die();
        }
    }

    // =====================================Este if sobre escrbe la palabra "_portada"================================
    if (strtolower($palabra) == "_portada b") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\n", "\n", $data);
        $data = json_decode($data, true);
        //$data["recipient"]["id"] = $sender;

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        sendToMessenger($token, $jsonData);
        sleep(0.25);

        $palabra = "_portada c";
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\n", "\n", $data);
        $data = json_decode($data, true);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    if (strtolower($palabra) == "_cambiar banco b") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);
        $text = $data["message"]["text"];
        $replies = $data["message"]["quick_replies"];

        $urlformulario = FORM_CAMBIAR_BANCO . "?userid=$sender&conid=$sender&$timestamp";

        $data = array(
            "recipient" => array(
                "id" => $sender,
            ),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => $text,
                        "buttons" => array(
                            array(
                                "type" => "web_url",
                                "url" => $urlformulario,
                                "fallback_url" => $urlformulario,
                                "title" => "Cambiar banco",
                                "webview_height_ratio" => "tall",
                                "messenger_extensions" => true,
                                "webview_share_button" => "hide",
                            ),
                        ),
                    ),
                ),
                "quick_replies" => $replies,
            ),
        );

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    if (strtolower($palabra) == "_adelantos b") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);
        $text = $data["message"]["attachment"]["payload"]["text"];
        $replies = $data["message"]["quick_replies"];

        $urlformulario = FORM_SOLICITUD_ADELANTO . "?userid=$sender&conid=$sender&$timestamp";

        $data = array(
            "recipient" => array(
                "id" => $sender,
            ),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => $text,
                        "buttons" => array(
                            array(
                                "type" => "web_url",
                                "url" => $urlformulario,
                                "fallback_url" => $urlformulario,
                                "title" => "Solicitar Adelanto",
                                "webview_height_ratio" => "tall",
                                "messenger_extensions" => true,
                                "webview_share_button" => "hide",
                            ),
                        ),
                    ),
                ),
                "quick_replies" => $replies,
            ),
        );

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    if (strtolower($palabra) == "_prueba") {
        /* Envia email a administracion */
        $_nombre_ = utf8_encode($_nombre_);
        $_apellido_ = utf8_encode($_apellido_);

        $query = "SELECT * FROM " . TABLE_SOLICITUD_ADELANTOS . " WHERE fecha_fin IS NOT NULL AND identificador = '$sender' ORDER BY id DESC LIMIT 1";
        $res = $obj->executeQuery($query);

        if ($res != null) {
            $ahora = date("Y/m/d H:i:s");
            $date = $res[0]["fecha_fin"];
            $apellidos = $res[0]["apellidos"];
            $nombres = $res[0]["nombres"];
            $legajo = $res[0]["legajo"];
            $importe_acreditar = $res[0]['importe_acreditar'];

            $asunto = "CHATBOT Sancor Salud - Solicitud de adelanto";
            $dest = DESTINATARIOS_SOLICITUD_ADELANTOS_PRODUCTION;

            $texto = "<b>Fecha de solicitud:</b> $date <br />";
            $texto .= "<b>Apellido/s:</b> $apellidos <br />";
            $texto .= "<b>Nombre/s:</b> $nombres <br />";
            $texto .= "<b>Legajo:</b> $legajo <br />";
            $texto .= "<b>Importe a acreditar:</b> $ $importe_acreditar <br />";

            $query = "INSERT INTO " . TABLE_CD_EMAIL . " (fecha, enviado, texto, asunto, destinatarios) ";
            $query .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";

            $res = $obj->executeSentence($query);
        }

        die();
    }

    if (strtolower($palabra) == "_descripcion licencias") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);
        $text = $data["message"]["text"];
        $replies = $data["message"]["quick_replies"];

        $urlformulario = FORM_LICENCIAS . "?userid=$sender&conid=$sender&$timestamp";

        $data = array(
            "recipient" => array(
                "id" => $sender,
            ),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => $text,
                        "buttons" => array(
                            array(
                                "type" => "web_url",
                                "url" => $urlformulario,
                                "fallback_url" => $urlformulario,
                                "title" => "Licencias",
                                "webview_height_ratio" => "tall",
                                "messenger_extensions" => true,
                                "webview_share_button" => "hide",
                            ),
                        ),
                    ),
                ),
                "quick_replies" => $replies,
            ),
        );

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    /**
     * Mis fichadas
     */
    if (strtolower($palabra) === "_mis fichadas") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);

        $text = $data["message"]["text"];
        $text = findCaraterReplace($text);
        $textButton = "Fichadas";
        $quick_replies = $data["message"]["quick_replies"];
        $urlForm = FORM_MIS_FICHADAS . "?userid=$sender&conid=$sender&$timestamp";

        $data = createOneButton($sender, $text, $urlForm, $textButton, $quick_replies);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    /**
     * Fichadas de mi equipo
     */
    if (strtolower($palabra) === "_fichadas de mi equipo") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);

        $text = $data["message"]["text"];
        $text = findCaraterReplace($text);
        $textButton = "Fichadas de mi equipo";
        $quick_replies = $data["message"]["quick_replies"];
        $urlForm = FORM_FICHADAS_DE_MI_EQUIPO . "?userid=$sender&conid=$sender&$timestamp";

        $data = createOneButton($sender, $text, $urlForm, $textButton, $quick_replies);

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    /**
     * Cuando se presiona en la burbuja el botón 'Otras consultas'
     */
    if (strtolower($palabra) == "_otras consultas 2") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);
        $text = $data["message"]["text"];
        $textButton = "Hacer consulta";
        $replies = $data["message"]["quick_replies"];

        $urlformulario = FORM_OTRAS_CONSULTAS . "?userid=$sender&conid=$sender&$timestamp";

        $data = array(
            "recipient" => array(
                "id" => $sender,
            ),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => $text,
                        "buttons" => array(
                            array(
                                "type" => "web_url",
                                "url" => $urlformulario,
                                "fallback_url" => $urlformulario,
                                "title" => $textButton,
                                "webview_height_ratio" => "tall",
                                "messenger_extensions" => true,
                                "webview_share_button" => "hide",
                            ),
                        ),
                    ),
                ),
                "quick_replies" => $replies,
            ),
        );

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    /**
     * Cuando se presiona en la burbuja el botón 'Otras consultas sobe licencias'
     */
    if (strtolower($palabra) == "_otras consultas sobre licencias") {
        $keyword = urlencode($palabra);
        $urlJson1 = WITAI . "?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
        $data = file_get_contents($urlJson1, false, null);
        $data = str_replace("<PSID>", $sender, $data);
        $data = str_replace("\\\\\\n", "\\n", $data);
        $data = json_decode($data, true);
        $text = $data["message"]["text"];
        $textButton = "Hacer consulta";
        $replies = $data["message"]["quick_replies"];

        $urlformulario = FORM_OTRAS_CONSULTAS_SOBRE_LICENCIAS . "?userid=$sender&conid=$sender&$timestamp";

        $data = array(
            "recipient" => array(
                "id" => $sender,
            ),
            "message" => array(
                "attachment" => array(
                    "type" => "template",
                    "payload" => array(
                        "template_type" => "button",
                        "text" => $text,
                        "buttons" => array(
                            array(
                                "type" => "web_url",
                                "url" => $urlformulario,
                                "fallback_url" => $urlformulario,
                                "title" => $textButton,
                                "webview_height_ratio" => "tall",
                                "messenger_extensions" => true,
                                "webview_share_button" => "hide",
                            ),
                        ),
                    ),
                ),
                "quick_replies" => $replies,
            ),
        );

        $data = addProperties($data);
        $data = utf8_converter($data);
        $jsonData = json_encode($data);
        $jsonData = normalizeJson($jsonData);

        echo $jsonData;
        die();
    }

    /**
     * Envio de email.
     */
    if (in_array($palabra, array_keys($mailKeywords))) {
        $res = $obj->getUsuario($sender);

        $legajo = $res[0]["legajo"];
        $ahora = date("Y/m/d H:i:s");

        $asunto = utf8_decode($mailKeywords[$palabra]["asunto"]);
        $dest = $mailKeywords[$palabra]["destinatarios"];

        $n = utf8_encode($_nombre_);
        $a = utf8_encode($_apellido_);

        $texto = "<b>Fecha de consulta:</b> $date <br />";
        $texto .= "<b>Nombre:</b> $n <br />";
        $texto .= "<b>Apellido:</b> $a <br />";
        $texto .= "<b>Nº de Legajo:</b> $legajo";
        $texto = utf8_decode($texto);

        $query = "INSERT INTO " . TABLE_CD_EMAIL . " (fecha, enviado, texto, asunto, destinatarios) ";
        $query .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";

        $res = $obj->executeSentence($query);
    }

    if (strtolower($palabra) == "_negativa 2") {
        $res = $obj->getUsuario($sender);

        $legajo = $res[0]["legajo"];
        $ahora = date("Y/m/d H:i:s");

        $asunto = "Otras Consultas Internas";
        $dest = DESTINATARIOS_OTRAS_CONSULTAS_INTERNAS;

        $n = utf8_encode($_nombre_);
        $a = utf8_encode($_apellido_);

        $texto = "<b>Fecha de consulta:</b> $ahora <br />";
        $texto .= "<b>Nombre:</b> $n <br />";
        $texto .= "<b>Apellido:</b> $a <br />";
        $texto .= "<b>Nº de Legajo:</b> $legajo <br />";
        $texto .= "<b>Consulta:</b> $consultaNegativa";
        $texto = utf8_decode($texto);

        $query = "INSERT INTO " . TABLE_CD_EMAIL . " (fecha, enviado, texto, asunto, destinatarios) ";
        $query .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";

        $res = $obj->executeSentence($query);
    }
}
