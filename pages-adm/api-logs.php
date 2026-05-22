<?php
ob_start();

// Limpar qualquer output acidental e enviar JSON limpo
function enviarJSON(array $dados): void {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

// Silenciar warnings sem esconder erros graves
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

require_once '../lib/Guard.php';
Guard::apenasAdmin();

require_once '../conexao/conexao.php';
require_once '../lib/Logger.php';

try {
    Logger::setPDO($pdo);
} catch (Exception $e) {
    enviarJSON(['erro' => 'Falha na conexão: ' . $e->getMessage()]);
}

$acao = $_GET['acao'] ?? 'listar';

// ── LISTAR ───────────────────────────────────────────────────────────────────
if ($acao === 'listar') {
    $limite = min((int)($_GET['limite'] ?? 100), 500);
    $offset = (int)($_GET['offset'] ?? 0);

    $filtros = [
        'modulo'      => trim($_GET['modulo']      ?? ''),
        'acao'        => trim($_GET['acao_filtro'] ?? ''),
        'busca'       => trim($_GET['busca']       ?? ''),
        'data_inicio' => trim($_GET['data_inicio'] ?? ''),
        'data_fim'    => trim($_GET['data_fim']    ?? ''),
        'id_usuario'  => trim($_GET['id_usuario']  ?? ''),
    ];

    try {
        $logs  = Logger::buscar($filtros, $limite, $offset);
        $total = Logger::total($filtros);
        $stats = Logger::estatisticas();

        enviarJSON([
            'sucesso'      => true,
            'logs'         => $logs,
            'total'        => $total,
            'estatisticas' => $stats,
        ]);
    } catch (Exception $e) {
        enviarJSON([
            'sucesso'      => false,
            'mensagem'     => 'Erro ao buscar logs: ' . $e->getMessage(),
            'dica'         => 'Verifique se a tabela logs_auditoria foi criada no Supabase.',
            'logs'         => [],
            'total'        => 0,
            'estatisticas' => [],
        ]);
    }
}

// ── EXPORTAR CSV ─────────────────────────────────────────────────────────────
elseif ($acao === 'exportar') {
    $filtros = [
        'modulo'      => trim($_GET['modulo']      ?? ''),
        'busca'       => trim($_GET['busca']       ?? ''),
        'data_inicio' => trim($_GET['data_inicio'] ?? ''),
        'data_fim'    => trim($_GET['data_fim']    ?? ''),
    ];

    try {
        $logs = Logger::buscar($filtros, 1000, 0);
        ob_end_clean();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="simpa-logs-' . date('Y-m-d') . '.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Data/Hora','Módulo','Ação','Descrição','Usuário','IP','Contexto'], ';');
        foreach ($logs as $log) {
            fputcsv($out, [
                $log['id'], $log['data_fmt'], $log['modulo'], $log['acao'],
                $log['descricao'], $log['nome_usuario'], $log['ip'],
                $log['contexto'] ? json_encode($log['contexto'], JSON_UNESCAPED_UNICODE) : '',
            ], ';');
        }
        fclose($out);
    } catch (Exception $e) {
        enviarJSON(['erro' => 'Erro ao exportar: ' . $e->getMessage()]);
    }
    exit;

} else {
    enviarJSON(['erro' => 'Ação inválida.']);
}
?>
