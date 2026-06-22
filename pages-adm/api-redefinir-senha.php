<?php
ob_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
session_start();
header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($errno, $errstr, $file, $line) {
    if (in_array($errno, [E_NOTICE, E_WARNING, E_DEPRECATED])) return true;
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno.']);
    exit;
});

require_once '../conexao/conexao.php';
require_once '../lib/Mailer.php';

$acao = $_POST['acao'] ?? '';

// ── PASSO 1: Verificar e-mail ───────────────────────────────────────────────
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

    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $_SESSION['reset_email']    = $email;
    $_SESSION['reset_codigo']   = $codigo;
    $_SESSION['reset_expira']   = $expira;
    $_SESSION['reset_nome']     = $usuario['nome'];
    $_SESSION['reset_id']       = $usuario['id_usuario'];
    $_SESSION['reset_validado'] = false;

    // ── Tentar enviar e-mail via SMTP ─────────────────────────────────────
    $mailer = new Mailer();

    $texto = "Olá, {$usuario['nome']}!\n\n"
           . "Recebemos uma solicitação de redefinição de senha para sua conta no SIMPA.\n\n"
           . "Seu código de verificação é:\n\n"
           . "  🔑  $codigo\n\n"
           . "Este código é válido por 15 minutos.\n"
           . "Se você não solicitou a redefinição de senha, ignore este e-mail.\n\n"
           . "Equipe SIMPA — UEMA ProExae";

    $html = "
    <div style='font-family:Arial,sans-serif;max-width:480px;margin:0 auto;background:#f8fafc;padding:24px;border-radius:12px;'>
        <div style='background:#2B3C50;padding:20px;border-radius:8px 8px 0 0;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:1.2rem;'>🔒 Redefinição de Senha</h2>
            <p style='color:#94a3b8;margin:6px 0 0;font-size:.85rem;'>SIMPA — Sistema Integrado de Monitoramento de Projetos</p>
        </div>
        <div style='background:#fff;padding:28px;border-radius:0 0 8px 8px;border:1px solid #e2e8f0;'>
            <p style='color:#374151;'>Olá, <strong>{$usuario['nome']}</strong>!</p>
            <p style='color:#64748b;font-size:.9rem;'>Recebemos uma solicitação de redefinição de senha. Use o código abaixo:</p>
            <div style='background:#f1f5f9;border-radius:10px;padding:20px;text-align:center;margin:20px 0;'>
                <div style='letter-spacing:12px;font-size:2.2rem;font-weight:700;color:#2B3C50;font-family:monospace;'>{$codigo}</div>
                <p style='color:#94a3b8;font-size:.78rem;margin:8px 0 0;'>Válido por 15 minutos</p>
            </div>
            <p style='color:#94a3b8;font-size:.8rem;'>Se você não solicitou a redefinição, ignore este e-mail. Sua senha permanece inalterada.</p>
        </div>
        <p style='text-align:center;color:#94a3b8;font-size:.75rem;margin-top:16px;'>SIMPA — UEMA ProExae</p>
    </div>";

    $resultado = $mailer->enviar($email, "SIMPA — Código de Verificação", $texto, $html);

    if ($resultado === true) {
        echo json_encode([
            'sucesso'  => true,
            'mensagem' => "Código enviado para {$email}. Verifique sua caixa de entrada (e a pasta de spam).",
        ]);
    } else {
        // SMTP não configurado ou falhou — NUNCA expor o código na resposta:
        // isso permitiria sequestrar qualquer conta sem acesso ao e-mail.
        // O código fica só no log do servidor para o ADM recuperar manualmente.
        error_log("SIMPA reset-senha: SMTP indisponível ({$resultado}) — código para {$email}: {$codigo}");
        echo json_encode([
            'sucesso'  => true,
            'mensagem' => "Não foi possível enviar o e-mail agora. Tente novamente em alguns minutos ou contate o suporte.",
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
        foreach (['reset_codigo','reset_expira','reset_tentativas'] as $k) unset($_SESSION[$k]);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Código expirado. Solicite um novo.']);
        exit;
    }

    // Rate limiting — no máximo 5 tentativas por código, senão invalida e exige novo envio
    $_SESSION['reset_tentativas'] = ($_SESSION['reset_tentativas'] ?? 0) + 1;
    if ($_SESSION['reset_tentativas'] > 5) {
        foreach (['reset_codigo','reset_expira','reset_tentativas'] as $k) unset($_SESSION[$k]);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Muitas tentativas incorretas. Solicite um novo código.']);
        exit;
    }

    if ($codigo !== $_SESSION['reset_codigo']) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Código incorreto. Tente novamente.']);
        exit;
    }
    unset($_SESSION['reset_tentativas']);
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
    if (strlen($nova) < 6) { echo json_encode(['sucesso' => false, 'mensagem' => 'Senha deve ter pelo menos 6 caracteres.']); exit; }
    if ($nova !== $confirma) { echo json_encode(['sucesso' => false, 'mensagem' => 'As senhas não conferem.']); exit; }

    $hash = password_hash($nova, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id_usuario = :id");
    $ok   = $stmt->execute([':senha' => $hash, ':id' => (int)$_SESSION['reset_id']]);

    foreach (['reset_email','reset_codigo','reset_expira','reset_nome','reset_id','reset_validado','reset_tentativas'] as $k) unset($_SESSION[$k]);

    echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Senha redefinida com sucesso!' : 'Erro ao salvar.']);
    exit;
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
?>
