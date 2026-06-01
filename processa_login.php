<?php
session_start();
require_once 'conexao/conexao.php';
require_once 'lib/Logger.php';
Logger::setPDO($pdo);

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha e-mail e senha.']);
    exit();
}

/**
 * Descobre os valores reais do ENUM status_acesso diretamente do Supabase
 * e devolve o valor correto para 'sucesso' ou 'falha'.
 * Usa cache de sessão para não repetir a query toda requisição.
 */
function resolverStatusAcesso(PDO $pdo, string $tipo): string {
    // Cache na sessão para não consultar o banco toda vez
    if (!isset($_SESSION['_enum_status_acesso'])) {
        $_SESSION['_enum_status_acesso'] = [];

        try {
            // Lê os valores do ENUM diretamente do schema do PostgreSQL
            $rows = $pdo->query("
                SELECT enumlabel
                FROM pg_enum e
                JOIN pg_type t ON e.enumtypid = t.oid
                WHERE t.typname = 'status_acesso'
                ORDER BY e.enumsortorder
            ")->fetchAll(PDO::FETCH_COLUMN);

            $_SESSION['_enum_status_acesso'] = $rows;
        } catch (Exception $e) {
            // Se não conseguiu ler o schema, usa fallback
        }
    }

    $valores = $_SESSION['_enum_status_acesso'];

    if (empty($valores)) {
        // Fallback: tenta valores comuns
        return $tipo === 'sucesso' ? 'sucesso' : 'falha';
    }

    // Procura o valor que corresponde ao tipo pelo conteúdo (case-insensitive)
    $padroesSucesso = ['sucesso', 'success', 'ok', 'bem', 'valid'];
    $padroesFalha   = ['falha', 'fail', 'failure', 'erro', 'error', 'inval', 'negad'];
    $padroes = $tipo === 'sucesso' ? $padroesSucesso : $padroesFalha;

    foreach ($valores as $v) {
        $vLower = strtolower($v);
        foreach ($padroes as $p) {
            if (str_contains($vLower, $p)) return $v; // retorna o valor EXATO do ENUM
        }
    }

    // Se não achou por padrão, usa o primeiro (sucesso) ou segundo (falha) valor do ENUM
    return $tipo === 'sucesso' ? ($valores[0] ?? 'sucesso') : ($valores[1] ?? $valores[0] ?? 'falha');
}

/**
 * Insere o registro de acesso usando o valor correto do ENUM.
 */
function registrarAcesso(PDO $pdo, $id_usuario, string $email, string $tipo): void {
    try {
        $statusValor = resolverStatusAcesso($pdo, $tipo);
        $pdo->prepare(
            "INSERT INTO acessos (id_usuario, email, status) VALUES (:id, :email, :status)"
        )->execute([
            ':id'     => $id_usuario,
            ':email'  => $email,
            ':status' => $statusValor,
        ]);
    } catch (Exception $e) {
        // Não quebrar o login por falha no log — silencia
    }
}

// ── FLUXO PRINCIPAL ───────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare(
        "SELECT id_usuario, nome, email, senha,
                CAST(perfil AS TEXT) AS perfil,
                CAST(status AS TEXT) AS status
         FROM usuarios
         WHERE email = :email
         LIMIT 1"
    );
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');

    // ── LOGIN VÁLIDO ──────────────────────────────────────────────────────────
    if ($u && $u['status'] === 'ativo' && password_verify($senha, $u['senha'])) {

        $_SESSION['id_usuario'] = $u['id_usuario'];
        $_SESSION['nome']       = $u['nome'];
        $_SESSION['email']      = $u['email'];
        $_SESSION['perfil']     = $u['perfil'];

        registrarAcesso($pdo, $u['id_usuario'], $email, 'sucesso');
        Logger::registrar(Logger::LOGIN, Logger::LOGIN_OK, "Login bem-sucedido: \"{$u['nome']}\" ({$u['email']})");

        $p = strtolower($u['perfil']);
        if (str_contains($p, 'admin'))
            $redirect = 'adm-page.php';
        elseif (str_contains($p, 'professor') || str_contains($p, 'orientador'))
            $redirect = 'professor-page.php';
        else
            $redirect = 'aluno-page.php';

        echo json_encode(['status' => 'ok', 'redirect' => $redirect]);
        exit();
    }

    // ── LOGIN INVÁLIDO ────────────────────────────────────────────────────────
    $idParaLog = ($u && isset($u['id_usuario'])) ? $u['id_usuario'] : null;
    registrarAcesso($pdo, $idParaLog, $email, 'falha');
    Logger::registrar(Logger::LOGIN, Logger::LOGIN_FALHA, "Tentativa de login falha para e-mail: \"{$email}\"", ['id_usuario' => $idParaLog ?? 'desconhecido']);

    echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail ou senha incorretos.']);
    exit();

} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno. Tente novamente.']);
    exit();
}
?>
