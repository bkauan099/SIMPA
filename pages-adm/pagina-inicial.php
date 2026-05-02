<?php
session_start();
?>
<?php
// pages-adm/pagina-inicial.php

require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/dashboardController.php';

$controller = new DashboardController($pdo);
$controller->index();
?>