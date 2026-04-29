<?php
require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../controllers/controller-aluno/participacoesController.php';

$controller = new ParticipacaoAlunoController($pdo, $id_usuario);
$controller->index();
?>
