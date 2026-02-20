<?php
$usuario = $_POST["email"];
$senha = $_POST["senha"];

if($usuario == "admin@gmail.com" && $senha == "123456"){
    header("Location: teste.php");
    exit();
}else{
    header("Location: login-page.php?erro=1");
    exit();
}

?>