<?php
// pages-adm/usuarios.php

// Puxa a conexão direta da raiz
require_once '../conexao/conexao.php';

// Puxa o Controller da raiz
require_once '../controllers/controller-adm/usuarioController.php';

$controller = new UsuarioController($pdo);
$controller->index();
?>