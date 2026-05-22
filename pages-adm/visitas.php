<?php
require_once '../lib/Guard.php';
Guard::apenasAdmin();
?>
<?php
require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/acessoController.php';
$controller = new AcessoController($pdo);
$controller->index();
?>
