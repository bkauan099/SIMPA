<?php
session_start();

// 🔒 proteger sessão
session_regenerate_id(true);

// 🔒 validar método
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit();
}

// 🔒 sanitizar input
$usuario = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = trim($_POST['senha'] ?? '');

if (!$usuario || !$senha) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Preencha todos os campos."
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

if ($usuario == "admin@gmail.com" && $senha == "@admin123") {

    $_SESSION["usuario"] = $usuario;
    $_SESSION["tipo"] = "admin";

    echo json_encode([
        "status" => "ok",
        "redirect" => "adm-page.php"
    ]);

} elseif ($usuario == "professor@gmail.com" && $senha == "@professor123") {

    $_SESSION["usuario"] = $usuario;
    $_SESSION["tipo"] = "professor";

    echo json_encode([
        "status" => "ok",
        "redirect" => "professor-page.php"
    ]);

} elseif ($usuario == "aluno@gmail.com" && $senha == "@aluno123") {

    $_SESSION["usuario"] = $usuario;
    $_SESSION["tipo"] = "aluno";

    echo json_encode([
        "status" => "ok",
        "redirect" => "aluno-page.php"
    ]);

} else {

    echo json_encode([
        "status" => "erro",
        "mensagem" => "Usuário ou senha incorretos."
    ], JSON_UNESCAPED_UNICODE);
}

exit();