<?php
session_start();

$usuario = $_POST["email"];
$senha = $_POST["senha"];

if($usuario == "admin@gmail.com" && $senha == "@admin123"){

    $_SESSION["usuario"] = $usuario;
    $_SESSION["tipo"] = "admin";

    header("Location: adm-page.php");
    exit();

}elseif($usuario == "professor@gmail.com" && $senha == "@professor123"){

    $_SESSION["usuario"] = $usuario;
    $_SESSION["tipo"] = "professor";

    header("Location: professor-page.php");
    exit();

}elseif($usuario == "aluno@gmail.com" && $senha == "@aluno123"){

    $_SESSION["usuario"] = $usuario;
    $_SESSION["tipo"] = "aluno";

    header("Location: aluno-page.php");
    exit();

}else{
    header("Location: login-page.php?erro=1");
    exit();
}