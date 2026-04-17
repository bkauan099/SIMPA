<?php
session_start();
require_once 'conexao/conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header("Location: login-page.php?erro=1");
    exit();
}

$stmt = $pdo->prepare("SELECT id_usuario, nome, perfil, senha FROM usuarios WHERE email = :email AND status = 'ativo'");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: login-page.php?erro=1");
    exit();
}

$senhaValida = password_verify($senha, $usuario['senha']) || $usuario['senha'] === $senha;

if (!$senhaValida) {
    header("Location: login-page.php?erro=1");
    exit();
}

$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nome_usuario'] = $usuario['nome'];
$_SESSION['perfil'] = $usuario['perfil'];

if ($usuario['perfil'] === 'admin') {
    header("Location: adm-page.php");
} else {
    header("Location: aluno-page.php");
}
exit();
?>
