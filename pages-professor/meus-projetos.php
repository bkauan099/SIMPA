<?php
require_once 'conexao/conexao.php';
require_once 'model/Projeto.php';

$projetoModel = new Projeto($pdo);
// Usando o ID da sessão para ser real, ou mantendo o 5 para seus testes atuais
$id_logado = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 5;

$projetos = $projetoModel->listarProjetosPorProfessor($id_logado);
$estatisticas = $projetoModel->obterEstatisticasProfessor($id_logado);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Meus Projetos</h3>
        <p class="text-muted mb-0">Projetos que você coordena ou orienta</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModal()">
        <i class="bi bi-plus-circle me-2"></i>Novo Projeto
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4>
                <small class="text-muted">Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['aguardando'] ?></h4>
                <small class="text-muted">Aguard. Aprovação</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['alunos'] ?></h4>
                <small class="text-muted">Alunos no Total</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-all"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['concluidos'] ?></h4>
                <small class="text-muted">Concluídos</small>
            </div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar projeto...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Tipo (Todos)</option>
                <option>Projeto Especial</option>
                <option>Ligas Acadêmicas</option>
                <option>Empresa Jr</option>
                <option>Atlética</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Status (Todos)</option>
                <option>Ativo</option>
                <option>Concluído</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Lista de Projetos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th>
                    <th>TÍTULO</th>
                    <th>TIPO</th>
                    <th>ALUNOS</th>
                    <th>CARGA HORÁRIA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Inicializa o contador sequencial
                $contador = 1;

                // Verifica se a variável $projetos (que vem do seu Model) possui dados
                if (!empty($projetos)):
                    foreach ($projetos as $projeto):
                ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $contador ?></td>

                            <td class="fw-medium"><?= htmlspecialchars($projeto['titulo']) ?></td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($projeto['tipo_nome'] ?? 'Projeto Especial') ?>
                                </span>
                            </td>

                            <td><?= $projeto['total_participantes'] ?? 0 ?></td>

                            <td><?= $projeto['carga_horaria'] ?? '0' ?>h</td>

                            <td>
                                <?php
                                $status_atual = $projeto['status'] ?? '';

                                if ($status_atual == 'ativo'): ?>
                                    <span class="status-ativo">Ativo</span>

                                <?php elseif ($status_atual == 'pendente'): ?>
                                    <span class="badge bg-warning text-dark" style="padding: 5px 12px; font-weight: 600;">Pendente</span>

                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Concluído</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="abrirModalAlunos(<?= $projeto['id_projeto'] ?>)"
                                        title="Ver alunos">
                                    <i class="bi bi-people"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary ms-1" 
                                        onclick='abrirModalEditar(<?= htmlspecialchars(json_encode($projeto)) ?>)'
                                        title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info ms-1" title="Documentos">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                                <?php if (($projeto['status'] ?? '') != 'ativo'): ?>
                                    <button class="btn btn-sm btn-outline-success ms-1" title="Reativar">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                        $contador++; // Incrementa para a próxima linha da tabela
                    endforeach;
                else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Nenhum projeto encontrado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>