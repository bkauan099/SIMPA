<?php
ob_start();
require_once '../lib/Guard.php';
Guard::apenasAdmin();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
// pages-adm/api-projetos.php
header('Content-Type: application/json; charset=utf-8');

require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/projetoController.php';

$acao = $_GET['acao'] ?? '';
$controller = new ProjetoController($pdo);

switch ($acao) {
    case 'criar':
        $controller->criar();
        break;
    case 'atualizar':
        $controller->atualizar();
        break;
    case 'alterarStatus':
        $controller->alterarStatus();
        break;
    case 'buscar':
        $controller->buscarPorId();
        break;
    default:
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
}
?>
