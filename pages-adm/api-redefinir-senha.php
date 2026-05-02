<?php
// pages-adm/api-redefinir-senha.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Captura erros PHP e devolve como JSON limpo
set_error_handler(function($errno, $errstr) {
    // Ignorar erros de mail() — tratamos manualmente
    if (str_contains($errstr, 'mail()') || str_contains($errstr, 'mailserver') || str_contains($errstr, 'SMTP')) {
        return true; // silencia o erro de SMTP
    }
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno. Tente novamente.']);
    exit;
});

require_once '../conexao/conexao.php';

$acao = $_POST['acao'] ?? '';

// ── PASSO 1: Verificar e-mail e gerar código ────────────────────────────────
if ($acao === 'verificar_email') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Informe um e-mail válido.']);
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT id_usuario, nome FROM usuarios WHERE email = :email AND CAST(status AS TEXT) = 'ativo' LIMIT 1"
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma conta ativa encontrada com este e-mail.']);
        exit;
    }

    // Gerar código de 6 dígitos
    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Salvar na sessão
    $_SESSION['reset_email']    = $email;
    $_SESSION['reset_codigo']   = $codigo;
    $_SESSION['reset_expira']   = $expira;
    $_SESSION['reset_nome']     = $usuario['nome'];
    $_SESSION['reset_id']       = $usuario['id_usuario'];
    $_SESSION['reset_validado'] = false;

    // ── Tentar enviar e-mail (sem deixar erro vazar) ────────────────────────
    $emailEnviado = false;
    try {
        $assunto = "SIMPA - Código de Verificação";
        $corpo   = "Olá, {$usuario['nome']}!\n\n"
                 . "Seu código de verificação para redefinição de senha é:\n\n"
                 . "  🔑 $codigo\n\n"
                 . "Válido por 15 minutos. Se não foi você, ignore este e-mail.\n\n"
                 . "— Equipe SIMPA / UEMA";
        $headers = "From: noreply@simpa.uema.br\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        
        // Suprimir qualquer warning/error do mail()
        $emailEnviado = @mail($email, $assunto, $corpo, $headers);
    } catch (Exception $ignored) {
        $emailEnviado = false;
    }

    if ($emailEnviado) {
        // E-mail enviado com sucesso
        echo json_encode([
            'sucesso'  => true,
            'mensagem' => "Código enviado para {$email}. Verifique sua caixa de entrada.",
            'nome'     => $usuario['nome'],
        ]);
    } else {
        // SMTP não configurado — exibir código diretamente na tela
        echo json_encode([
            'sucesso'    => true,
            'mensagem'   => "Servidor de e-mail não configurado. Use o código abaixo:",
            'nome'       => $usuario['nome'],
            'codigo_dev' => $codigo,
        ]);
    }
    exit;
}

// ── PASSO 2: Verificar código ───────────────────────────────────────────────
if ($acao === 'verificar_codigo') {
    $codigo = trim($_POST['codigo'] ?? '');

    if (empty($_SESSION['reset_codigo']) || empty($_SESSION['reset_expira'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Comece novamente.']);
        exit;
    }

    if (strtotime($_SESSION['reset_expira']) < time()) {
        unset($_SESSION['reset_codigo'], $_SESSION['reset_expira']);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Código expirado. Solicite um novo.']);
        exit;
    }

    if ($codigo !== $_SESSION['reset_codigo']) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Código incorreto. Tente novamente.']);
        exit;
    }

    $_SESSION['reset_validado'] = true;
    echo json_encode(['sucesso' => true, 'mensagem' => 'Código verificado!']);
    exit;
}

// ── PASSO 3: Salvar nova senha ──────────────────────────────────────────────
if ($acao === 'nova_senha') {
    if (empty($_SESSION['reset_validado']) || empty($_SESSION['reset_id'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão inválida. Comece novamente.']);
        exit;
    }

    $nova    = $_POST['nova_senha'] ?? '';
    $confirma= $_POST['confirma']   ?? '';

    if (strlen($nova) < 6) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A senha deve ter pelo menos 6 caracteres.']);
        exit;
    }
    if ($nova !== $confirma) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'As senhas não conferem.']);
        exit;
    }

    $hash = password_hash($nova, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id_usuario = :id");
    $ok   = $stmt->execute([':senha' => $hash, ':id' => (int)$_SESSION['reset_id']]);

    // Limpar sessão de redefinição
    foreach (['reset_email','reset_codigo','reset_expira','reset_nome','reset_id','reset_validado'] as $k) {
        unset($_SESSION[$k]);
    }

    echo json_encode([
        'sucesso'  => $ok,
        'mensagem' => $ok ? 'Senha redefinida com sucesso!' : 'Erro ao salvar senha. Tente novamente.',
    ]);
    exit;
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
?>
