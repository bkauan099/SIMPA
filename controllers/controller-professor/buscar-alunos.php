<?php
require_once '../../conexao/conexao.php';
require_once '../../model/Projeto.php';
require_once '../../model/Usuario.php';

$projetoModel = new Projeto($pdo);
$usuarioModel = new Usuario($pdo);

// AÇÃO 1: BUSCA DINÂMICA (Dropdown de sugestões)
if (isset($_GET['busca'])) {
    $sugestoes = $usuarioModel->buscarAlunosPorNome($_GET['busca']);
    if (empty($sugestoes)) {
        echo "<div class='list-group-item text-muted small'>Nenhum aluno encontrado</div>";
    } else {
        foreach ($sugestoes as $s) {
            echo "<a href='javascript:void(0)' class='list-group-item list-group-item-action' 
             onclick=\"selecionarAluno('" . htmlspecialchars($s['nome']) . "', {$s['id_usuario']})\">
            <div class='fw-bold'>" . htmlspecialchars($s['nome']) . "</div>
            <small class='text-muted'>Matrícula: " . htmlspecialchars($s['matricula']) . "</small>
          </a>";
        }
    }
    exit;
}

// AÇÃO 2: LISTAR ALUNOS ATUAIS
if (isset($_GET['id_projeto']) && $_GET['id_projeto'] !== 'null' && is_numeric($_GET['id_projeto'])) {
    $id_projeto = $_GET['id_projeto'];
    $participantes = $projetoModel->listarParticipantes($id_projeto);

    echo '<table class="table table-hover align-middle">
            <thead class="table-light small">
                <tr>
                    <th>NOME</th>
                    <th>MATRÍCULA</th>
                    <th>CURSO</th>
                    <th>CH</th> <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>';

    if (empty($participantes)) {
        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Este projeto ainda não possui alunos vinculados.</td></tr>";
    } else {
        foreach ($participantes as $p) {
            // --- DEFINIÇÃO DAS VARIÁVEIS PARA O BOTÃO REMOVER ---
            $uid = $p['id_usuario']; 
            $pid = $id_projeto; 
            // ----------------------------------------------------

            $chValor = (isset($p['carga_horaria']) && $p['carga_horaria'] !== null) ? $p['carga_horaria'] : 0;
            $chFormatada = $chValor . 'h';

            echo "<tr>
                <td>" . htmlspecialchars($p['nome']) . "</td>
                <td>" . htmlspecialchars($p['matricula']) . "</td>
                <td>" . htmlspecialchars($p['curso']) . "</td>
                <td><span class='fw-bold text-primary'>$chFormatada</span></td> 
                <td><span class='badge bg-success'>Ativo</span></td>
                <td class='text-center'>
                    <button type='button' class='btn btn-sm btn-outline-danger' onclick='removerAluno($uid, $pid)'>
                        <i class='bi bi-trash'></i>
                    </button>
                </td>
              </tr>";
        }
    }
    echo '</tbody></table>';
} else {
    if (isset($_GET['id_projeto'])) {
        echo "<div class='text-center py-3 text-muted small'>Selecione um projeto para carregar...</div>";
    }
}
