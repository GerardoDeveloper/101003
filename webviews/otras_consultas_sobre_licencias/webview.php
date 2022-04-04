<?php
//file_put_contents(__DIR__ . '/prueba.log', print_r($_SERVER, true), FILE_APPEND);
const IN_PRODUCTION = true;

try {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

    session_start();

    include_once "inc/functions.php";
    include_once "controller/formulario.controller.php";

    $fecha = new DateTime();
    $timestamp = $fecha->getTimestamp();

    if (IN_PRODUCTION) {
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
            $_SESSION["conid"] = $_GET["conid"];
            $conId = $_GET["conid"];
        } else if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
            $origen = "web";
            $_SESSION["conid"] = $_POST["conid"];
            $conId = $_POST["conid"];
        } else {
            $origen = "facebook";
        }

        if (($origen == "web" && array_key_exists("HTTP_SEC_FETCH_DEST", $_SERVER)
            && $_SERVER["HTTP_SEC_FETCH_DEST"] == "iframe")
            || $origen == "facebook"
        ) {
            $instancia = FormularioController::getInstance();
            $instancia->insertFormulario($userId, date("Y/m/d H:i:s"), $origen);
        }
    } else {
        $conId = rand(0, 1152637485966359);
        $origen = "web";

        $instancia = FormularioController::getInstance();
        $instancia->insertFormulario($conId, date("Y/m/d H:i:s"), $origen);
    }
} catch (Exception $e) {
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
    <title>Formulario Consultas</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../library/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../otras_consultas/css/style.css?<?php echo $timestamp ?>">
    <!-- <link rel="stylesheet" href="css/global.css?<?php echo $timestamp ?>"> -->
</head>

<body>
    <?php
if (isset($_SESSION["conid"])) {
    echo '<input type="hidden" id="conid" data-value="' . $_SESSION["conid"] . '" />';
}
?>
    <!--Indicador de carga-->
    <div class="loader fadeIn">
        <div class="lds-spinner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- end_loader -->

    <div class="container">
        <div class="content-wrapper">
            <form id="formData" class="form-wrapper">
                <?php
unset($_SESSION["conid"]);

if (isset($_GET["conid"]) && !empty($_GET["conid"])) {
    $_SESSION["conid"] = $_GET["conid"];
} else if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
    $_SESSION["conid"] = $_POST["conid"];
}

if ($_SESSION["conid"]) {
    echo '<input type="hidden" name="conid" id="conid" value="' . $_SESSION["conid"] . '" data-value="' . $_SESSION["conid"] . '" />';
}
?>
                <div class="mt-3 mb-3">
                    <textarea class="form-control textarea_TipoDetalleLicencia" name="descripcion_consulta" id="descripcion_consulta" cols="30" rows="10"></textarea>
                </div>
            </form>

            <div class="buttons-wrapper">
                <div class="control-wrapper">
                    <button name="btnEnviar" id="btnEnviar" class="btn btn-primary float-right" type="button">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="libs/jquery.min.js"></script>
    <script>
        let conid = "<?php echo base64_encode($conId) ?>";
    </script>
    <script src="../library/SweetAlert2/sweetalert2.all.min.js"></script>
    <script src="js/global.js?<?php echo $timestamp ?>"></script>
</body>

</html>