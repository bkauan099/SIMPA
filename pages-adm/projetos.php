<?php
session_start();
?>
<?php
require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/projetoController.php';
$controller = new ProjetoController($pdo);
$controller->index();
?>
