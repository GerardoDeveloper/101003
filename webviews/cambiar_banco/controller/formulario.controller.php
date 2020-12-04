
<?php
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

	$d = "hilos/101003/webviews/cambiar_banco";
	include_once($_SERVER['DOCUMENT_ROOT'] . "/$d/model/formulario.model.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/$d/inc/functions.php");
	
	class FormularioController {	
		private static $instancia;
		private $modelInstancia;
		private $formulario;
		
		public function __construct() {
			try {
				$this->modelInstancia = FormularioModel::getInstance();
			} 
			catch (Exception $e) {
				$error = "Error!: " . $e->getMessage();

				die();
			}
		}

		public static function getInstance() {
			if (!isset(self::$instancia)) {
				$miclase = __CLASS__;

				self::$instancia = new $miclase;
			}

			return self::$instancia;
		}

		function insertFormulario($identificador, $fecha_inicio, $origen) {
			$res = $this->modelInstancia->insertFormulario($identificador, $fecha_inicio, $origen);
			setLog($res[0], $res[1], false);
			return $res;
		}

		function updateFormulario($identificador, $fecha_fin, $empresa, $apellidos, $nombres, $dni, $banco, $cbu, $fecha_ingreso, $provincia) {
			$res = $this->modelInstancia->updateFormulario($identificador, $fecha_fin, $empresa, $apellidos, $nombres, $dni, $banco, $cbu, $fecha_ingreso, $provincia);
			setLog($res[0], $res[1], false);
			return $res;
		}		
	}
	
	if (isset($_POST["function"]) AND !empty($_POST["function"])) {
		$function = $_POST["function"];
	} elseif (isset($_GET["function"]) AND !empty ($_GET["function"])) {
		$function = $_GET["function"];
	} else {
		$function = NULL;
	}

	if ($function != NULL) {
		switch(strtolower($function)) {
			case "updateformulario":
				echo updateFormulario();
				break;				
		}
	}
	
	function updateFormulario () {
		$fecha = new DateTime();
		$timestamp = $fecha->getTimestamp();
		
		if (isset($_POST["empresa"]) AND !empty($_POST["empresa"]) AND
			isset($_POST["apellidos"]) AND !empty($_POST["apellidos"]) AND
			isset($_POST["nombres"]) AND !empty($_POST["nombres"]) AND
			isset($_POST["dni"]) AND !empty($_POST["dni"]) AND
			isset($_POST["banco"]) AND !empty($_POST["banco"]) AND
			isset($_POST["cbu"]) AND !empty($_POST["cbu"]) AND
			isset($_POST["fecha_ingreso"]) AND !empty($_POST["fecha_ingreso"]) AND
			isset($_POST["provincia"]) AND !empty($_POST["provincia"])
		) {
			$empresa = $_POST["empresa"];
			$apellidos = $_POST["apellidos"];
			$nombres = $_POST["nombres"];
			$dni = $_POST["dni"];
			$banco = $_POST["banco"];
			$cbu = $_POST["cbu"];
			$fecha_ingreso = $_POST["fecha_ingreso"];
			$provincia = $_POST["provincia"];

			$userid   = base64_decode($_POST["userid"]);
		} 
		else if (isset($_GET["empresa"]) AND !empty($_GET["empresa"]) AND
				isset($_GET["apellidos"]) AND !empty($_GET["apellidos"]) AND
				isset($_GET["nombres"]) AND !empty($_GET["nombres"]) AND
				isset($_GET["dni"]) AND !empty($_GET["dni"]) AND
				isset($_GET["banco"]) AND !empty($_GET["banco"]) AND
				isset($_GET["cbu"]) AND !empty($_GET["cbu"]) AND
				isset($_GET["fecha_ingreso"]) AND !empty($_GET["fecha_ingreso"]) AND
				isset($_GET["provincia"]) AND !empty($_GET["provincia"])
		) {
			$empresa = $_GET["empresa"];
			$apellidos = $_GET["apellidos"];
			$nombres = $_GET["nombres"];
			$dni = $_GET["dni"];
			$banco = $_GET["banco"];
			$cbu = $_GET["cbu"];
			$fecha_ingreso = $_GET["fecha_ingreso"];
			$provincia = $_GET["provincia"];

			$userid   = base64_decode($_GET["userid"]);
		} 
		else {
			return 0;
		}
		
		$fecha_fin = date("Y/m/d H:i:s");
		$controller = FormularioController::getInstance();
		$r = $controller->updateFormulario($userid, $fecha_fin, $empresa, $apellidos, $nombres, $dni, $banco, $cbu, $fecha_ingreso, $provincia);
				
		if ($r[0] == 1){
			file_put_contents(__DIR__ . '/prueba.log', "Set log en true ".print_r($_POST,TRUE), FILE_APPEND);
			if(isset($_POST["conid"]) && !empty($_POST["conid"]))
			{
				$_SESSION["conid"] = $_POST["conid"];
			}
			setLog(200, $r[1], TRUE);
		}
		else{
			file_put_contents(__DIR__ . '/prueba.log', "Set log en false", FILE_APPEND);
			setLog(0, $r[1]);
		}

		return $r[0];
	}	
?>