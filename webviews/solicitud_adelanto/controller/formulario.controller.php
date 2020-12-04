
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$d = "hilos/101003/webviews/solicitud_adelanto";
include_once($_SERVER['DOCUMENT_ROOT'] . "/$d/model/formulario.model.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/$d/inc/functions.php");

class FormularioController
{
	private static $instancia;
	private $modelInstancia;
	private $formulario;

	public function __construct()
	{
		try {
			$this->modelInstancia = FormularioModel::getInstance();
		} catch (Exception $e) {
			$error = "Error!: " . $e->getMessage();

			die();
		}
	}

	public static function getInstance()
	{
		if (!isset(self::$instancia)) {
			$miclase = __CLASS__;

			self::$instancia = new $miclase;
		}

		return self::$instancia;
	}

	function insertFormulario($identificador, $fecha_inicio, $origen)
	{
		$res = $this->modelInstancia->insertFormulario($identificador, $fecha_inicio, $origen);
		setLog($res[0], $res[1], false);
		return $res;
	}

	function updateFormulario($identificador, $fecha_fin, $apellidos, $nombres, $legajo, $importe_acreditar)
	{
		$res = $this->modelInstancia->updateFormulario($identificador, $fecha_fin, $apellidos, $nombres, $legajo, $importe_acreditar);
		setLog($res[0], $res[1], false);
		return $res;
	}
}

if (isset($_POST["function"]) and !empty($_POST["function"])) {
	$function = $_POST["function"];
} elseif (isset($_GET["function"]) and !empty($_GET["function"])) {
	$function = $_GET["function"];
} else {
	$function = NULL;
}

if ($function != NULL) {
	switch (strtolower($function)) {
		case "updateformulario":
			echo updateFormulario();
			break;
		case "actionvolver":
			actionVolver();
			break;
	}
}

function actionVolver()
{
	$palabra = urlencode("_tambien puedo");
	$userId = $_POST["userid"];
	$conId = $_POST["conid"];
	if ($conId) {
		if(!isset($_SESSION["conid"]))
		{
			session_start();
			$_SESSION["conid"] = $conId;	
		}
		$jsonData = file_get_contents("http://labs357.com.ar/messengerhilo_chatweb.php?sender=$conId&numcta=" . LABS_CUENTA . "&palabra=$palabra");
	} else {
		$jsonData = file_get_contents("http://labs357.com.ar/messengerhilo.php?sender=$userId&numcta=" . LABS_CUENTA . "&palabra=$palabra");
	}
	$jsonData = str_replace("\\\\n", "\n", $jsonData);
	sendToMessenger($jsonData);
	die();
}

function updateFormulario()
{
	$fecha = new DateTime();
	$timestamp = $fecha->getTimestamp();

	if (
		isset($_POST["apellidos"]) and !empty($_POST["apellidos"]) and
		isset($_POST["nombres"]) and !empty($_POST["nombres"]) and
		isset($_POST["legajo"]) and !empty($_POST["legajo"]) and
		isset($_POST["importe_acreditar"]) and !empty($_POST["importe_acreditar"])
	) {
		$apellidos = $_POST["apellidos"];
		$nombres = $_POST["nombres"];
		$legajo = $_POST["legajo"];
		$importe_acreditar = $_POST["importe_acreditar"];

		$userid   = base64_decode($_POST["userid"]);
	} else if (
		isset($_GET["apellidos"]) and !empty($_GET["apellidos"]) and
		isset($_GET["nombres"]) and !empty($_GET["nombres"]) and
		isset($_GET["legajo"]) and !empty($_GET["legajo"]) and
		isset($_GET["importe_acreditar"]) and !empty($_GET["importe_acreditar"])
	) {
		$apellidos = $_GET["apellidos"];
		$nombres = $_GET["nombres"];
		$legajo = $_GET["legajo"];
		$importe_acreditar = $_GET["importe_acreditar"];

		$userid   = base64_decode($_GET["userid"]);
	} else {
		return 0;
	}

	$fecha_fin = date("Y/m/d H:i:s");
	$controller = FormularioController::getInstance();
	$r = $controller->updateFormulario($userid, $fecha_fin, $apellidos, $nombres, $legajo, $importe_acreditar);

	if ($r[0] == 1) {
		if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
			$_SESSION["conid"] = $_POST["conid"];
		}
		setLog(200, $r[1], TRUE);
	} else {
		setLog(0, $r[1]);
	}

	return $r[0];
}
?>