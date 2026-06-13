<?php
$id_professor = $_SESSION['id_usuario'] ?? 0;

// Migration: garante colunas necessárias
try {
    $pdo->exec("ALTER TABLE agenda_items ADD COLUMN IF NOT EXISTS id_projeto INTEGER");
    $pdo->exec("ALTER TABLE agenda_items ADD COLUMN IF NOT EXISTS prioridade VARCHAR(10) DEFAULT 'media'");
} catch (PDOException $e) {}


// Stats derivadas de producoes.status
$stats = ['total' => 0, 'pendentes' => 0, 'canceladas' => 0, 'concluidas' => 0];
try {
    $stmt = $pdo->prepare("SELECT
        COUNT(DISTINCT a.id) AS total,
        COUNT(DISTINCT CASE WHEN COALESCE(
            (SELECT pr.status FROM producoes pr WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto ORDER BY pr.id_producao DESC LIMIT 1),
            'pendente') = 'pendente' THEN a.id END) AS pendentes,
        COUNT(DISTINCT CASE WHEN (
            SELECT pr.status FROM producoes pr WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto ORDER BY pr.id_producao DESC LIMIT 1
            ) = 'cancelado' THEN a.id END) AS canceladas,
        COUNT(DISTINCT CASE WHEN (
            SELECT pr.status FROM producoes pr WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto ORDER BY pr.id_producao DESC LIMIT 1
            ) = 'concluido' THEN a.id END) AS concluidas
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
        COALESCE(
            (SELECT pr.status FROM producoes pr
             WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
             ORDER BY pr.id_producao DESC LIMIT 1),
            'pendente'
        ) AS status_tarefa,
        a.id_usuario, a.id_projeto,
        u.nome AS nome_aluno,
        p.titulo AS nome_projeto,
        (SELECT COUNT(*) FROM producoes pr
         WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto AND pr.status = 'pendente') AS docs_pendentes
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
$stLabels    = ['pendente' => 'Pendente', 'concluido' => 'Concluída', 'cancelado' => 'Cancelada', 'ativo' => 'Ativo', 'inativo' => 'Inativo'];
$stClasses   = ['pendente' => 'bg-warning-subtle text-warning fw-semibold', 'concluido' => 'bg-success-subtle text-success fw-semibold', 'cancelado' => 'bg-danger-subtle text-danger fw-semibold', 'ativo' => 'bg-success-subtle text-success fw-semibold', 'inativo' => 'bg-danger-subtle text-danger fw-semibold'];
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
        <div class="stat-card-modern sc-blue">
            <div class="sc-watermark"><i class="bi bi-list-check"></i></div>
            <div class="sc-label"><i class="bi bi-list-check"></i> Total de Tarefas</div>
            <div class="sc-number" id="statTotal"><?= intval($stats['total']) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-yellow">
            <div class="sc-watermark"><i class="bi bi-hourglass"></i></div>
            <div class="sc-label"><i class="bi bi-hourglass"></i> Pendentes</div>
            <div class="sc-number" id="statPendentes"><?= intval($stats['pendentes']) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-red">
            <div class="sc-watermark"><i class="bi bi-x-circle"></i></div>
            <div class="sc-label"><i class="bi bi-x-circle"></i> Canceladas</div>
            <div class="sc-number" id="statCanceladas"><?= intval($stats['canceladas']) ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-green">
            <div class="sc-watermark"><i class="bi bi-check2-circle"></i></div>
            <div class="sc-label"><i class="bi bi-check2-circle"></i> Concluídas</div>
            <div class="sc-number" id="statConcluidas"><?= intval($stats['concluidas']) ?></div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-3"><div class="card-body py-2">
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
                <option value="concluido">Concluída</option>
                <option value="cancelado">Cancelada</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">Limpar</button>
        </div>
    </div>
</div></div>

<!-- Tabela de Tarefas -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <div class="px-4 pt-3 pb-2"><h5 class="fw-bold mb-0">Lista de Tarefas</h5></div>
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
                        $temDocPendente = intval($t['docs_pendentes'] ?? 0) > 0;
                        $dotHtml = $temDocPendente
                            ? "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle' style='width:10px;height:10px;'></span>"
                            : '';
                        $acoes = "<div class='position-relative d-inline-block'>
                            <button class='btn btn-sm btn-outline-primary' onclick=\"verTarefa('{$id}')\" title='Ver detalhes'><i class='bi bi-eye'></i></button>
                            {$dotHtml}
                        </div>";
                        if ($status !== 'concluido') {
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
</div>

<!-- ===== MODAIS BOOTSTRAP ===== -->

<!-- Modal Nova / Editar Tarefa -->
<div class="modal fade" id="modalTarefa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:560px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h4 id="modalTarefaTitulo" class="modal-title fw-bold" style="color:var(--azul-uema)">Nova Tarefa</h4>
                <button type="button" class="btn-close" onclick="fecharModalTarefa()"></button>
            </div>
            <div class="modal-body pt-2">
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
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prazo</label>
                            <input type="date" id="tarefaPrazo" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hora</label>
                            <input type="time" id="tarefaHora" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prioridade</label>
                            <select id="tarefaPrioridade" class="form-select">
                                <option value="alta">Alta</option>
                                <option value="media" selected>Média</option>
                                <option value="baixa">Baixa</option>
                            </select>
                        </div>
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
    </div>
</div>

<!-- Modal Ver Detalhes -->
<div class="modal fade" id="modalVerTarefa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:560px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" style="color:var(--azul-uema)">Detalhes da Tarefa</h4>
                <button type="button" class="btn-close" onclick="bsHide('modalVerTarefa')"></button>
            </div>
            <div class="modal-body pt-2">
                <div id="conteudoDetalheTarefa"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" onclick="bsHide('modalVerTarefa')">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="modalConfirmarExclusaoTarefa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content text-center p-4 border-0">
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:4rem;"></i>
                <h4 class="fw-bold mt-3">Excluir Tarefa?</h4>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <input type="hidden" id="tarefaParaExcluir">
            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-light border px-4" onclick="bsHide('modalConfirmarExclusaoTarefa')">Cancelar</button>
                <button type="button" id="btnConfirmarExclusaoTarefaReal" class="btn btn-danger px-4" onclick="executarExclusaoTarefa()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script>
// Flag: indica se houve mudança no banco enquanto o modal de detalhes estava aberto
let _tarefaModalAlterado = false;

// Ao fechar o modal "Ver Detalhes", recarrega a aba somente se algo foi alterado
document.getElementById('modalVerTarefa')
    .addEventListener('hidden.bs.modal', function () {
        if (_tarefaModalAlterado) {
            _tarefaModalAlterado = false;
            // Recarrega a aba "tarefas" via AJAX nav (mesmo mecanismo do menu lateral)
            const linkTarefas = document.querySelector('a[href*="page=tarefas"], a[data-page="tarefas"]');
            if (linkTarefas) {
                linkTarefas.click();
            } else {
                // fallback: recarrega a página inteira
                window.location.reload();
            }
        }
    });

(function () {
    // ── Filtragem client-side ──────────────────────────────────────────────
    const norm = s => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    const começa = (texto, termo) => norm(texto).startsWith(termo);

    const filtrar = () => {
        const busca   = norm(document.getElementById('buscaTarefa').value.trim());
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
    bsShow('modalTarefa');
};

window.fecharModalTarefa = function () {
    bsHide('modalTarefa');
    bsHide('modalVerTarefa');
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
    fd.append('hora',          document.getElementById('tarefaHora').value);
    fd.append('prioridade',    document.getElementById('tarefaPrioridade').value);
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
    bsShow('modalVerTarefa');
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
            document.getElementById('tarefaHora').value        = t.hora ? t.hora.substring(0,5) : '';
            document.getElementById('tarefaPrioridade').value  = t.prioridade || 'media';
            document.getElementById('tarefaDescricao').value   = t.descricao || '';
            document.getElementById('modalTarefaTitulo').textContent = 'Editar Tarefa';
            bsShow('modalTarefa');
            if (t.id_projeto) carregarAlunosTarefa(t.id_projeto, t.id_usuario);
        })
        .catch(() => alert('Erro ao buscar dados da tarefa.'));
};

window.confirmarExcluirTarefa = function (id) {
    document.getElementById('tarefaParaExcluir').value = id;
    bsShow('modalConfirmarExclusaoTarefa');
};

window.avaliarDoc = function (idProducao, acao, btn) {
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    const fd = new FormData();
    fd.append('id_producao', idProducao);
    fd.append('acao', acao);

    fetch('controllers/controller-professor/avaliar-doc-tarefa.php', { method: 'POST', body: fd })
        .then(r => r.text())
        .then(txt => {
            let data;
            try { data = JSON.parse(txt); }
            catch(e) { throw new Error('Resposta inválida: ' + txt.substring(0, 200)); }
            return data;
        })
        .then(data => {
            if (!data.sucesso) {
                alert('Erro: ' + data.mensagem);
                btn.disabled = false;
                btn.innerHTML = orig;
                return;
            }
            // Marca que houve alteração no banco — ao fechar o modal a aba será recarregada
            _tarefaModalAlterado = true;
            // Atualiza o card do documento sem recarregar o modal
            const card = document.getElementById('doc-' + idProducao);
            if (!card) return;
            const novoStatus  = data.novo_status;
            const statusLabel = { concluido: 'Aprovado', cancelado: 'Reprovado', pendente: 'Pendente', ativo: 'Aprovado', inativo: 'Reprovado' };
            const statusClass = { concluido: 'bg-success-subtle text-success fw-semibold', cancelado: 'bg-danger-subtle text-danger fw-semibold', pendente: 'bg-warning-subtle text-warning fw-semibold', ativo: 'bg-success-subtle text-success fw-semibold', inativo: 'bg-danger-subtle text-danger fw-semibold' };
            card.querySelector('.doc-status-badge').className = 'badge doc-status-badge ' + (statusClass[novoStatus] ?? 'bg-secondary');
            card.querySelector('.doc-status-badge').textContent = statusLabel[novoStatus] ?? novoStatus;
            // Reconstrói os botões de ação
            const acoes = card.querySelector('.doc-acoes');
            let btns = '';
            if (novoStatus !== 'concluido') btns += `<button class="btn btn-sm btn-success"         onclick="avaliarDoc(${idProducao},'aprovar',this)"  title="Aprovar"><i class="bi bi-check-lg"></i> Aprovar</button>`;
            if (novoStatus !== 'cancelado') btns += `<button class="btn btn-sm btn-outline-danger ms-1" onclick="avaliarDoc(${idProducao},'reprovar',this)" title="Reprovar"><i class="bi bi-x-lg"></i> Reprovar</button>`;
            acoes.innerHTML = btns;
        })
        .catch(err => { alert('Erro: ' + err.message); btn.disabled = false; btn.innerHTML = orig; });
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
                bsHide('modalConfirmarExclusaoTarefa');
                document.querySelector('a[href*="tarefas"]')?.click();
            } else {
                alert('Erro: ' + data.mensagem);
                btn.disabled = false; btn.innerHTML = orig;
            }
        })
        .catch(() => { alert('Erro de comunicação.'); btn.disabled = false; btn.innerHTML = orig; });
};
</script>
