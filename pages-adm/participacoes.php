<?php
require_once '../lib/Guard.php';
Guard::apenasAdmin();
require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/participacaoController.php';
$controller = new ParticipacaoController($pdo);
$controller->index();
?>
