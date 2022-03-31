<?php
/**
 * Este script concentra todas las constantes que el bot necesita para su funcionamiento.
 */

const ARRAY_TIPO_CONSULTAS = array(
    1 => "Liquidación de sueldos / Impuesto a las ganancias",
    2 => "Envío o solicitud de documentación / Consultas por fichadas",
    3 => "Beneficios / Privilege",
    4 => "UNI",
    5 => "Programas de desarrollo/Desempeño",
    6 => "Otras consultas sobre licencias",
);

//============================TABLAS SQL================================
define("TABLE_FORMULRIO_OTRAS_CONSULTAS", "formulario_otras_consultas");

//==========================URLs FORMULARIOS============================
define("FORM_OTRAS_CONSULTAS", "https://labs357.com.ar/hilos/101003/webviews/otras_consultas/webview.php");