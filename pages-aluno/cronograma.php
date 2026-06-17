<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) { echo '<p class="text-danger p-4">Sessão expirada. Recarregue a página.</p>'; exit; }
require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../controllers/controller-aluno/cronogramaController.php';
$controller = new CronogramaAlunoController($pdo, $id_usuario);
$controller->index();
?>
