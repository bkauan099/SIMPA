<?php
require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../controllers/controller-aluno/dashboardController.php';

$controller = new DashboardAlunoController($pdo, $id_usuario);
$controller->index();
?>
