<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_projeto = $_POST['id_projeto'] ?? null;
    $descricao = $_POST['descricao'] ?? '';

    if (!$id_projeto || !isset($_FILES['arquivo'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
        exit;
    }

    $arquivo = $_FILES['arquivo'];
    $nomeOriginal = $arquivo['name'];
    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    // Extensões permitidas
    $permitidos = ['pdf', 'doc', 'docx', 'jpg', 'png'];
    if (!in_array($extensao, $permitidos)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de arquivo não permitido.']);
        exit;
    }

    // Caminho da raiz do projeto para o upload
    $diretorioDestino = "../../uploads/documentos/";

    // Garante que a pasta existe
    if (!is_dir($diretorioDestino)) {
        mkdir($diretorioDestino, 0777, true);
    }

    $nomeNoServidor = uniqid() . "." . $extensao;
    $caminhoFinal = $diretorioDestino . $nomeNoServidor;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
        try {
            $sql = "INSERT INTO documentos_projeto (id_projeto, nome_original, caminho_arquivo, descricao) 
                    VALUES (:id_projeto, :nome, :caminho, :descricao)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_projeto' => $id_projeto,
                ':nome' => $nomeOriginal,
                ':caminho' => $nomeNoServidor,
                ':descricao' => $descricao
            ]);

            echo json_encode(['sucesso' => true]);
        } catch (PDOException $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao mover o arquivo para a pasta.']);
    }
}
