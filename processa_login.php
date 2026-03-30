<?php
$usuario = $_POST["email"];
$senha = $_POST["senha"];

if($usuario == "admin@gmail.com" && $senha == "@admin123"){
    header("Location: adm-page.php");
    exit();
}elseif($usuario == "professor@gmail.com" && $senha == "@professor123"){
    header("Location: professor-page.php");
    exit();
}elseif($usuario == "aluno@gmail.com" && $senha == "@aluno123"){
    header("Location: aluno-page.php");
    exit();
}else{
    header("Location: login-page.php?erro=1");
    exit();
}

?>