<?php
    include_once('database.php');
	include("./../class/UsuarioPadrao.php");
	include("./../class/UsuarioLojista.php");
	
	session_start();
	
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	if(isset($_POST['type']))
		$type = $_POST['type'];
	else
		$type = 'off';	

	if ($type == 'on')
		$user = new UsuarioLojista('', $email, $password);
	else
		$user = new UsuarioPadrao('', $email, $password);
	
	if($user->getId() != '') {
		$_SESSION['id'] = $user->getId();

		if ($type == 'on')
			$_SESSION['name'] = $user->getRazaoSocial();
		else
			$_SESSION['name'] = $user->getNomeCompleto();

		$_SESSION['type'] = $type;	
	}
	else {
		unset($_SESSION['id']);
		unset($_SESSION['name']);
		unset($_SESSION['type']);
		
		$_SESSION['warning'] = "Dados inválidos.";
	}

	mysqli_close($link);
	
	header("Location: ./../");
?>