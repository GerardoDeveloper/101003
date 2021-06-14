<?php
    //file_put_contents(__DIR__ . '/prueba.log', print_r($_SERVER, true), FILE_APPEND);
	try {
		error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		
		session_start();
		
		include_once("inc/functions.php");
		include_once("controller/formulario.controller.php");
		
		$fecha = new DateTime();
		$timestamp = $fecha->getTimestamp();

		if (isset($_GET) || isset($_POST)) {
			if (isset($_GET["userid"]) && !empty($_GET["userid"])) {
				$userId = $_GET["userid"];
			} else {
				if (isset($_POST["userid"]) && !empty($_POST["userid"])) {
					$userId = $_POST["userid"];
				} else {
					$userId = "";
					
					die();
				}
			}
		}
		
		$_SESSION["userid"] = $userId;

		if (isset($_GET["conid"]) && !empty($_GET["conid"])) {
			$origen = "web";
		} else if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
			$origen = "web";
		} else {
			$origen = "facebook";
		}

        if(($origen == "web" && array_key_exists("HTTP_SEC_FETCH_DEST", $_SERVER) 
        && $_SERVER["HTTP_SEC_FETCH_DEST"] == "iframe") 
        || $origen == "facebook")
        {
            $instancia = FormularioController::getInstance();        
            $instancia->insertFormulario($userId, date("Y/m/d H:i:s"), $origen);    
        }

	} 
	catch (Exception $e) {
		$error = $e->getMessage();
		setLog(0, $error);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cambiar Banco</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css?<?php echo $timestamp ?>">
</head>
<body>
    <div class="container">
        <div class="content-wrapper">
            <div class="logo-wrapper">
                
            </div>

            <form id="formData" class="form-wrapper">
                <?php
                    unset($_SESSION["conid"]);

                    if (isset($_GET["conid"]) && !empty($_GET["conid"])) {
                        $_SESSION["conid"] = $_GET["conid"];
                    } 
                    else if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
                        $_SESSION["conid"] = $_POST["conid"];
                    }

                    if ($_SESSION["conid"]) {
                        echo '<input type="hidden" name="conid" id="conid" value="' . $_SESSION["conid"] . '" data-value="' . $_SESSION["conid"] . '" />';
                    }
                ?>
                <div class="control-wrapper">
                    <input type="text" name="empresa" id="empresa" placeholder="Empresa" class="form-control" autocomplete="off" required>
                </div>

                <div class="control-wrapper">
                    <input type="text" name="apellidos" id="apellidos" placeholder="Apellido/s" class="form-control" autocomplete="off" required>
                </div>

                <div class="control-wrapper">
                    <input type="text" name="nombres" id="nombres" placeholder="Nombre/s" class="form-control" autocomplete="off" required>
                </div>

                <div class="control-wrapper">
                    <input type="number" name="dni" id="dni" placeholder="DNI" class="form-control" autocomplete="off" required>
                </div>

                <div class="control-wrapper">
                    <input type="text" name="banco" id="banco" placeholder="Banco" class="form-control" autocomplete="off" required>
                </div>

                <div class="control-wrapper">
                    <input type="number" name="cbu" id="cbu" placeholder="CBU" class="form-control" autocomplete="off" required>
                </div>
            </form>

            <div class="buttons-wrapper">
                <div class="control-wrapper">
                   
                    <button id="btnCancel" class="button luvi-secondary-button form-control" type="button">Cancelar</button>
                    <button id="btnSend" class="button luvi-primary-button form-control" type="button">Enviar</button>
                </div>                
            </div>
        </div>
    </div>

    <div class="overlay loader"></div>
    <div class="sk-circle loader">
        <div class="sk-circle1 sk-child"></div>
        <div class="sk-circle2 sk-child"></div>
        <div class="sk-circle3 sk-child"></div>
        <div class="sk-circle4 sk-child"></div>
        <div class="sk-circle5 sk-child"></div>
        <div class="sk-circle6 sk-child"></div>
        <div class="sk-circle7 sk-child"></div>
        <div class="sk-circle8 sk-child"></div>
        <div class="sk-circle9 sk-child"></div>
        <div class="sk-circle10 sk-child"></div>
        <div class="sk-circle11 sk-child"></div>
        <div class="sk-circle12 sk-child"></div>
    </div>    

    <script src="libs/jquery.min.js"></script>
    <script>var userId = "<?php echo base64_encode($userId) ?>";</script>    
    <script src="js/global.js?<?php echo $timestamp ?>"></script>
</body>
</html>