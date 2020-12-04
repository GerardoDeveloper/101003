<?php
	define( "DB_HOST", "127.0.0.1" );
	define( "DB_NAME", "_101003" );
	define( "DB_USER", "root" );
	define( "DB_PASSWORD", "homero" );
	
	define( "LABS_CUENTA", "101003");
	
	define( "FB_TOKEN", "");
	
	define( "TB_FORMULARIO", "formulario_cambiar_banco");

	
	$codes = Array (
		"200" => Array (
			"message" => "Envio cambio banco correctamente",
			"payload" => "_form enviado 1"
		)
	);
	define( "STATES_CODES", json_encode($codes));
?>