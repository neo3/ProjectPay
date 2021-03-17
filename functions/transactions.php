<?php
	include_once("./../functions/database.php");
	include_once("./../class/Transacao.php");

	$lista_transacoes = Transacao::LoadUsuario();

	echo json_encode($lista_transacoes, JSON_INVALID_UTF8_IGNORE); 
?>