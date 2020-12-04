<?php
	define( "DB_HOST", "127.0.0.1" );
	define( "DB_NAME", "_101003" );
	define( "DB_USER", "root" );
	define( "DB_PASSWORD", "homero" );
	
	define( "LABS_CUENTA", "101003");
	
	define( "FB_TOKEN", "");
	
	define( "TB_FORMULARIO", "formulario_licencias");
	define( "TB_TIPO_LICENCIA", "tipo_licencia");
	define( "TB_DETALLE_LICENCIA", "detalle_licencia");
	
	$codes = Array (
		"200" => Array (
			"message" => "Envio consulta licencias",
			"payload" => ""
		)
	);
	define( "STATES_CODES", json_encode($codes));
?>