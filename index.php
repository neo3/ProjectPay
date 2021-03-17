<!DOCTYPE html>
<html>
<head>
	<?php 
		header("Expires: 0");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		session_start();
	?>
	<title>ProjectPay</title>
	<link rel="shortcut icon" href="./img/logo.png" type="image/x-icon"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="./css/bootstrap.min.css" rel="stylesheet">
	<link href="./css/datatables.min.css" rel="stylesheet">
	<link href="./css/font-awesome.min.css" rel="stylesheet">
	<script type="text/javascript" src="./js/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="./js/jquery.mask.min.js"></script>
	<script type="text/javascript" src="./js/bootstrap.min.js"></script>
	<script type="text/javascript" src="./js/datatables.min.js"></script>
	<script type="text/javascript" src="./js/bootbox.js"></script>
</head>
<body>
    <?php
		define('IndexPage', TRUE);
		
        if(isset($_SESSION['id'])) include("./views/admin.php");
        else include("./views/login.php");
    ?>
</body>
</html>