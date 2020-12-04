<?php
	sleep(0.15);
	$nombre_db 		= "_" . $cuenta;
	$obj 			= new tabla($nombre_db);
	$token = $obj->getToken($cuenta);
	$token = $token[0]['password'];
	$fecha = new DateTime();
	$timestamp = $fecha->getTimestamp();	
	$primerVezVida  = $obj->primeraVezVida($sender);
	$yamando 		= $obj->mandoHoy($sender);
	$entro 			= 0;
	$paso		    = 0;
	$filesUrl = "http://labs357.com.ar/files/_$cuenta/";
	$usoHorario = "-3";
	$mensajeSiguiente = null;
	$_nombre_ = utf8_decode($_nombre_);
	$_apellido_ = utf8_decode($_apellido_);
	$MESSAGE_URL = 'https://tv1.chatbotamerica.com/Home/CallBackPlataformPost';
	
	if($continuar == true){
		require_once(__DIR__ . "/functions.php");
		$mailKeywords = Array (
			"_derivar banco" => Array (
				"asunto" => "¿En qué banco se abrió mi cuenta?",
				"destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com"
			),
			"_ingreso" => Array (
				"asunto" => "Problemas en el Ingreso a Plataforma",
				"destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com"
			),
			"_visualizacion enviado" => Array (
				"asunto" => "Puede acceder, pero no ve el recibo",
				"destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com"
			),
			"_restablecer enviado" => Array (
				"asunto" => "Restablecer contraseña de turecibo.com",
				"destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com"
			),
			"_alta obra social" => Array (
				"asunto" => "Alta - Plan de Salud",
				"destinatarios" => "administracionch@sancorsalud.com.ar,antonio.ferrero@sancorsalud.com.ar,arasosaguenaga@gmail.com"
			),
			"_consulta disponibilidad" => Array (
				"asunto" => "¿Cuántos días de licencia tengo?",
				"destinatarios" => "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com"
			)
		);

		$palabrasLegajo = Array("_portada", "_pedir legajo", "_legajo invalido");

		if ($primerVezVida) {
			$palabra = "_portada";
			insertarEntrante($sender, $palabra);
		} else {
			$ultimosDos = $obj->getUltimos($sender, 2);
			if (!in_array($ultimosDos[1]["mensaje"], $palabrasLegajo)) {
				$tieneLegajo = tieneLegajo($sender);
				$palabra = $tieneLegajo ? $palabra : "_pedir legajo";
			}
		}

		if (!$primerVezVida) {
			if (in_array($ultimosDos[1]["mensaje"], $palabrasLegajo)) {
				if ($palabra != "33") {
					if($palabra != "_form enviado 1" && $palabra != "_tambien puedo")
					{
						$palabra = "_legajo invalido";
					}
					else
					{
						$_nombre_ = utf8_decode("Matías");
						$_apellido_ = "Caloia";
					}
				} else {
					$_nombre_ = utf8_decode("Matías");
					$_apellido_ = "Caloia";

					actualizarLegajo($sender, $_nombre_, $_apellido_, intval($palabra));

					$_nombre_ = utf8_encode($_nombre_);
					$_apellido_ = utf8_encode($_apellido_);

					$palabra = "_portada b";
				}
			}
		}

		if (strtolower($palabra) == "_form enviado 1") {
			$_nombre_ = utf8_encode($_nombre_);
			$_apellido_ = utf8_encode($_apellido_);

			$query = "SELECT * FROM formulario_cambiar_banco WHERE fecha_fin IS NOT NULL AND identificador = '$sender' ORDER BY id DESC LIMIT 1";
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
				$fecha_ingreso = $res[0]['fecha_ingreso'];
				$provincia = $res[0]['provincia'];

				$asunto = "Solicitud de cambio de banco";
				$dest = "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com";

				$texto  = "<b>Empresa:</b> $empresa <br />";
				$texto .= "<b>Apellido/s:</b> $apellidos <br />";
				$texto .= "<b>Nombre/s:</b> $nombres <br />";
				$texto .= "<b>DNI:</b> $dni <br />";
				$texto .= "<b>Banco:</b> <br /> $banco <br />";
				$texto .= "<b>CBU:</b> <br /> $cbu <br />";
				$texto .= "<b>Fecha ingreso:</b> <br /> $fecha_ingreso <br />";
				$texto .= "<b>Provincia:</b> <br /> $provincia <br />";
				
				$query    = "INSERT INTO cd_mails (fecha, enviado, texto, asunto, destinatarios) ";
				$query   .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";
						
				$res = $obj->executeSentence($query);
			}
		}

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

		//DialogFlow
		$resAdministrar = $obj->getAdministrar($palabra);
		$resAlias = $obj->getAlias($palabra);
		if (
			$resAdministrar == null &&
			$resAlias == null &&
			!in_array($palabra, $mailKeywords) &&
			!in_array($palabra, $palabrasLegajo)
		) {
			$params = Array (
				"query" => $palabra,
				"lang" => "es",
				"sessionId" => $sender,
				"v" => "20170712"
			);		
			
			$params = http_build_query($params);
			$ch = curl_init('https://api.dialogflow.com/v1/query?' . $params); // INITIALISE CURL
			
			$post = json_encode($params); // Create JSON string from data ARRAY
			$authorization = "Authorization: Bearer 2a67e00422fe424cae8287ca673cd2b8"; // **Prepare Autorisation Token**
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // **Inject Token into Header**
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

				$command = "INSERT INTO historial_noexitosas(fecha, texto, identificador) VALUES ('$ahora', '$palabra', '$sender');";
				$res = $obj->executeSentence($command);

				$ultimosDos = $obj->getUltimos($sender, 2);
				if ($ultimosDos[1]["mensaje"] == "_no exitosa") {
					$consultaNegativa = $palabra;
					$palabra = "_negativa 2";
				} else {	
					$usoHorario = "-3";
					$idMedio = 4;
		
					$command  = "INSERT INTO entrantes(numero, mensaje, fecha, procesado, fecha_salida, id_medio, usohorario) VALUES (";
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
		
				if ($resAdm != null){
					$idPalabraAnt = $resAdm[0]["msj_anterior"];
					
					if ($idPalabraAnt > 0){
						$resPalAnt = $obj->getAdministrarXId($idPalabraAnt);
						$palabraAnt = $resPalAnt[0]["palabra"];

						$keyword = urlencode($palabraAnt);
						$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
						$data = file_get_contents($urlJson1, false, null);
						$data = str_replace("<PSID>", $sender, $data);
						$data = json_decode($data, TRUE);
			
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
					$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
					$data = file_get_contents($urlJson1, false, null);
					$data = str_replace("<PSID>", $sender, $data);
					$data = json_decode($data, TRUE);
		
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
					if ($idPalabraSig > 0){
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

		if (strtolower($palabra) == "_portada b") {
			$keyword = urlencode($palabra);
			$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
			$data = file_get_contents($urlJson1, false, null);
			$data = str_replace("<PSID>", $sender, $data);
			$data = str_replace("\\\\n", "\n", $data);
			$data = json_decode($data, TRUE);
			//$data["recipient"]["id"] = $sender;

			$data = addProperties($data);
			$data = utf8_converter($data);
			$jsonData = json_encode($data);
			$jsonData = normalizeJson($jsonData);
	
			sendToMessenger($token, $jsonData);
			sleep(0.25);

			$palabra = "_portada c";
			$keyword = urlencode($palabra);
			$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
			$data = file_get_contents($urlJson1, false, null);
			$data = str_replace("<PSID>", $sender, $data);
			$data = str_replace("\\\\n", "\n", $data);
			$data = json_decode($data, TRUE);
			//$data["recipient"]["id"] = $sender;

			$data = addProperties($data);
			$data = utf8_converter($data);
			$jsonData = json_encode($data);
			$jsonData = normalizeJson($jsonData);

			echo $jsonData;
			die();
		}

		if (strtolower($palabra) == "_cambiar banco b") {
			$keyword = urlencode($palabra);
			$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
			$data = file_get_contents($urlJson1, false, null);
			$data = str_replace("<PSID>", $sender, $data);
			$data = str_replace("\\\\\\n", "\\n", $data);
			$data = json_decode($data, TRUE);
			$text = $data["message"]["text"];
			$replies = $data["message"]["quick_replies"];

			$urlformulario = "https://labs357.com.ar/hilos/101003/webviews/cambiar_banco/webview.php?userid=$sender&conid=$sender&$timestamp";

			$data = Array (
				"recipient" => Array (
					"id" => $sender
				),
				"message" => Array (
					"attachment" => Array (
						"type" => "template",
						"payload" => Array (
							"template_type" => "button",
							"text" => $text,
							"buttons" => Array (
								Array (
									"type" => "web_url",
									"url" => $urlformulario,
									"fallback_url" => $urlformulario,
									"title" => "Cambiar banco",
									"webview_height_ratio" => "tall",
									"messenger_extensions" => True,
									"webview_share_button" => "hide"	
								)						
							)
						)
					),
					"quick_replies" => $replies
				)
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
			$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
			$data = file_get_contents($urlJson1, false, null);
			$data = str_replace("<PSID>", $sender, $data);
			$data = str_replace("\\\\\\n", "\\n", $data);
			$data = json_decode($data, TRUE);
			$text = $data["message"]["attachment"]["payload"]["text"];
			$replies = $data["message"]["quick_replies"];

			$urlformulario = "https://labs357.com.ar/hilos/101003/webviews/solicitud_adelanto/webview.php?userid=$sender&conid=$sender&$timestamp";

			$data = Array (
				"recipient" => Array (
					"id" => $sender
				),
				"message" => Array (
					"attachment" => Array (
						"type" => "template",
						"payload" => Array (
							"template_type" => "button",
							"text" => $text,
							"buttons" => Array (
								Array (
									"type" => "web_url",
									"url" => $urlformulario,
									"fallback_url" => $urlformulario,
									"title" => "Solicitar Adelanto",
									"webview_height_ratio" => "tall",
									"messenger_extensions" => True,
									"webview_share_button" => "hide"	
								)						
							)
						)
					),
					"quick_replies" => $replies
				)
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

			$query = "SELECT * FROM formulario_solicitud_adelanto WHERE fecha_fin IS NOT NULL AND identificador = '$sender' ORDER BY id DESC LIMIT 1";
			$res = $obj->executeQuery($query);

			if ($res != null) {
				$ahora = date("Y/m/d H:i:s");
				$date = $res[0]["fecha_fin"];
				$apellidos = $res[0]["apellidos"];
				$nombres = $res[0]["nombres"];
				$legajo = $res[0]["legajo"];
				$importe_acreditar = $res[0]['importe_acreditar'];

				$asunto = "CHATBOT Sancor Salud - Solicitud de adelanto";
				$dest = "matias.eniacgroup@gmail.com,arasosaguenaga@gmail.com,kevin.eniacgroup@gmail.com";

				$texto  = "<b>Fecha de solicitud:</b> $date <br />";
				$texto .= "<b>Apellido/s:</b> $apellidos <br />";
				$texto .= "<b>Nombre/s:</b> $nombres <br />";
				$texto .= "<b>Legajo:</b> $legajo <br />";
				$texto .= "<b>Importe a acreditar:</b> $ $importe_acreditar <br />";
				
				$query    = "INSERT INTO cd_mails (fecha, enviado, texto, asunto, destinatarios) ";
				$query   .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";
						
				$res = $obj->executeSentence($query);
			}

			die();
		}

		if (strtolower($palabra) == "_descripcion licencias") {
			$keyword = urlencode($palabra);
			$urlJson1 = "https://labs357.com.ar/witai/Keyword/?cuenta=$cuenta&keyword=$keyword&prefijotabla=cw_";
			$data = file_get_contents($urlJson1, false, null);
			$data = str_replace("<PSID>", $sender, $data);
			$data = str_replace("\\\\\\n", "\\n", $data);
			$data = json_decode($data, TRUE);
			$text = $data["message"]["text"];
			$replies = $data["message"]["quick_replies"];

			$urlformulario = "https://labs357.com.ar/hilos/101003/webviews/licencias/webview.php?userid=$sender&conid=$sender&$timestamp";

			$data = Array (
				"recipient" => Array (
					"id" => $sender
				),
				"message" => Array (
					"attachment" => Array (
						"type" => "template",
						"payload" => Array (
							"template_type" => "button",
							"text" => $text,
							"buttons" => Array (
								Array (
									"type" => "web_url",
									"url" => $urlformulario,
									"fallback_url" => $urlformulario,
									"title" => "Licencias",
									"webview_height_ratio" => "tall",
									"messenger_extensions" => True,
									"webview_share_button" => "hide"	
								)						
							)
						)
					),
					"quick_replies" => $replies
				)
			);

			$data = addProperties($data);
			$data = utf8_converter($data);
			$jsonData = json_encode($data);
			$jsonData = normalizeJson($jsonData);
			
			echo $jsonData;
			die();
		}

		if (in_array($palabra, array_keys($mailKeywords))) {
			$res = $obj->getUsuario($sender);
			
			$legajo = $res[0]["legajo"];
			$ahora = date("Y/m/d H:i:s");

			$asunto = utf8_decode($mailKeywords[$palabra]["asunto"]);
			$dest = $mailKeywords[$palabra]["destinatarios"];

			$n = utf8_encode($_nombre_);
			$a = utf8_encode($_apellido_);

			$texto  = "<b>Fecha de consulta:</b> $date <br />";
			$texto .= "<b>Nombre:</b> $n <br />";
			$texto .= "<b>Apellido:</b> $a <br />";
			$texto .= "<b>Nº de Legajo:</b> $legajo";
			$texto = utf8_decode($texto);

			$query    = "INSERT INTO cd_mails (fecha, enviado, texto, asunto, destinatarios) ";
			$query   .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";
					
			$res = $obj->executeSentence($query);	
		}

		if (strtolower($palabra) == "_negativa 2") {
			$res = $obj->getUsuario($sender);
			
			$legajo = $res[0]["legajo"];
			$ahora = date("Y/m/d H:i:s");

			$asunto = "Otras Consultas Internas";
			$dest = "matias.eniacgroup@gmail.com,arasosaguenaga@gmail.com,kevin.eniacgroup@gmail.com";

			$n = utf8_encode($_nombre_);
			$a = utf8_encode($_apellido_);

			$texto  = "<b>Fecha de consulta:</b> $ahora <br />";
			$texto .= "<b>Nombre:</b> $n <br />";
			$texto .= "<b>Apellido:</b> $a <br />";
			$texto .= "<b>Nº de Legajo:</b> $legajo <br />";
			$texto .= "<b>Consulta:</b> $consultaNegativa";
			$texto = utf8_decode($texto);

			$query    = "INSERT INTO cd_mails (fecha, enviado, texto, asunto, destinatarios) ";
			$query   .= "VALUES ('$ahora', 0, '$texto', '$asunto', '$dest')";
					
			$res = $obj->executeSentence($query);
		}
	}	
?>