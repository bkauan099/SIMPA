<?php
// pages-adm/pagina-inicial.php

require_once '../Backend/conexao/conexao.php';
require_once '../Backend/controllers/controller-adm/dashboardController.php';

$controller = new DashboardController($pdo);
$controller->index();
?>