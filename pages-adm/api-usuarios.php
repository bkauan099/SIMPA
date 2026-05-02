<?php
// pages-adm/api-usuarios.php
header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($errno, $errstr) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro PHP: $errstr"]);
    exit;
});


require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/usuarioController.php';

$acao = $_GET['acao'] ?? '';
$controller = new UsuarioController($pdo);

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
    case 'redefinirSenha':
        $controller->redefinirSenha();
        break;
    case 'buscar':
        $controller->buscarPorId();
        break;
    default:
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
}
?>
