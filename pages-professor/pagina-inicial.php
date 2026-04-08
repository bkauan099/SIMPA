<?php
// pages-professor/pagina-inicial.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Caminho exato para o arquivo de conexão
// dirname(__DIR__) sobe para a pasta 'simpa', depois entra em 'backend/conexao/conexao.php'
$caminhoConexao = dirname(__DIR__) . '/backend/conexao/conexao.php';

if (file_exists($caminhoConexao)) {
    require_once $caminhoConexao;
} else {
    die("Erro Crítico: Arquivo de conexão NÃO encontrado em: " . $caminhoConexao);
}

// 2. Carrega o Controller
require_once dirname(__DIR__) . '/backend/controllers/controller-professor/dashboardController.php';

// 3. Inicia o Controller
// Lembre-se: no seu conexao.php a variável deve se chamar $pdo
if (isset($pdo)) {
    $controller = new DashboardController($pdo); 
    $controller->index();
} else {
    die("Erro: A variável \$pdo não foi encontrada no arquivo de conexão.");
}