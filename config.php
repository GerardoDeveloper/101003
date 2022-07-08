<?php
/**
 * Este script concentra todas las constantes que el bot necesita para su funcionamiento.
 */

/*==============================================================================================*/

// Constante que determina si el bot esta de producción o en desarrollo. True sí esta en producción, false sí esta en desarrollo.
const BOT_IN_PRODUCTION = true;

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
define("TABLE_NOMINA_DNI", "nomina_dni");
define("TABLE_FORM_CAMBIAR_BANCO", "formulario_cambiar_banco");
define("TABLE_HISTORIAL_NO_EXITOSAS", "historial_noexitosas");
define("TABLE_ENTRANTES", "entrantes");
define("TABLE_SOLICITUD_ADELANTOS", "formulario_solicitud_adelanto");

//==========================URLs FORMULARIOS============================
define("FORM_OTRAS_CONSULTAS", "https://labs357.com.ar/hilos/101003/webviews/otras_consultas/webview.php");
define("FORM_OTRAS_CONSULTAS_SOBRE_LICENCIAS", "https://labs357.com.ar/hilos/101003/webviews/otras_consultas_sobre_licencias/webview.php");
define("FORM_MIS_FICHADAS", "https://labs357.com.ar/hilos/101003/webviews/mis_fichadas/webview.php");
define("FORM_FICHADAS_DE_MI_EQUIPO", "https://labs357.com.ar/hilos/101003/webviews/fichadas_de_mi_equipo/webview.php");
define("FORM_CAMBIAR_BANCO", "https://labs357.com.ar/hilos/101003/webviews/cambiar_banco/webview.php");
define("FORM_SOLICITUD_ADELANTO", "https://labs357.com.ar/hilos/101003/webviews/solicitud_adelanto/webview.php");
define("FORM_LICENCIAS", "https://labs357.com.ar/hilos/101003/webviews/licencias/webview.php");

// Constante que trae las palabras claves desde la base de plataforma.
define("WITAI", "https://labs357.com.ar/witai/Keyword/");

// URL de archivos.
define("URL_FILES", "http://labs357.com.ar/files/");

// CallBack de plataforma de chatbotamerica.
define("CALLBACK_PLATAFORM_POST_CHATBOTAMERICA", "https://tv1.chatbotamerica.com/Home/CallBackPlataformPost");

// Destinatarios de correos para el cambio de bancos.
define("DESTINATARIOS_BANCO_PRODUCTION", "administracionch@sancorsalud.com.ar,arasosaguenaga@gmail.com");

// Destinatarios de solicitud de adelantos.
define("DESTINATARIOS_SOLICITUD_ADELANTOS_PRODUCTION", "matias.eniacgroup@gmail.com,arasosaguenaga@gmail.com,kevin.eniacgroup@gmail.com");

// Destinatarios de otras consultas internas.
define("DESTINATARIOS_OTRAS_CONSULTAS_INTERNAS", "matias.eniacgroup@gmail.com,arasosaguenaga@gmail.com,kevin.eniacgroup@gmail.com");

// Destinatario para desarrollo y testing.
define("DESTINATARIOS_DEVELOPER", "control@labs357.com");

// URL de AI.
define("URL_AI", "https://api.dialogflow.com/v1/query?");
