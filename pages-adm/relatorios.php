<?php
require_once '../lib/Guard.php';
Guard::apenasAdmin();
?>
<?php
// pages-adm/relatorios.php

require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/relatorioController.php';

$controller = new RelatorioController($pdo);
$controller->index();
?>
