<?php
session_start();
require_once '../../conexao/conexao.php';
require_once '../../model/Projeto.php';

/**
 * Converte data de dd/mm/aaaa para aaaa-mm-dd (formato do banco)
 */
function formatarDataParaBanco($data)
{
    if (empty($data)) return null;
    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return "{$partes[2]}-{$partes[1]}-{$partes[0]}";
    }
    return null;
}

// Define que a resposta deste arquivo será sempre um JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $projetoModel = new Projeto($pdo);

        // Formatação das datas para evitar erro de tipo no SQL
        $dados = $_POST;
        $dados['data_inicio'] = formatarDataParaBanco($dados['data_inicio'] ?? '');
        $dados['data_fim'] = formatarDataParaBanco($dados['data_fim'] ?? '');

        // Identifica o usuário (Professor)
        $id_usuario_logado = $_SESSION['id_usuario'] ?? null;

        // Executa o cadastro
        if ($projetoModel->cadastrar($dados, $id_usuario_logado)) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Projeto cadastrado com sucesso!'
            ]);
        } else {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro interno ao salvar no banco de dados.'
            ]);
        }
    } catch (Exception $e) {
        // Captura erros inesperados e envia como JSON
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro no servidor: ' . $e->getMessage()
        ]);
    }
    exit;
} else {
    // Caso tentem acessar o arquivo diretamente via URL
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método de requisição inválido.'
    ]);
    exit;
}
