<?php
const IN_PRODUCTION = true;

if (IN_PRODUCTION) {
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

define("TB_FORMULARIO", "formulario_otras_consultas_sobre_licencias");

$codes = array(
    "200" => array(
        "message" => "Envio de consultas sobre licencias",
        "payload" => "_otras consultas sobre licencias formulario",
    ),
);
define("STATES_CODES", json_encode($codes));
