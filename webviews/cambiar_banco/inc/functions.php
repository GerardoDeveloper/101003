<?php
	include_once("config.php");
	
	function getUserData($userId) {
		$url = "https://graph.facebook.com/v3.2/$userId?fields=first_name,last_name,gender&access_token=" . FB_TOKEN;
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		$response = curl_exec($ch);
		
		return json_decode($response, TRUE);
	}
	
	function sendToMessenger($data) {
		if ($_SESSION["conid"]) {		
			$url = "https://tv1.chatbotamerica.com/Home/CallBackPlataformPost";
			$data = substr($data, 0, -1) . ',"conexion":"' . $_SESSION["conid"] . '"}';
			//file_put_contents(__DIR__ . '/prueba.log', $data, FILE_APPEND);
        } else {
            $url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . FB_TOKEN;
        }
        
        $ch = curl_init($url); 
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))                                                                       
        );                                                                                                                   
                                                                                                                             
        if (!$result = curl_exec($ch)) {
            curl_close($ch);
            file_put_contents(__DIR__ . '/prueba.log', "Fallo el envio..", FILE_APPEND);
            return "0";
        } else {
            curl_close($ch);
            
            return "1";
        }
    }
	
	function setLog($cod_error, $error, $send=FALSE) {
        session_start();
		$userId = isset($_SESSION['conid']) && $_SESSION['conid'] != '' ? $_SESSION['conid'] : $_SESSION['userid'];

        $codes = json_decode(STATES_CODES, TRUE);
        if ($cod_error != 1) {
            $path = "php-errors.log";
            $timeZone = date_default_timezone_get();
            $date = date("d-M-Y H:i:s");
            $message = "[$date $timeZone - User: $userId]: Code: $cod_error. Message: " . $codes[$cod_error]["message"];
            $message = !empty($error) ? $message . " | $error \n\n" : $message . "\n\n";
            error_log($message, 3, $path);
            
            if ($send) {
                $palabra = urlencode($codes[$cod_error]["payload"]);
                if ($_SESSION["conid"]) {
					$jsonData = file_get_contents("http://labs357.com.ar/messengerhilo_chatweb.php?sender=$userId&numcta=" . LABS_CUENTA . "&palabra=$palabra");
                } else {
                    $jsonData = file_get_contents("http://labs357.com.ar/messengerhilo.php?sender=$userId&numcta=" . LABS_CUENTA . "&palabra=$palabra");
				}

				$jsonData = str_replace("\\\\n", "\n", $jsonData);
				//$jsonData = str_replace("\\uD83D\\uDE09", "\uD83D\uDE09", $jsonData);

                sendToMessenger($jsonData);
            }
        }
    }
	
	function createPostCurl($url, $params) {
		$data = http_build_query($params);
				
		$ch = curl_init($url); 
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                                                                                    
		return $ch;
	}
	
	function getId($q) {
		//Generate a random string.
		$r1 = base64_encode(bin2hex(openssl_random_pseudo_bytes($q*2)) . base64_encode(openssl_random_pseudo_bytes($q*2)) . base64_encode(openssl_random_pseudo_bytes($q*2)) . base64_encode(openssl_random_pseudo_bytes($q*2)));

		$token = "";
		for ($x = 0; $x < $q; $x++) {
		  $token .= str_replace("=", "", base64_encode($r1[rand(0, strlen($r1)-1)]));
		}

		//Print it out for example purposes.
		return $token;	
	}	

	function uploadFile($fileImagen, $pathCarpeta){
	    try{
	      $nombre_archivo_final = '';
	      $hoy = getdate();
	      $dia = $hoy['mday'];
	      $mes = $hoy['mon'];
	      $anio = $hoy['year'];
	      $hora = $hoy['hours'];
	      $minutos = $hoy['minutes'];
	      $segundos = $hoy['seconds'];
	      if ($dia < 10){
	        $dia = "0" . $dia;
	      }
	      if ($mes < 10){
	        $mes = "0" . $mes;
	      }
	      if ($hora < 10){
	        $hora = "0" . $hora;
	      }
	      if ($minutos < 10){
	        $minutos = "0" . $minutos;
	      }
	      if ($segundos < 10){
	        $segundos = "0" . $segundos;
	      }

	      $unwanted_array = array('??'=>'S', '??'=>'s', '??'=>'Z', '??'=>'z', '??'=>'A', '??'=>'A', '??'=>'A', 
	                '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'C', '??'=>'E', '??'=>'E',
	                              '??'=>'E', '??'=>'E', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'N', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'U',
	                              '??'=>'U', '??'=>'U', '??'=>'U', '??'=>'Y', '??'=>'B', '??'=>'Ss', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'c',
	                              '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'o', '??'=>'n', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o',
	                              '??'=>'o', '??'=>'o', '??'=>'u', '??'=>'u', '??'=>'u', '??'=>'y', '??'=>'b', '??'=>'y', ' ' => '', '/' => '-');
	      
	      $nombre_archivo_final =  $anio . $mes . $dia . "-" . $hora . $minutos . $segundos . "-" . strtr($fileImagen['name'], $unwanted_array);

	      if (move_uploaded_file($fileImagen['tmp_name'], $pathCarpeta . $nombre_archivo_final)) {
	        return ["estado" => true, "nombre_archivo" => $nombre_archivo_final];
	      }
	      error_log("Se produjo un error al intentar subir la imagen.");
	    } 
	    catch (Exception $e) {
	      error_log("Se produjo una excepci??n al intentar subir la imagen. Excepci??n: " . $e->getMessage());
	    }
	    return ["estado" => false];
	}
?>