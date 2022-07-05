<?php
if (file_exists(__DIR__ . "/../../config.php")) {
    require_once __DIR__ . "/../../config.php";
}

try {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    session_start();

    include_once "inc/functions.php";
    include_once "controller/formulario.controller.php";

    $fecha = new DateTime();
    $timestamp = $fecha->getTimestamp();

    if (BOT_IN_PRODUCTION) {
        if (isset($_GET) || isset($_POST)) {
            if (isset($_REQUEST["userid"])) {
                $userId = $_REQUEST["userid"];
            } else {
                $userId = "";
                die();
            }
        }

        // if (isset($_GET) || isset($_POST)) {
        //     if (isset($_GET["userid"]) && !empty($_GET["userid"])) {
        //         $userId = $_GET["userid"];
        //     } else {
        //         if (isset($_POST["userid"]) && !empty($_POST["userid"])) {
        //             $userId = $_POST["userid"];
        //         } else {
        //             $userId = "";

        //             die();
        //         }
        //     }
        // }

        $_SESSION["userid"] = $userId;

        if (isset($_REQUEST["conid"]) && !empty($_REQUEST["conid"])) {
            $origen = "web";
            $conId = $_REQUEST["conid"];
        } else {
            $origen = "facebook";
        }

        // if (isset($_GET["conid"]) && !empty($_GET["conid"])) {
        //     $origen = "web";
        // } else if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
        //     $origen = "web";
        // } else {
        //     $origen = "facebook";
        // }

        if (($origen == "web" && array_key_exists("HTTP_SEC_FETCH_DEST", $_SERVER) && $_SERVER["HTTP_SEC_FETCH_DEST"] == "iframe") || $origen == "facebook") {
            $instancia = FormularioController::getInstance();
            $instancia->insertFormulario($userId, date("Y/m/d H:i:s"), $origen);

            $fichadasDeMiEquipo = $instancia->getFichadasDeMiEquipo();
        }
    } else {
        $conId = rand(0, 1152637485966359);
        $origen = "web";
        $dateTime = date("Y/m/d H:i:s");

        $instancia = FormularioController::getInstance();
        $instancia->insertFormulario($conId, $dateTime, $origen);

        $fichadasDeMiEquipo = $instancia->getFichadasDeMiEquipo();
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
    <title>Formulario fichadas de mi equipo</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../library/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fichadas_de_mi_equipo/css/style.css?<?php echo $timestamp ?>">
</head>
<body>
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

                if (isset($_REQUEST["conid"]) && !empty($_REQUEST["conid"])) {
                    $_SESSION["conid"] = $_REQUEST["conid"];
                }

                // if (isset($_GET["conid"]) && !empty($_GET["conid"])) {
                //     $_SESSION["conid"] = $_GET["conid"];
                // } else if (isset($_POST["conid"]) && !empty($_POST["conid"])) {
                //     $_SESSION["conid"] = $_POST["conid"];
                // }

                if ($_SESSION["conid"]) {
                    echo '<input type="hidden" name="conid" id="conid" value="' . $_SESSION["conid"] . '" data-value="' . $_SESSION["conid"] . '" />';
                }
                ?>
                <div class="mt-3 mb-3">
                    <select name="fichadas-equipo" id="fichadas-equipo" class="form-control fichadas-equipo_select" autocomplete="off" required onfocus="(this.options[0].style.display='none')">
                        <option value="" disabled selected>Selecciona el tipo de fichada del equipo</option>
                        <?php echo $fichadasDeMiEquipo; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-control detalle_FichadasEquipo" id="detalle_fichadas_equipo" data-placeholder="Detalle del tipo de licencia seleccionada"></div>
                </div>
            </form>

            <div class="buttons-wrapper">
                <div class="control-wrapper">
                    <button name="btnClose" id="btnClose" class="btn btn-primary float-right" type="button">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="libs/jquery.min.js"></script>
    <script>let conId = "<?php echo base64_encode($conId) ?>";</script>
    <script src="js/global.js?<?php echo $timestamp ?>"></script>
</body>
</html>