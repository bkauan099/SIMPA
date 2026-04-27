<?php
session_start();
// Importa a conexão PDO e a sua Classe
require_once '../../conexao/conexao.php';
require_once '../../model/Projeto.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Instancia a classe Projeto passando a conexão $pdo
    $projetoModel = new Projeto($pdo);

    // 2. Chama o método cadastrar passando os dados do formulário ($_POST)
    if ($projetoModel->cadastrar($_POST)) {
        // Pega a página onde você estava antes de abrir o modal
        $origem = isset($_POST['pagina_origem']) ? $_POST['pagina_origem'] : 'pagina-inicial';

        // Redireciona para a mesma página mantendo o contexto
        header("Location: ../../adm-page.php?page=" . $origem . "&sucesso=1");
    } else {
        $origem = isset($_POST['pagina_origem']) ? $_POST['pagina_origem'] : 'pagina-inicial';
        header("Location: ../../adm-page.php?page=" . $origem . "&erro=1");
    }
    exit;
}
