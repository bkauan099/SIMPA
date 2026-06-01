<?php
$id_professor = $_SESSION['id_usuario'] ?? 0;

// Migration: garante colunas necessárias na agenda_items
try {
    $pdo->exec("ALTER TABLE agenda_items ADD COLUMN IF NOT EXISTS id_projeto INTEGER");
    $pdo->exec("ALTER TABLE agenda_items ADD COLUMN IF NOT EXISTS prioridade VARCHAR(10) DEFAULT 'media'");
    $pdo->exec("ALTER TABLE agenda_items ADD COLUMN IF NOT EXISTS status_tarefa VARCHAR(20) DEFAULT 'pendente'");
} catch (PDOException $e) {}

// Stats
$stats = ['total' => 0, 'pendentes' => 0, 'em_andamento' => 0, 'concluidas' => 0];
try {
    $stmt = $pdo->prepare("SELECT
        COUNT(DISTINCT a.id) AS total,
        COUNT(DISTINCT CASE WHEN COALESCE(a.status_tarefa,'pendente') = 'pendente' THEN a.id END) AS pendentes,
        COUNT(DISTINCT CASE WHEN a.status_tarefa = 'em_andamento' THEN a.id END) AS em_andamento,
        COUNT(DISTINCT CASE WHEN a.status_tarefa = 'concluida' THEN a.id END) AS concluidas
    FROM agenda_items a
    JOIN participacao par ON a.id_projeto = par.id_projeto
    WHERE par.id_usuario = :id AND a.id_projeto IS NOT NULL");
    $stmt->execute([':id' => $id_professor]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) $stats = $row;
} catch (PDOException $e) {}

// Projetos do professor
$projetos = [];
try {
    $stmt = $pdo->prepare("SELECT DISTINCT p.id_projeto, p.titulo
        FROM projetos p
        JOIN participacao par ON p.id_projeto = par.id_projeto
        WHERE par.id_usuario = :id
        ORDER BY p.titulo ASC");
    $stmt->execute([':id' => $id_professor]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Todas as tarefas (renderizadas server-side, filtradas no cliente)
$tarefas = [];
try {
    $stmt = $pdo->prepare("SELECT DISTINCT ON (a.id)
        a.id, a.titulo, a.descricao, a.data,
        COALESCE(a.prioridade, 'media') AS prioridade,
        COALESCE(a.status_tarefa, 'pendente') AS status_tarefa,
        a.id_usuario, a.id_projeto,
        u.nome AS nome_aluno,
        p.titulo AS nome_projeto
    FROM agenda_items a
    JOIN participacao par ON a.id_projeto = par.id_projeto
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    LEFT JOIN projetos p ON a.id_projeto = p.id_projeto
    WHERE par.id_usuario = :id AND a.id_projeto IS NOT NULL
    ORDER BY a.id, a.data ASC NULLS LAST");
    $stmt->execute([':id' => $id_professor]);
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

$prioLabels  = ['alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'];
$prioClasses = ['alta' => 'bg-danger', 'media' => 'bg-warning text-dark', 'baixa' => 'bg-secondary'];
$stLabels    = ['pendente' => 'Pendente', 'em_andamento' => 'Em Andamento', 'concluida' => 'Concluída'];
$stClasses   = ['pendente' => 'bg-warning text-dark', 'em_andamento' => 'bg-info text-dark', 'concluida' => 'bg-success'];
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Tarefas</h3>
        <p class="text-muted mb-0">Tarefas atribuídas aos alunos dos seus projetos</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalNovaTarefa()">
        <i class="bi bi-plus-circle me-2"></i>Nova Tarefa
    </button>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-list-check"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statTotal"><?= intval($stats['total']) ?></h4><small class="text-muted">Total de Tarefas</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statPendentes"><?= intval($stats['pendentes']) ?></h4><small class="text-muted">Pendentes</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-arrow-repeat"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statAndamento"><?= intval($stats['em_andamento']) ?></h4><small class="text-muted">Em Andamento</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-circle"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statConcluidas"><?= intval($stats['concluidas']) ?></h4><small class="text-muted">Concluídas</small></div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="buscaTarefa" class="form-control border-start-0" placeholder="Buscar tarefa ou aluno...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select id="filtroProjeto" class="form-select">
                <option value="">Todos os Projetos</option>
                <?php foreach ($projetos as $p): ?>
                    <option value="<?= $p['id_projeto'] ?>"><?= htmlspecialchars($p['titulo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select id="filtroStatus" class="form-select">
                <option value="">Todos os Status</option>
                <option value="pendente">Pendente</option>
                <option value="em_andamento">Em Andamento</option>
                <option value="concluida">Concluída</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">Limpar</button>
        </div>
    </div>
</div>

<!-- Tabela de Tarefas -->
<div class="content-card">
    <h5 class="fw-bold mb-3">Lista de Tarefas</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th><th>ALUNO</th><th>PROJETO</th><th>PRAZO</th>
                    <th>PRIORIDADE</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody id="tabelaTarefas">
                <?php if (empty($tarefas)): ?>
                    <tr id="linhaVazia"><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x mb-2" style="font-size:2rem;display:block;"></i>
                        <p class="fw-bold m-0">Nenhuma tarefa encontrada</p>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($tarefas as $t):
                        $prio      = $t['prioridade']    ?? 'media';
                        $status    = $t['status_tarefa'] ?? 'pendente';
                        $nomeAluno = $t['nome_aluno']    ?? 'N/A';
                        $nomeProjeto = $t['nome_projeto'] ?? '—';
                        $prazo     = $t['data'] ? date('d/m/Y', strtotime($t['data'])) : '—';
                        $id        = htmlspecialchars($t['id'], ENT_QUOTES);
                        $acoes = "<button class='btn btn-sm btn-outline-primary' onclick=\"verTarefa('{$id}')\" title='Ver detalhes'><i class='bi bi-eye'></i></button>";
                        if ($status !== 'concluida') {
                            $acoes .= " <button class='btn btn-sm btn-outline-secondary ms-1' onclick=\"editarTarefa('{$id}')\" title='Editar'><i class='bi bi-pencil'></i></button>";
                            $acoes .= " <button class='btn btn-sm btn-outline-danger ms-1' onclick=\"confirmarExcluirTarefa('{$id}')\" title='Excluir'><i class='bi bi-trash'></i></button>";
                        }
                    ?>
                    <tr class="linha-tarefa"
                        data-titulo="<?= strtolower(htmlspecialchars($t['titulo'])) ?>"
                        data-aluno="<?= strtolower(htmlspecialchars($nomeAluno)) ?>"
                        data-projeto="<?= $t['id_projeto'] ?>"
                        data-status="<?= $status ?>">
                        <td class="fw-medium"><?= htmlspecialchars($t['titulo']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nomeAluno) ?>&background=e0f2fe&color=0369a1"
                                     class="rounded-circle" width="26" alt="">
                                <?= htmlspecialchars($nomeAluno) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($nomeProjeto) ?></td>
                        <td><?= $prazo ?></td>
                        <td><span class="badge <?= $prioClasses[$prio] ?? 'bg-secondary' ?>"><?= $prioLabels[$prio] ?? ucfirst($prio) ?></span></td>
                        <td><span class="badge <?= $stClasses[$status] ?? 'bg-secondary' ?>"><?= $stLabels[$status] ?? ucfirst($status) ?></span></td>
                        <td class="text-center"><?= $acoes ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== MODAIS ===== -->

<!-- Modal Nova / Editar Tarefa -->
<div id="modalTarefa" class="modal-simpa" style="display:none;">
    <div class="modal-content-simpa" style="max-width:560px;width:95%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 id="modalTarefaTitulo" class="m-0 fw-bold" style="color:var(--azul-uema)">Nova Tarefa</h4>
            <button type="button" class="btn-close" onclick="fecharModalTarefa()"></button>
        </div>
        <form id="formTarefa" onsubmit="return false;">
            <input type="hidden" id="tarefaId">

            <div class="mb-3">
                <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                <input type="text" id="tarefaTituloInput" class="form-control" placeholder="Título da tarefa" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Projeto <span class="text-danger">*</span></label>
                    <select id="tarefaProjeto" class="form-select" required onchange="carregarAlunosTarefa(this.value)">
                        <option value="">Selecione um projeto...</option>
                        <?php foreach ($projetos as $p): ?>
                            <option value="<?= $p['id_projeto'] ?>"><?= htmlspecialchars($p['titulo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Aluno</label>
                    <select id="tarefaAluno" class="form-select">
                        <option value="">Selecione o projeto primeiro</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prazo</label>
                    <input type="date" id="tarefaPrazo" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Prioridade</label>
                    <select id="tarefaPrioridade" class="form-select">
                        <option value="alta">Alta</option>
                        <option value="media" selected>Média</option>
                        <option value="baixa">Baixa</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select id="tarefaStatusInput" class="form-select">
                    <option value="pendente">Pendente</option>
                    <option value="em_andamento">Em Andamento</option>
                    <option value="concluida">Concluída</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Descrição</label>
                <textarea id="tarefaDescricao" class="form-control" rows="3" placeholder="Detalhes da tarefa..."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light border" onclick="fecharModalTarefa()">Cancelar</button>
                <button type="button" id="btnSalvarTarefa" class="btn btn-primary" onclick="salvarTarefa()">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver Detalhes -->
<div id="modalVerTarefa" class="modal-simpa" style="display:none;">
    <div class="modal-content-simpa" style="max-width:520px;width:95%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="m-0 fw-bold" style="color:var(--azul-uema)">Detalhes da Tarefa</h4>
            <button type="button" class="btn-close" onclick="document.getElementById('modalVerTarefa').style.display='none'"></button>
        </div>
        <div id="conteudoDetalheTarefa"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
        <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalVerTarefa').style.display='none'">Fechar</button>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div id="modalConfirmarExclusaoTarefa" class="modal-simpa" style="display:none;">
    <div class="modal-content-simpa" style="max-width:400px;text-align:center;">
        <div class="mb-4">
            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:4rem;"></i>
            <h4 class="fw-bold mt-3">Excluir Tarefa?</h4>
            <p class="text-muted">Esta ação não pode ser desfeita.</p>
        </div>
        <input type="hidden" id="tarefaParaExcluir">
        <div class="d-flex justify-content-center gap-3">
            <button type="button" class="btn btn-light border px-4"
                onclick="document.getElementById('modalConfirmarExclusaoTarefa').style.display='none'">Cancelar</button>
            <button type="button" id="btnConfirmarExclusaoTarefaReal" class="btn btn-danger px-4"
                onclick="executarExclusaoTarefa()">Excluir</button>
        </div>
    </div>
</div>

<script>
(function () {
    // ── Filtragem client-side ──────────────────────────────────────────────
    const começa = (texto, termo) => texto.split(' ').some(p => p.startsWith(termo));

    const filtrar = () => {
        const busca   = document.getElementById('buscaTarefa').value.toLowerCase().trim();
        const projeto = document.getElementById('filtroProjeto').value;
        const status  = document.getElementById('filtroStatus').value;
        const linhas  = document.querySelectorAll('.linha-tarefa');

        linhas.forEach(tr => {
            const bateBusca   = busca === '' || começa(tr.dataset.titulo, busca) || começa(tr.dataset.aluno, busca);
            const bateProjeto = projeto === '' || tr.dataset.projeto === projeto;
            const bateStatus  = status === ''  || tr.dataset.status  === status;
            tr.style.display  = (bateBusca && bateProjeto && bateStatus) ? '' : 'none';
        });

        // Linha "nenhuma tarefa" quando tudo fica oculto
        const visiveis = document.querySelectorAll('.linha-tarefa:not([style*="none"])');
        let aviso = document.getElementById('linhaFiltroVazio');
        if (visiveis.length === 0) {
            if (!aviso) {
                aviso = document.createElement('tr');
                aviso.id = 'linhaFiltroVazio';
                aviso.innerHTML = `<td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-search mb-2" style="font-size:2rem;display:block;"></i>
                    <p class="fw-bold m-0">Nenhuma tarefa encontrada para este filtro</p>
                </td>`;
                document.getElementById('tabelaTarefas').appendChild(aviso);
            }
        } else if (aviso) {
            aviso.remove();
        }
    };

    document.getElementById('buscaTarefa').addEventListener('input', filtrar);
    document.getElementById('filtroProjeto').addEventListener('change', filtrar);
    document.getElementById('filtroStatus').addEventListener('change', filtrar);
})();

window.limparFiltros = function () {
    document.getElementById('buscaTarefa').value  = '';
    document.getElementById('filtroProjeto').value = '';
    document.getElementById('filtroStatus').value  = '';
    document.querySelectorAll('.linha-tarefa').forEach(tr => tr.style.display = '');
    const aviso = document.getElementById('linhaFiltroVazio');
    if (aviso) aviso.remove();
};

// ── Modal Nova Tarefa ────────────────────────────────────────────────────
window.abrirModalNovaTarefa = function () {
    document.getElementById('tarefaId').value = '';
    document.getElementById('formTarefa').reset();
    document.getElementById('tarefaAluno').innerHTML = '<option value="">Selecione o projeto primeiro</option>';
    document.getElementById('modalTarefaTitulo').textContent = 'Nova Tarefa';
    document.getElementById('modalTarefa').style.display = 'flex';
};

window.fecharModalTarefa = function () {
    document.getElementById('modalTarefa').style.display    = 'none';
    document.getElementById('modalVerTarefa').style.display = 'none';
};

window.carregarAlunosTarefa = function (idProjeto, idAlunoSelecionado) {
    const sel = document.getElementById('tarefaAluno');
    if (!idProjeto) { sel.innerHTML = '<option value="">Selecione o projeto primeiro</option>'; return; }
    sel.innerHTML = '<option value="">Carregando...</option>';
    fetch(`controllers/controller-professor/alunos-por-projeto.php?id_projeto=${idProjeto}`)
        .then(r => r.json())
        .then(alunos => {
            sel.innerHTML = '<option value="">Sem aluno específico</option>';
            alunos.forEach(a => {
                const sel_ = (idAlunoSelecionado && a.id_usuario == idAlunoSelecionado) ? ' selected' : '';
                sel.innerHTML += `<option value="${a.id_usuario}"${sel_}>${a.nome}</option>`;
            });
        })
        .catch(() => { sel.innerHTML = '<option value="">Erro ao carregar</option>'; });
};

window.salvarTarefa = function () {
    const titulo    = document.getElementById('tarefaTituloInput').value.trim();
    const idProjeto = document.getElementById('tarefaProjeto').value;
    if (!titulo)    { alert('Informe o título da tarefa.'); return; }
    if (!idProjeto) { alert('Selecione um projeto.'); return; }

    const btn = document.getElementById('btnSalvarTarefa');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

    const fd = new FormData();
    fd.append('id',            document.getElementById('tarefaId').value);
    fd.append('titulo',        titulo);
    fd.append('id_projeto',    idProjeto);
    fd.append('id_usuario',    document.getElementById('tarefaAluno').value);
    fd.append('data',          document.getElementById('tarefaPrazo').value);
    fd.append('prioridade',    document.getElementById('tarefaPrioridade').value);
    fd.append('status_tarefa', document.getElementById('tarefaStatusInput').value);
    fd.append('descricao',     document.getElementById('tarefaDescricao').value);

    fetch('controllers/controller-professor/salvar-tarefa.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                fecharModalTarefa();
                document.querySelector('a[href*="tarefas"]')?.click();
            } else {
                alert('Erro: ' + data.mensagem);
                btn.disabled = false; btn.innerHTML = orig;
            }
        })
        .catch(() => { alert('Erro de comunicação.'); btn.disabled = false; btn.innerHTML = orig; });
};

window.verTarefa = function (id) {
    const conteudo = document.getElementById('conteudoDetalheTarefa');
    conteudo.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    document.getElementById('modalVerTarefa').style.display = 'flex';
    fetch(`controllers/controller-professor/buscar-tarefas.php?modo=detalhe&id=${encodeURIComponent(id)}`)
        .then(r => r.text())
        .then(html => { conteudo.innerHTML = html; })
        .catch(() => { conteudo.innerHTML = '<p class="text-danger">Erro ao carregar detalhes.</p>'; });
};

window.editarTarefa = function (id) {
    fetch(`controllers/controller-professor/buscar-tarefas.php?modo=detalhe_json&id=${encodeURIComponent(id)}`)
        .then(r => r.json())
        .then(t => {
            if (t.erro) { alert(t.erro); return; }
            document.getElementById('tarefaId').value          = t.id;
            document.getElementById('tarefaTituloInput').value = t.titulo;
            document.getElementById('tarefaProjeto').value     = t.id_projeto || '';
            document.getElementById('tarefaPrazo').value       = t.data || '';
            document.getElementById('tarefaPrioridade').value  = t.prioridade || 'media';
            document.getElementById('tarefaStatusInput').value = t.status_tarefa || 'pendente';
            document.getElementById('tarefaDescricao').value   = t.descricao || '';
            document.getElementById('modalTarefaTitulo').textContent = 'Editar Tarefa';
            document.getElementById('modalTarefa').style.display = 'flex';
            if (t.id_projeto) carregarAlunosTarefa(t.id_projeto, t.id_usuario);
        })
        .catch(() => alert('Erro ao buscar dados da tarefa.'));
};

window.confirmarExcluirTarefa = function (id) {
    document.getElementById('tarefaParaExcluir').value = id;
    document.getElementById('modalConfirmarExclusaoTarefa').style.display = 'flex';
};

window.executarExclusaoTarefa = function () {
    const id  = document.getElementById('tarefaParaExcluir').value;
    const btn = document.getElementById('btnConfirmarExclusaoTarefaReal');
    if (!id) return;

    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Excluindo...';

    const fd = new FormData();
    fd.append('id', id);

    fetch('controllers/controller-professor/excluir-tarefa.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                document.getElementById('modalConfirmarExclusaoTarefa').style.display = 'none';
                document.querySelector('a[href*="tarefas"]')?.click();
            } else {
                alert('Erro: ' + data.mensagem);
                btn.disabled = false; btn.innerHTML = orig;
            }
        })
        .catch(() => { alert('Erro de comunicação.'); btn.disabled = false; btn.innerHTML = orig; });
};
</script>
