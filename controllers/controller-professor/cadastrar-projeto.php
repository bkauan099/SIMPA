<?php
session_start();
require_once '../../conexao/conexao.php';
require_once '../../model/Projeto.php';

// 1. Definição da função de formatação (Pode ficar no topo do arquivo)
function formatarDataParaBanco($data)
{
    if (empty($data)) return null;
    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return "{$partes[2]}-{$partes[1]}-{$partes[0]}"; // Transforma dd/mm/aaaa em aaaa-mm-dd
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projetoModel = new Projeto($pdo);

    // 2. Formatação das datas vindas do formulário
    // Isso garante que o SQL não dê erro de tipo de dado
    $_POST['data_inicio'] = formatarDataParaBanco($_POST['data_inicio']);
    $_POST['data_fim'] = formatarDataParaBanco($_POST['data_fim']);

    // 3. Pegar o ID real do professor logado (ou manter o 5 para teste se preferir)
    $id_usuario_logado = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 5;

    // Chamamos o método cadastrar passando os dados formatados
    if ($projetoModel->cadastrar($_POST, $id_usuario_logado)) {

        $origem = isset($_POST['pagina_origem']) ? $_POST['pagina_origem'] : 'meus-projetos';

        // Redireciona com a mensagem de sucesso
        header("Location: ../../professor-page.php?page=" . $origem . "&sucesso=1");
    } else {
        $origem = isset($_POST['pagina_origem']) ? $_POST['pagina_origem'] : 'meus-projetos';
        header("Location: ../../professor-page.php?page=" . $origem . "&erro=1");
    }
    exit;
}
