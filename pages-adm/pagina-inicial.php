<?php

require_once __DIR__ . '/../backend/conexao/conexao.php';
require_once __DIR__ . '/../backend/controllers/controller-adm/dashboardController.php';

$controller = new DashboardController($pdo);
$controller->index();

?>