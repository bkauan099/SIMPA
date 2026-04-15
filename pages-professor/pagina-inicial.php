<?php
// pages-professor/pagina-inicial.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Caminho exato para o arquivo de conexão
require_once dirname(__DIR__) . '/conexao/conexao.php';

// 2. Carrega o Controller
require_once dirname(__DIR__) . '/controllers/controller-professor/dashboardController.php';

// 3. Inicia o Controller

$controller = new DashboardController($pdo);
$controller->index();