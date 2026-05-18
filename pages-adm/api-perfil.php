<?php
ob_start();
require_once '../lib/Guard.php';
Guard::apenasAdmin();
header('Content-Type: application/json; charset=utf-8');
set_error_handler(function($e,$m){ echo json_encode(['sucesso'=>false,'mensagem'=>$m]); exit; });
if (empty($_SESSION['id_usuario'])) { echo json_encode(['erro'=>'Não autenticado']); exit; }
require_once '../conexao/conexao.php';
require_once '../lib/Logger.php';
Logger::setPDO($pdo);
$acao = $_GET['acao'] ?? '';
$id   = (int)$_SESSION['id_usuario'];

if ($acao === 'dados') {
    $s=$pdo->prepare("SELECT id_usuario,nome,email,matricula,CAST(perfil AS TEXT) AS perfil,curso FROM usuarios WHERE id_usuario=:id");
    $s->execute([':id'=>$id]);
    echo json_encode($s->fetch(PDO::FETCH_ASSOC) ?: ['erro'=>'Não encontrado']);
} elseif ($acao === 'atualizar' && $_SERVER['REQUEST_METHOD']==='POST') {
    $nome=trim($_POST['nome']??''); $email=trim($_POST['email']??''); $curso=trim($_POST['curso']??'');
    if(!$nome||!$email){echo json_encode(['sucesso'=>false,'mensagem'=>'Nome e e-mail são obrigatórios.']);exit;}
    $s=$pdo->prepare("UPDATE usuarios SET nome=:n,email=:e,curso=:c WHERE id_usuario=:id");
    $ok=$s->execute([':n'=>$nome,':e'=>$email,':c'=>$curso,':id'=>$id]);
    if($ok){$_SESSION['nome']=$nome;$_SESSION['email']=$email; Logger::registrar(Logger::PERFIL, Logger::EDITAR, 'ADM editou o próprio perfil', ['nome'=>$nome,'email'=>$email]);}
    echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Perfil atualizado!':'Erro ao atualizar.']);
} elseif ($acao === 'senha' && $_SERVER['REQUEST_METHOD']==='POST') {
    $at=$_POST['senha_atual']??''; $nv=$_POST['nova_senha']??''; $cf=$_POST['confirma']??'';
    if(strlen($nv)<6){echo json_encode(['sucesso'=>false,'mensagem'=>'Mínimo 6 caracteres.']);exit;}
    if($nv!==$cf){echo json_encode(['sucesso'=>false,'mensagem'=>'As senhas não conferem.']);exit;}
    $s=$pdo->prepare("SELECT senha FROM usuarios WHERE id_usuario=:id"); $s->execute([':id'=>$id]);
    $row=$s->fetch(PDO::FETCH_ASSOC);
    if(!$row||!password_verify($at,$row['senha'])){echo json_encode(['sucesso'=>false,'mensagem'=>'Senha atual incorreta.']);exit;}
    $h=password_hash($nv,PASSWORD_DEFAULT);
    $s2=$pdo->prepare("UPDATE usuarios SET senha=:s WHERE id_usuario=:id");
    $ok=$s2->execute([':s'=>$h,':id'=>$id]);
    if($ok) Logger::registrar(Logger::PERFIL, Logger::ALTERAR_SENHA, 'ADM alterou a própria senha');
    echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Senha alterada com sucesso!':'Erro ao alterar.']);
} else { echo json_encode(['erro'=>'Ação inválida.']); }
?>
