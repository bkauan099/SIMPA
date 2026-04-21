<?php
require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../controllers/controller-aluno/projetosController.php';


$controller = new ProjetosAlunoController($pdo, $id_usuario);
$controller->index();
?>
