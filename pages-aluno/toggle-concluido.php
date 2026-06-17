<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../model/Aluno.php';

header('Content-Type: application/json');

if (!$id_usuario) {
    echo json_encode(['ok' => false, 'erro' => 'Sessão expirada.']);
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['ok' => false, 'erro' => 'ID ausente']);
    exit;
}

try {
    // Bloquear desfazer se prazo passou (data + hora)
    $stmt = $pdo->prepare("SELECT concluido::int AS concluido, data, hora, id_projeto, titulo FROM agenda_items WHERE id = :id AND id_usuario = :uid");
    $stmt->execute([':id' => $id, ':uid' => $id_usuario]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item && (int)$item['concluido'] === 1) {
        $prazoPassou = $item['data'] < date('Y-m-d');
        if (!$prazoPassou && !empty($item['hora'])) {
            $prazoComHora = new DateTime($item['data'] . ' ' . substr($item['hora'], 0, 5));
            $prazoPassou  = new DateTime() > $prazoComHora;
        }
        if ($prazoPassou) {
            echo json_encode(['ok' => false, 'erro' => 'Prazo encerrado. Não é possível desfazer.']);
            exit;
        }

        // Bloquear desfazer se professor já avaliou o documento
        if ($item['id_projeto']) {
            $stmtDoc = $pdo->prepare("
                SELECT status FROM producoes
                WHERE id_projeto = :proj AND titulo = :titulo AND status != 'inativo'
                ORDER BY id_producao DESC LIMIT 1
            ");
            $stmtDoc->execute([':proj' => $item['id_projeto'], ':titulo' => $item['titulo']]);
            $statusDoc = $stmtDoc->fetchColumn();
            if (in_array($statusDoc, ['concluido', 'cancelado'])) {
                echo json_encode(['ok' => false, 'erro' => 'Esta atividade já foi avaliada pelo professor.']);
                exit;
            }
        }
    }

    $aluno = new Aluno($pdo);
    $novoConcluido = $aluno->toggleConcluido($id, $id_usuario);
    echo json_encode(['ok' => true, 'concluido' => $novoConcluido]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erro' => htmlspecialchars($e->getMessage())]);
}
