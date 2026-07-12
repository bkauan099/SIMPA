<?php
require_once 'conexao/conexao.php';
require_once 'model/Projeto.php';

$projetoModel = new Projeto($pdo);
$id_logado    = $_SESSION['id_usuario'] ?? 0;

$projetos     = $projetoModel->listarProjetosPorProfessor($id_logado);
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

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-blue">
            <div class="sc-watermark"><i class="bi bi-folder-fill"></i></div>
            <div class="sc-label"><i class="bi bi-folder-fill"></i> Ativos</div>
            <div class="sc-number"><?= $estatisticas['ativos'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-yellow">
            <div class="sc-watermark"><i class="bi bi-hourglass-split"></i></div>
            <div class="sc-label"><i class="bi bi-hourglass-split"></i> Aguard. Aprovação</div>
            <div class="sc-number"><?= $estatisticas['aguardando'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-purple">
            <div class="sc-watermark"><i class="bi bi-people"></i></div>
            <div class="sc-label"><i class="bi bi-people"></i> Alunos no Total</div>
            <div class="sc-number"><?= $estatisticas['alunos'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-green">
            <div class="sc-watermark"><i class="bi bi-check2-all"></i></div>
            <div class="sc-label"><i class="bi bi-check2-all"></i> Concluídos</div>
            <div class="sc-number"><?= $estatisticas['concluidos'] ?></div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filtro_projeto" class="form-control border-start-0" placeholder="Buscar projeto...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select class="form-select" id="filtro_tipo">
                    <option value="">Tipo (Todos)</option>
                    <option>Projeto Especial</option>
                    <option>Ligas Acadêmicas</option>
                    <option>Inovação TIC</option>
                    <option>Extensão</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select class="form-select" id="filtro_status">
                    <option value="">Status (Todos)</option>
                    <option>Ativo</option>
                    <option>Pendente</option>
                    <option>Concluído</option>
                    <option>Inativo</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="limparFiltrosProjetos()">
                    <i class="bi bi-x-circle me-1"></i>Limpar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Projetos -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-2">
            <h5 class="fw-bold mb-0">Lista de Projetos</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                <tbody id="tabela_projetos_corpo">
                    <?php
                    $contador = 1;
                    if (!empty($projetos)):
                        foreach ($projetos as $projeto):
                    ?>
                        <tr class="projeto-linha">
                            <td class="fw-bold text-muted">#<?= $contador ?></td>
                            <td class="fw-medium projeto-titulo text-start"><?= htmlspecialchars($projeto['titulo']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border projeto-tipo">
                                    <?= htmlspecialchars($projeto['tipo_nome'] ?? 'Projeto Especial') ?>
                                </span>
                            </td>
                            <td><?= $projeto['total_participantes'] ?? 0 ?></td>
                            <td><?= $projeto['carga_horaria'] ?? '0' ?>h</td>
                            <td>
                                <?php $status_atual = strtolower($projeto['status'] ?? ''); ?>
                                <?php if ($status_atual == 'ativo'): ?>
                                    <span class="badge bg-success-subtle text-success fw-semibold projeto-status-badge">Ativo</span>
                                <?php elseif ($status_atual == 'pendente'): ?>
                                    <span class="badge bg-warning text-dark projeto-status-badge">Pendente</span>
                                <?php else: ?>
                                    <span class="badge bg-success text-white projeto-status-badge">Concluído</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAlunos(<?= $projeto['id_projeto'] ?>)" title="Ver alunos">
                                    <i class="bi bi-people"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick='abrirModalEditar(<?= htmlspecialchars(json_encode($projeto, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)' title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info ms-1" title="Documentos"
                                    onclick="abrirModalDocumentos(<?= $projeto['id_projeto'] ?>, '<?= addslashes($projeto['titulo']) ?>')">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                            </td>
                        </tr>
                    <?php
                        $contador++;
                    endforeach;
                    else:
                    ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x mb-2" style="font-size:2rem;display:block;"></i>
                                <p class="fw-bold m-0">Nenhum projeto encontrado.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    const norm = s => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

    const filtrarProjetos = () => {
        const termo          = norm(document.getElementById('filtro_projeto').value.trim());
        const tipoSelecionado = document.getElementById('filtro_tipo').value.toLowerCase().trim();
        const statusSelecionado = document.getElementById('filtro_status').value.toLowerCase().trim();
        const tabela         = document.getElementById('tabela_projetos_corpo');
        if (!tabela) return;

        const linhas = tabela.querySelectorAll('.projeto-linha');
        let encontrouAlgum = false;

        linhas.forEach(linha => {
            const txtTitulo = norm(linha.querySelector('.projeto-titulo').textContent.trim());
            const txtTipo   = linha.querySelector('.projeto-tipo').textContent.toLowerCase().trim();
            const txtStatus = linha.querySelector('.projeto-status-badge').textContent.toLowerCase().trim();

            const bateTitulo = termo === '' || txtTitulo.startsWith(termo);
            const bateTipo   = tipoSelecionado === "" || txtTipo.includes(tipoSelecionado);
            const bateStatus = statusSelecionado === "" || txtStatus.includes(statusSelecionado);

            if (bateTitulo && bateTipo && bateStatus) {
                linha.style.display = "";
                encontrouAlgum = true;
            } else {
                linha.style.display = "none";
            }
        });

        let linhaAviso = document.getElementById('linha-nenhum-resultado');
        if (!encontrouAlgum) {
            if (!linhaAviso) {
                linhaAviso = document.createElement('tr');
                linhaAviso.id = 'linha-nenhum-resultado';
                linhaAviso.innerHTML = `<td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-search mb-2" style="font-size:2rem;display:block;"></i>
                    <p class="fw-bold m-0">Nenhum projeto encontrado</p>
                    <small>Tente ajustar os termos da busca ou os filtros.</small>
                </td>`;
                tabela.appendChild(linhaAviso);
            }
        } else if (linhaAviso) {
            linhaAviso.remove();
        }
    };

    const elInput  = document.getElementById('filtro_projeto');
    const elTipo   = document.getElementById('filtro_tipo');
    const elStatus = document.getElementById('filtro_status');
    if (elInput)  elInput.addEventListener('input', filtrarProjetos);
    if (elTipo)   elTipo.addEventListener('change', filtrarProjetos);
    if (elStatus) elStatus.addEventListener('change', filtrarProjetos);

    window.limparFiltrosProjetos = function() {
        document.getElementById('filtro_projeto').value = '';
        document.getElementById('filtro_tipo').value    = '';
        document.getElementById('filtro_status').value  = '';
        filtrarProjetos();
    };
})();
</script>
