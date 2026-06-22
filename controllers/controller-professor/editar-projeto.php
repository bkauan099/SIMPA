<?php
session_start();
require_once '../../conexao/conexao.php';
require_once '../../model/Projeto.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header("Location: ../../login-page.php");
    exit;
}

// Função que já usamos para converter a data do formulário (BR) para o banco (ISO)
function formatarDataParaBanco($data)
{
    if (empty($data)) return null;
    // Formato ISO yyyy-mm-dd (input type="date" nativo)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) return $data;
    // Formato dd/mm/yyyy (flatpickr legado)
    $partes = explode('/', $data);
    if (count($partes) == 3) return "{$partes[2]}-{$partes[1]}-{$partes[0]}";
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_projeto'])) {
    $projetoModel = new Projeto($pdo);

    // 1. Preparar os dados para o UPDATE
    $id_projeto = (int) $_POST['id_projeto'];

    // Garante que o professor logado realmente participa deste projeto
    // (evita que qualquer professor edite projeto de outro orientador)
    $stmtOwner = $pdo->prepare(
        "SELECT 1 FROM participacao WHERE id_projeto = :proj AND id_usuario = :uid LIMIT 1"
    );
    $stmtOwner->execute([':proj' => $id_projeto, ':uid' => $id_usuario]);
    if (!$stmtOwner->fetch()) {
        header("Location: ../../professor-page.php?page=meus-projetos&erro_edit=1");
        exit;
    }

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
