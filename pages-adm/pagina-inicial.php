<?php

require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../controllers/controller-adm/dashboardController.php';

$controller = new DashboardController($pdo);
$controller->index();

?>