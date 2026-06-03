<?php
session_start();
require_once '../../conexao/conexao.php';
require_once '../../model/Projeto.php';

// Função que já usamos para converter a data do formulário (BR) para o banco (ISO)
function formatarDataParaBanco($data)
{
    if (empty($data)) return null;
    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return "{$partes[2]}-{$partes[1]}-{$partes[0]}";
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_projeto'])) {
    $projetoModel = new Projeto($pdo);

    // 1. Preparar os dados para o UPDATE
    $id_projeto = $_POST['id_projeto'];
    $dados = [
        'titulo'        => $_POST['titulo'],
        'id_tipo'       => $_POST['id_tipo'],
        'area'          => $_POST['area'],
        'descricao'     => $_POST['descricao'],
        'data_inicio'   => formatarDataParaBanco($_POST['data_inicio']),
        'data_fim'      => formatarDataParaBanco($_POST['data_fim']),
        'carga_horaria' => $_POST['carga_horaria']
    ];

    // 2. Chamar o método de editar no Model (vamos precisar criar esse método abaixo)
    if ($projetoModel->editar($id_projeto, $dados)) {
        $origem = $_POST['pagina_origem'] ?? 'meus-projetos';
        header("Location: ../../professor-page.php?page=" . $origem . "&sucesso_edit=1");
    } else {
        header("Location: ../../professor-page.php?page=meus-projetos&erro_edit=1");
    }
    exit;
}
