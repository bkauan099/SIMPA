<?php
require_once 'conexao/conexao.php';
require_once 'model/Projeto.php';

$projetoModel = new Projeto($pdo);
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

<!-- Cards de Estatísticas -->
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
    <!-- ... outros cards (mantidos como no seu original) ... -->
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

<!-- BARRA DE FILTROS -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtro_projeto" class="form-control border-start-0" placeholder="Buscar projeto...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtro_tipo">
                <option value="">Tipo (Todos)</option>
                <option>Projeto Especial</option>
                <option>Ligas Acadêmicas</option>
                <option>Inovação TIC</option>
                <option>Extensão</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtro_status">
                <option value="">Status (Todos)</option>
                <option>Ativo</option>
                <option>Pendente</option>
                <option>Concluído</option>
                <option>Inativo</option>
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
            <tbody id="tabela_projetos_corpo">
                <?php
                $contador = 1;
                if (!empty($projetos)):
                    foreach ($projetos as $projeto):
                ?>
                        <!-- ADICIONADA CLASSE projeto-linha -->
                        <tr class="projeto-linha">
                            <td class="fw-bold text-muted">#<?= $contador ?></td>

                            <!-- ADICIONADA CLASSE projeto-titulo -->
                            <td class="fw-medium projeto-titulo"><?= htmlspecialchars($projeto['titulo']) ?></td>

                            <td>
                                <!-- ADICIONADA CLASSE projeto-tipo -->
                                <span class="badge bg-light text-dark border projeto-tipo">
                                    <?= htmlspecialchars($projeto['tipo_nome'] ?? 'Projeto Especial') ?>
                                </span>
                            </td>

                            <td><?= $projeto['total_participantes'] ?? 0 ?></td>
                            <td><?= $projeto['carga_horaria'] ?? '0' ?>h</td>

                            <td>
                                <?php
                                $status_atual = strtolower($projeto['status'] ?? '');
                                if ($status_atual == 'ativo'): ?>
                                    <!-- ADICIONADA CLASSE projeto-status-badge -->
                                    <span class="status-ativo projeto-status-badge">Ativo</span>
                                <?php elseif ($status_atual == 'pendente'): ?>
                                    <span class="badge bg-warning text-dark projeto-status-badge" style="padding: 5px 12px; font-weight: 600;">Pendente</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white projeto-status-badge">Concluído</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAlunos(<?= $projeto['id_projeto'] ?>)" title="Ver alunos">
                                    <i class="bi bi-people"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick='abrirModalEditar(<?= htmlspecialchars(json_encode($projeto)) ?>)' title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info ms-1"
                                    title="Documentos"
                                    onclick="abrirModalDocumentos(<?= $projeto['id_projeto'] ?>)">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                        </tr>
                    <?php
                        $contador++;
                    endforeach;
                else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Nenhum projeto encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    (function() {
        const filtrarProjetos = () => {
            const input = document.getElementById('filtro_projeto');
            const selectTipo = document.getElementById('filtro_tipo');
            const selectStatus = document.getElementById('filtro_status');
            const tabela = document.getElementById('tabela_projetos_corpo');

            if (!tabela || !input) return;

            const termo = input.value.toLowerCase().trim();
            const tipoSelecionado = selectTipo.value.toLowerCase().trim();
            const statusSelecionado = selectStatus.value.toLowerCase().trim();

            const linhas = tabela.querySelectorAll('.projeto-linha');
            let encontrouAlgum = false; // Variável de controle

            linhas.forEach(linha => {
                const txtTitulo = linha.querySelector('.projeto-titulo').textContent.toLowerCase().trim();
                const txtTipo = linha.querySelector('.projeto-tipo').textContent.toLowerCase().trim();
                const txtStatus = linha.querySelector('.projeto-status-badge').textContent.toLowerCase().trim();

                const bateTitulo = txtTitulo.startsWith(termo);
                const bateTipo = tipoSelecionado === "" || txtTipo.includes(tipoSelecionado);
                const bateStatus = statusSelecionado === "" || txtStatus.includes(statusSelecionado);

                if (bateTitulo && bateTipo && bateStatus) {
                    linha.style.display = "";
                    encontrouAlgum = true; // Achou pelo menos um projeto
                } else {
                    linha.style.display = "none";
                }
            });

            // --- LÓGICA DO AVISO DE "NENHUM RESULTADO" ---
            // Verifica se já existe a linha de aviso para não duplicar
            let linhaAviso = document.getElementById('linha-nenhum-resultado');

            if (!encontrouAlgum) {
                if (!linhaAviso) {
                    linhaAviso = document.createElement('tr');
                    linhaAviso.id = 'linha-nenhum-resultado';
                    linhaAviso.innerHTML = `
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-search mb-2" style="font-size: 2rem; display: block;"></i>
                        <p class="fw-bold m-0">Nenhum projeto encontrado</p>
                        <small>Tente ajustar os termos da busca ou os filtros aplicados.</small>
                    </div>
                </td>
            `;
                    tabela.appendChild(linhaAviso);
                }
            } else {
                if (linhaAviso) {
                    linhaAviso.remove(); // Remove o aviso se encontrar algum projeto
                }
            }
        };

        // Vinculação dos eventos
        const elInput = document.getElementById('filtro_projeto');
        const elTipo = document.getElementById('filtro_tipo');
        const elStatus = document.getElementById('filtro_status');

        if (elInput) elInput.addEventListener('input', filtrarProjetos);
        if (elTipo) elTipo.addEventListener('change', filtrarProjetos);
        if (elStatus) elStatus.addEventListener('change', filtrarProjetos);

        console.log('[SIMPA] Filtro "Começa com" ativo.');
    })();
</script>