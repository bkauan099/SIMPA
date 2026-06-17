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
    $id_projeto = $_POST['id_projeto'] ?? null;
    $descricao  = $_POST['descricao']  ?? '';

    if (!$id_projeto || !isset($_FILES['arquivo'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
        exit;
    }

    // Obter matrícula do professor para organizar a pasta
    $stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
    $stmtMat->execute([':id' => $id_professor]);
    $matricula = $stmtMat->fetchColumn();
    if (!$matricula) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Professor não encontrado.']);
        exit;
    }

    $arquivo      = $_FILES['arquivo'];
    $nomeOriginal = $arquivo['name'];
    $extensao     = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    $permitidos = ['pdf', 'doc', 'docx', 'jpg', 'png'];
    if (!in_array($extensao, $permitidos)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de arquivo não permitido.']);
        exit;
    }

    $diretorioDestino = "../../uploads/producoes/professor/" . $matricula . "/";

    if (!is_dir($diretorioDestino)) {
        mkdir($diretorioDestino, 0755, true);
    }

    $nomeNoServidor   = bin2hex(random_bytes(16)) . "." . $extensao;
    $caminhoFinal     = $diretorioDestino . $nomeNoServidor;
    $caminhoParaBanco = "uploads/producoes/professor/" . $matricula . "/" . $nomeNoServidor;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
        try {
            $titulo = !empty($_POST['titulo']) ? $_POST['titulo'] : $nomeOriginal;

            $sql = "INSERT INTO producoes (id_projeto, tipo, caminho, titulo, status, data_registro)
                VALUES (:id_projeto, :nome_orig, :caminho, :titulo, 'pendente', NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_projeto' => $id_projeto,
                ':nome_orig'  => $nomeOriginal,
                ':caminho'    => $caminhoParaBanco,
                ':titulo'     => $titulo,
            ]);

            echo json_encode(['sucesso' => true]);
        } catch (PDOException $e) {
            if (file_exists($caminhoFinal)) unlink($caminhoFinal);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Falha ao salvar o arquivo.']);
    }
}
