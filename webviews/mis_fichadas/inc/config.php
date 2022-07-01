<?php
if (file_exists(__DIR__ . "/../../../config.php")) {
    require_once __DIR__ . "/../../../config.php";
}

if (BOT_IN_PRODUCTION) {
    define("DB_HOST", "127.0.0.1");
    define("DB_NAME", "_101003");
    define("DB_USER", "root");
    define("DB_PASSWORD", "homero");
} else {
    define("DB_HOST", "127.0.0.1");
    define("DB_NAME", "_101003");
    define("DB_USER", "root");
    define("DB_PASSWORD", "");
}


define("LABS_CUENTA", "101003");

define("FB_TOKEN", "");

define("TB_FORMULARIO", "formulario_mis_fichadas");
define("TB_MIS_FICHADAS", "mis_fichadas");
define("TB_DETALLE_MIS_FICHADAS", "detalle_mis_fichadas");

$codes = array(
    "200" => array(
        "message" => "Envio consulta mis fichadas",
        "payload" => "_mis fichadas",
    ),
);

define("STATES_CODES", json_encode($codes));
