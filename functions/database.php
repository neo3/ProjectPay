<?php
	$database_connection_information = "
		define('DB_HOST','localhost');
		define('DB_USER','root');
		define('DB_PASS','');
		define('DB_BASE','ProjectPay');
		define('DIR_CLASS_NAME','class'); 
		define('DIR_CLASS',DIR_CLASS_NAME.'_'.DB_BASE);
	";

	eval($database_connection_information);

	$link = mysqli_connect(DB_HOST,DB_USER,DB_PASS)
	or die ("Falha na conexão com o Banco de Dados!");

	mysqli_query($link,"SET NAMES 'utf8'");
	mysqli_query($link,"SET character_set_connection=utf8");
	mysqli_query($link,"SET character_set_client=utf8");
	mysqli_query($link,"SET character_set_results=utf8");

	mysqli_select_db($link, DB_BASE)
	or die ("Não foi possível localizar o Banco de Dados!");

	if(is_resource($link)) mysqli_close($link);
?>