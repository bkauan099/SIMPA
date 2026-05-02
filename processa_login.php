<?php
session_start();

require_once 'conexao/conexao.php';

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header("Location: login-page.php?erro=1");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id_usuario, nome, email, senha, CAST(perfil AS TEXT) AS perfil, CAST(status AS TEXT) AS status FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['status'] === 'ativo' && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id_usuario']  = $usuario['id_usuario'];
        $_SESSION['nome']        = $usuario['nome'];
        $_SESSION['email']       = $usuario['email'];
        $_SESSION['perfil']      = $usuario['perfil'];

        // Registrar acesso com sucesso na tabela acessos
        try {
            $ins = $pdo->prepare("INSERT INTO acessos (id_usuario, email, status) VALUES (:id_usuario, :email, 'sucesso')");
            $ins->execute([':id_usuario' => $usuario['id_usuario'], ':email' => $email]);
        } catch (Exception $ignored) {}

        // Redirecionar por perfil
        $perfil = strtolower($usuario['perfil']);
        if (str_contains($perfil, 'admin')) {
            header("Location: adm-page.php");
        } elseif (str_contains($perfil, 'professor') || str_contains($perfil, 'orientador')) {
            header("Location: professor-page.php");
        } else {
            header("Location: aluno-page.php");
        }
        exit();

    } else {
        // Registrar falha
        try {
            $ins = $pdo->prepare("INSERT INTO acessos (id_usuario, email, status) VALUES (:id, :email, 'falha')");
            $id_para_log = $usuario ? $usuario['id_usuario'] : null;
            $ins->execute([':id' => $id_para_log, ':email' => $email]);
        } catch (Exception $ignored) {}

        header("Location: login-page.php?erro=1");
        exit();
    }
} catch (Exception $e) {
    header("Location: login-page.php?erro=2");
    exit();
}
?>
