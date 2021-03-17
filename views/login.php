<?php
    if(!defined('IndexPage'))
        header('Location:./');
?>
<!DOCTYPE html>
<html>
<head>
	<link href="./css/login.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="box"></div>
        <div class="container-forms">
            <form action="./functions/connect.php" method="POST">
                <input type="checkbox" id="type" name="type">
                <div class="container-form">
                    <div class="form-item log-in">
                        <div class="table">
                            <div class="table-cell">
                                <h3>Acessar Conta</h3>
                                <input id="email" name="email" placeholder="E-mail" type="text" />
                                <input id="password" name="password" placeholder="Senha" type="Password" />
                                <p class="warning">
                                    <?php
                                        if(isset($_SESSION['warning'])){
                                            echo $_SESSION['warning'];
                                            unset($_SESSION['warning']);
                                        }
                                    ?>
                                </p>
                                <div class="row">
                                    <div class="col col-md-12 align-self-center">
                                        <button type="submit" class="btn">Entrar</button>
                                        <label for="type"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>