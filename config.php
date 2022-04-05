<?php
/**
 * Este script concentra todas las constantes que el bot necesita para su funcionamiento.
 */

/**
 * Contiene todos los destinatarios del formulario.
 *
 * * NOTA: Los asuntos se cargan dinámicamente.
 */
const ARRAY_DESTINATARIOS = array(
    1 => array(
        // Liquidación de sueldos / Impuesto a las ganancias
        "asunto" => "",
        "destinatarios" => "liquidacionch@sancorsalud.com.ar, control@labs357.com",
    ),
    2 => array(
        // Envío o solicitud de documentación / Consultas por fichadas
        "asunto" => "",
        "destinatarios" => "administracionch@sancorsalud.com.ar, control@labs357.com",
    ),
    3 => array(
        // Beneficios / Privilege
        "asunto" => "",
        "destinatarios" => "privilege@sancorsalud.com.ar, control@labs357.com",
    ),
    4 => array(
        // UNI
        "asunto" => "",
        "destinatarios" => "uni@sancorsalud.com.ar, control@labs357.com",
    ),
    5 => array(
        // Programas de desarrollo/Desempeño
        "asunto" => "",
        "destinatarios" => "desarrolloch@sancorsalud.com.ar, control@labs357.com",
    ),
);

// Este array contiene todos los destinatarios del botón 'Otras consultas sobre licencias'
const ARRAY_DESTINATARIOS_OTRAS_CONSULTAS_SOBRE_LICENCIAS = array(
    "asunto" => "Otras consultas sobre licencias",
    "destinatarios" => "administracionch@sancorsalud.com.ar, control@labs357.com",
);

//============================TABLAS SQL================================
define("TABLE_FORMULRIO_OTRAS_CONSULTAS", "formulario_otras_consultas");
define("TABLE_FORMULRIO_OTRAS_CONSULTAS_SOBRE_LICENCIAS", "formulario_otras_consultas_sobre_licencias");
define("TABLE_FORMULRIO_TIPO_CONSULTAS", "tipo_consultas");
define("TABLE_CD_EMAIL", "cd_mails");


//==========================URLs FORMULARIOS============================
define("FORM_OTRAS_CONSULTAS", "https://labs357.com.ar/hilos/101003/webviews/otras_consultas/webview.php");
define("FORM_OTRAS_CONSULTAS_SOBRE_LICENCIAS", "https://labs357.com.ar/hilos/101003/webviews/otras_consultas_sobre_licencias/webview.php");
