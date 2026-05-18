<?php
require_once '../lib/Guard.php';
Guard::apenasAdmin();
require_once '../conexao/conexao.php';
require_once '../lib/Logger.php';
Logger::setPDO($pdo);
require __DIR__ . '/../views/view-adm/logs.view.php';
?>
