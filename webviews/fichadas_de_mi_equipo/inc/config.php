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

define("TB_FORMULARIO", "formulario_fichadas_de_mi_equipo");
define("TB_FICHADAS_DE_MI_EQUIPO", "fichadas_de_mi_equipo");
define("TB_DETALLE_FICHADAS_DE_MI_EQUIPO", "detalle_fichadas_de_mi_equipo");

$codes = array(
    "200" => array(
        "message" => "Envio consulta fichadas de mi equipo",
        "payload" => "_fichada de mi equipo",
    ),
);

define("STATES_CODES", json_encode($codes));
