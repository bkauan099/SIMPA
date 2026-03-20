<?php
$usuario = $_POST["email"];
$senha = $_POST["senha"];

if($usuario == "admin@gmail.com" && $senha == "@admin123"){
    header("Location: adm-page.php");
    exit();
}else{
    header("Location: login-page.php?erro=1");
    exit();
}

?>