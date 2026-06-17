<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json');

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_documento = $_POST['id_documento'] ?? null;

    if (!$id_documento) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID do documento não fornecido.']);
        exit;
    }

    try {
        $sqlBusca = "SELECT caminho FROM producoes WHERE id_producao = :id";
        $stmtBusca = $pdo->prepare($sqlBusca);
        $stmtBusca->execute([':id' => $id_documento]);
        $doc = $stmtBusca->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            // caminho no banco já inclui "uploads/..." — usa diretamente a partir da raiz do projeto
            $baseDir     = realpath(__DIR__ . '/../../uploads');
            $caminhoFisico = realpath(__DIR__ . '/../../' . $doc['caminho']);

            $sqlDelete = "DELETE FROM producoes WHERE id_producao = :id";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([':id' => $id_documento]);

            if ($caminhoFisico && $baseDir && str_starts_with($caminhoFisico, $baseDir) && file_exists($caminhoFisico)) {
                unlink($caminhoFisico);
            }

            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Documento não encontrado.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir: ' . $e->getMessage()]);
    }
}
