<?php
// Carregar usuários disponíveis para vinculação
$professores = array_filter($listaUsuariosAtivos, fn($u) =>
    str_contains(strtolower($u['perfil'] ?? ''), 'professor') ||
    str_contains(strtolower($u['perfil'] ?? ''), 'orientador')
);
$alunos = array_filter($listaUsuariosAtivos, fn($u) =>
    str_contains(strtolower($u['perfil'] ?? ''), 'aluno')
);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Participações em Projetos</h3>
        <p class="text-muted mb-0">Vincule professores e alunos a projetos, gerencie equipes e exporte relatórios</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i>Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Relatório Geral</h6></li>
                <li><a class="dropdown-item" href="#" onclick="exportar('geral','html')"><i class="bi bi-file-earmark-text me-2 text-primary"></i>HTML (Impressão/PDF)</a></li>
                <li><a class="dropdown-item" href="#" onclick="exportar('geral','csv')"><i class="bi bi-filetype-csv me-2 text-success"></i>CSV (Excel)</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Por Projeto</h6></li>
                <li><a class="dropdown-item" href="#" onclick="abrirExportProjeto('html')"><i class="bi bi-file-earmark-medical me-2 text-warning"></i>Relatório detalhado (HTML)</a></li>
                <li><a class="dropdown-item" href="#" onclick="abrirExportProjeto('csv')"><i class="bi bi-filetype-csv me-2 text-info"></i>CSV por projeto</a></li>
            </ul>
        </div>
        <button class="btn btn-primary" onclick="abrirModalVincular()">
            <i class="bi bi-person-plus me-2"></i>Vincular Usuário
        </button>
    </div>
</div>

<!-- ESTATÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #3b82f6;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#3b82f6;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#3b82f6;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-diagram-3"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#3b82f6;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-diagram-3"></i> Total de Vínculos</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['total'] ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #22c55e;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#22c55e;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#22c55e;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-person-check"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#22c55e;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-person-check"></i> Vínculos Ativos</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['ativos'] ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #ef4444;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#ef4444;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#ef4444;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-person-x"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#ef4444;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-person-x"></i> Encerrados</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['encerrados'] ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #f97316;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#f97316;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#f97316;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-folder2-open"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#f97316;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-folder2-open"></i> Projetos com Equipe</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['projetos_com_participantes'] ?></div>
        </div>
    </div>
</div>

<!-- ABAS -->
<div class="content-card mb-0 p-0" style="border-radius:12px 12px 0 0;border-bottom:0">
    <ul class="nav nav-tabs px-3 pt-2 gap-1">
        <li class="nav-item">
            <button class="nav-link active fw-medium" id="aba-equipes-btn" onclick="mostrarAba('equipes')">
                <i class="bi bi-kanban me-1"></i>Visão por Projeto
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium" id="aba-todos-btn" onclick="mostrarAba('todos')">
                <i class="bi bi-list-ul me-1"></i>Todos os Vínculos
            </button>
        </li>
    </ul>
</div>

<!-- ═══ ABA: VISÃO POR PROJETO (KANBAN DE EQUIPES) ═══ -->
<div id="aba-equipes">
    <div class="content-card mb-4 p-3" style="border-radius:0 0 12px 12px">
        <div class="input-group" style="max-width:400px">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="filtroProjeto" class="form-control border-start-0" placeholder="Filtrar projetos..." oninput="filtrarCards()">
        </div>
    </div>

    <div class="row g-3" id="gridProjetos">
        <?php foreach ($listaProjetosEquipe as $proj):
            $stCor = match($proj['status']) {
                'ativo'    => 'success', 'concluido' => 'success',
                'pendente' => 'warning', default      => 'danger'
            };
            $stBadge = match($proj['status']) {
                'ativo'    => 'badge bg-success-subtle text-success fw-semibold',
                'concluido'=> 'badge bg-success text-white',
                'pendente' => 'badge bg-warning text-dark',
                default    => 'badge bg-danger text-white',
            };
            $stLabel = match($proj['status']) {
                'ativo'    => 'Ativo', 'concluido' => 'Concluído',
                'pendente' => 'Pendente', default   => 'Inativo'
            };
            $dataInicio = $proj['data_inicio'] ? date('d/m/Y', strtotime($proj['data_inicio'])) : '—';
        ?>
        <div class="col-md-6 col-xl-4 projeto-card" data-titulo="<?= htmlspecialchars(strtolower($proj['titulo'])) ?>">
            <div class="content-card h-100 d-flex flex-column" style="border-top:3px solid var(--bs-<?= $stCor ?>)">
                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-1">
                    <h6 class="fw-bold mb-0" style="font-size:.88rem;flex:1"><?= htmlspecialchars($proj['titulo']) ?></h6>
                    <span class="<?= $stBadge ?>"><?= $stLabel ?></span>
                </div>

                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar3 me-1"></i><?= $dataInicio ?>
                    <?php if ($proj['orientador']): ?>
                        &nbsp;·&nbsp;<i class="bi bi-person-video3 me-1"></i><?= htmlspecialchars($proj['orientador']) ?>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <div class="text-center flex-fill p-2 rounded" style="background:#f8fafc">
                        <div class="fw-bold"><?= $proj['membros_ativos'] ?></div>
                        <small class="text-muted" style="font-size:.7rem">Ativos</small>
                    </div>
                    <div class="text-center flex-fill p-2 rounded" style="background:#f8fafc">
                        <div class="fw-bold"><?= $proj['total_membros'] ?></div>
                        <small class="text-muted" style="font-size:.7rem">Total</small>
                    </div>
                </div>

                <div class="mt-auto d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary flex-fill"
                        onclick="verEquipe(<?= $proj['id_projeto'] ?>, '<?= htmlspecialchars(addslashes($proj['titulo'])) ?>')">
                        <i class="bi bi-people me-1"></i>Ver Equipe
                    </button>
                    <button class="btn btn-sm btn-outline-success"
                        onclick="abrirModalVincularProjeto(<?= $proj['id_projeto'] ?>, '<?= htmlspecialchars(addslashes($proj['titulo'])) ?>')">
                        <i class="bi bi-person-plus"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item small" href="pages-adm/api-export.php?tipo=projeto&formato=html&id_projeto=<?= $proj['id_projeto'] ?>" target="_blank">
                                <i class="bi bi-file-earmark-text me-2"></i>Exportar relatório (HTML)</a></li>
                            <li><a class="dropdown-item small" href="pages-adm/api-export.php?tipo=projeto&formato=csv&id_projeto=<?= $proj['id_projeto'] ?>">
                                <i class="bi bi-filetype-csv me-2"></i>Exportar CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($listaProjetosEquipe)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>Nenhum projeto encontrado.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══ ABA: TODOS OS VÍNCULOS ═══ -->
<div id="aba-todos" class="d-none">
    <div class="content-card mb-4 p-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por nome, projeto ou função" oninput="filtrarTabela()">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select" id="filtroStatus" onchange="filtrarTabela()">
                    <option value="">Status (Todos)</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <select class="form-select" id="filtroPerfil" onchange="filtrarTabela()">
                    <option value="">Perfil (Todos)</option>
                    <option value="professor_orientador">Professor</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Lista de Participações</h5>
            <span class="text-muted small" id="contadorPart"><?= count($listaParticipacoes) ?> resultados</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tabelaPart">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>ID</th><th>USUÁRIO</th><th>PROJETO</th><th>FUNÇÃO</th><th>C.H.</th><th>ENTRADA</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaParticipacoes)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Nenhuma participação encontrada.</td></tr>
                    <?php else: foreach ($listaParticipacoes as $p): ?>
                        <tr data-status="<?= $p['status'] ?>"
                            data-perfil="<?= strtolower($p['usuario_perfil'] ?? '') ?>"
                            data-busca="<?= htmlspecialchars(strtolower($p['usuario_nome'].$p['projeto_titulo'].$p['funcao'])) ?>">
                            <td class="fw-bold text-muted">#<?= $p['id_participacao'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['usuario_nome']) ?>&background=random&size=30" class="rounded-circle" width="30" height="30">
                                    <div>
                                        <div class="fw-medium small"><?= htmlspecialchars($p['usuario_nome']) ?></div>
                                        <div class="text-muted" style="font-size:.72rem"><?= htmlspecialchars($p['usuario_email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fw-medium small"><?= htmlspecialchars($p['projeto_titulo']) ?></span></td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><?= $p['carga_horaria'] ? $p['carga_horaria'].'h' : '—' ?></td>
                            <td><?= $p['data_entrada'] ? date('d/m/Y', strtotime($p['data_entrada'])) : '—' ?></td>
                            <td><?= $p['status'] === 'ativo' ? '<span class="status-ativo">Ativo</span>' : '<span class="status-inativo">Inativo</span>' ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" title="Editar"
                                    onclick="editarPart(<?= $p['id_participacao'] ?>,<?= $p['id_projeto'] ?>,'<?= addslashes($p['usuario_nome']) ?>','<?= addslashes($p['funcao']) ?>','<?= $p['carga_horaria']??'' ?>','<?= $p['data_entrada']??'' ?>','<?= $p['data_saida']??'' ?>','<?= $p['status'] ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm <?= $p['status']==='ativo'?'btn-outline-danger':'btn-outline-success' ?> ms-1"
                                    onclick="toggleStatus(<?= $p['id_participacao'] ?>,'<?= $p['status']==='ativo'?'inativo':'ativo' ?>')">
                                    <i class="bi bi-<?= $p['status']==='ativo'?'person-x':'person-check' ?>"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="excluirPart(<?= $p['id_participacao'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ MODAL: VER EQUIPE DO PROJETO ═══ -->
<div class="modal fade" id="modalEquipe" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <div>
            <h5 class="modal-title fw-bold mb-0" id="equipe_titulo">Equipe do Projeto</h5>
            <small class="text-muted" id="equipe_sub"></small>
        </div>
        <div class="d-flex gap-2 ms-auto me-3">
            <a id="btnExportEquipeHtml" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-text me-1"></i>Relatório HTML
            </a>
            <a id="btnExportEquipeCsv" href="#" class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <!-- Abas internas: Equipe | Produções -->
        <ul class="nav nav-pills mb-3 gap-1">
            <li class="nav-item"><button class="nav-link active" onclick="abaEquipe('membros', this)"><i class="bi bi-people me-1"></i>Membros</button></li>
            <li class="nav-item"><button class="nav-link" onclick="abaEquipe('producoes', this)"><i class="bi bi-files me-1"></i>Produções e Atividades</button></li>
        </ul>

        <!-- Membros -->
        <div id="painel_membros">
            <div id="spinner_equipe" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            <div class="table-responsive d-none" id="tabela_membros_wrap">
                <table class="table table-hover align-middle" id="tabela_membros">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>MEMBRO</th><th>PERFIL</th><th>FUNÇÃO</th><th>C.H.</th><th>ENTRADA</th><th>SAÍDA</th><th>STATUS</th><th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_membros"></tbody>
                </table>
            </div>
        </div>

        <!-- Produções -->
        <div id="painel_producoes" class="d-none">
            <div id="tbody_producoes_wrap">
                <p class="text-muted text-center py-3">Selecione a aba para carregar</p>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button class="btn btn-success btn-sm" onclick="abrirModalVincularAtual()">
            <i class="bi bi-person-plus me-1"></i>Adicionar membro a este projeto
        </button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- ═══ MODAL: VINCULAR USUÁRIO ═══ -->
<div class="modal fade" id="modalVincular" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="vincular_titulo">Vincular Usuário ao Projeto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="vl_id_participacao">
        <div class="row g-3">

            <div class="col-12">
                <label class="form-label fw-medium">Projeto <span class="text-danger">*</span></label>
                <select class="form-select" id="vl_id_projeto">
                    <option value="">— Selecionar projeto —</option>
                    <?php foreach ($listaProjetos as $proj): ?>
                        <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seleção de tipo: Professor ou Aluno -->
            <div class="col-12">
                <label class="form-label fw-medium">Tipo de membro <span class="text-danger">*</span></label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn flex-fill btn-outline-info" id="btnTipoProfessor"
                        onclick="selecionarTipoMembro('professor')">
                        <i class="bi bi-person-video3 me-2"></i>Professor / Orientador
                    </button>
                    <button type="button" class="btn flex-fill btn-outline-primary" id="btnTipoAluno"
                        onclick="selecionarTipoMembro('aluno')">
                        <i class="bi bi-mortarboard me-2"></i>Aluno / Bolsista
                    </button>
                </div>
            </div>

            <div class="col-12" id="wrap_professor" style="display:none">
                <label class="form-label fw-medium">Professor <span class="text-danger">*</span></label>
                <select class="form-select" id="vl_id_professor">
                    <option value="">— Selecionar professor —</option>
                    <?php foreach ($professores as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?> — <?= htmlspecialchars($u['email']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12" id="wrap_aluno" style="display:none">
                <label class="form-label fw-medium">Aluno <span class="text-danger">*</span></label>
                <select class="form-select" id="vl_id_aluno">
                    <option value="">— Selecionar aluno —</option>
                    <?php foreach ($alunos as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?> — <?= htmlspecialchars($u['email']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">Função <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="vl_funcao" placeholder="Ex: Orientador, Bolsista FAPEMA, Voluntário">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Carga Horária (h/semana)</label>
                <input type="number" class="form-control" id="vl_carga" min="1" max="40" placeholder="Ex: 20">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Data de Entrada <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="vl_entrada">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Data de Saída</label>
                <input type="date" class="form-control" id="vl_saida">
            </div>
            <div class="col-md-6" id="wrap_status_vl" style="display:none">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" id="vl_status">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
        </div>
        <div id="vl_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarVinculo()"><i class="bi bi-save me-1"></i>Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- ═══ MODAL: EXPORTAR POR PROJETO ═══ -->
<div class="modal fade" id="modalExport" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="export_titulo">Exportar Relatório</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label fw-medium">Selecionar projeto</label>
        <select class="form-select" id="export_projeto">
            <option value="">— Todos os projetos —</option>
            <?php foreach ($listaProjetos as $proj): ?>
                <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="text-muted small mt-2">Inclui: dados do projeto, equipe completa, relatórios, publicações, eventos e produtos.</p>
      </div>
      <div class="modal-footer gap-2">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-outline-success" id="btnConfExportCsv" onclick="confirmarExport('csv')">
            <i class="bi bi-filetype-csv me-1"></i>Exportar CSV
        </button>
        <button class="btn btn-primary" id="btnConfExportHtml" onclick="confirmarExport('html')">
            <i class="bi bi-file-earmark-text me-1"></i>Exportar HTML
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function _escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

let projetoAtualId   = null;
let projetoAtualNome = '';
let tipoMembroAtual  = null;
let formatoExportAtual = 'html';

// ── Abas principais ──────────────────────────────────────────
function mostrarAba(qual) {
    document.getElementById('aba-equipes').classList.toggle('d-none', qual !== 'equipes');
    document.getElementById('aba-todos').classList.toggle('d-none', qual !== 'todos');
    document.getElementById('aba-equipes-btn').classList.toggle('active', qual === 'equipes');
    document.getElementById('aba-todos-btn').classList.toggle('active', qual === 'todos');
}

// ── Filtro de cards de projeto ───────────────────────────────
function filtrarCards() {
    const busca = document.getElementById('filtroProjeto').value.toLowerCase();
    document.querySelectorAll('.projeto-card').forEach(card => {
        card.style.display = card.dataset.titulo.includes(busca) ? '' : 'none';
    });
}

// ── Ver equipe de um projeto ─────────────────────────────────
function verEquipe(idProjeto, nomeProjeto) {
    projetoAtualId   = idProjeto;
    projetoAtualNome = nomeProjeto;
    document.getElementById('equipe_titulo').textContent = nomeProjeto;
    document.getElementById('equipe_sub').textContent    = 'Carregando equipe...';
    document.getElementById('spinner_equipe').classList.remove('d-none');
    document.getElementById('tabela_membros_wrap').classList.add('d-none');
    document.getElementById('tbody_producoes_wrap').innerHTML = '<p class="text-muted text-center py-3">Clique em "Produções e Atividades" para carregar</p>';
    document.getElementById('btnExportEquipeHtml').href = `pages-adm/api-export.php?tipo=projeto&formato=html&id_projeto=${idProjeto}`;
    document.getElementById('btnExportEquipeCsv').href  = `pages-adm/api-export.php?tipo=projeto&formato=csv&id_projeto=${idProjeto}`;

    // Resetar aba interna para "membros"
    document.querySelectorAll('.nav-pills .nav-link').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.nav-pills .nav-link')[0].classList.add('active');
    document.getElementById('painel_membros').classList.remove('d-none');
    document.getElementById('painel_producoes').classList.add('d-none');

    carregarMembros(idProjeto);
    new bootstrap.Modal(document.getElementById('modalEquipe')).show();
}

function carregarMembros(idProjeto) {
    fetch(`pages-adm/api-participacoes.php?acao=listarPorProjeto&id_projeto=${idProjeto}`, { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            document.getElementById('spinner_equipe').classList.add('d-none');
            const tbody = document.getElementById('tbody_membros');
            const wrap  = document.getElementById('tabela_membros_wrap');
            const membros = data.participantes || [];
            document.getElementById('equipe_sub').textContent = `${membros.length} membro(s)`;

            if (!membros.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Nenhum membro neste projeto.</td></tr>';
                wrap.classList.remove('d-none');
                return;
            }
            tbody.innerHTML = membros.map(m => {
                const perfBadge = (() => {
                    const p = (m.usuario_perfil || '').toLowerCase();
                    if (p.includes('professor') || p.includes('orientador')) return '<span class="badge bg-info text-dark">Professor</span>';
                    if (p.includes('admin')) return '<span class="badge bg-secondary">Admin</span>';
                    return '<span class="badge bg-light text-dark border">Aluno</span>';
                })();
                const stBadge = m.status === 'ativo' ? '<span class="status-ativo">Ativo</span>' : '<span class="status-inativo">Inativo</span>';
                const entrada = m.data_entrada ? new Date(m.data_entrada).toLocaleDateString('pt-BR') : '—';
                const saida   = m.data_saida   ? new Date(m.data_saida).toLocaleDateString('pt-BR')   : '—';
                const avatar  = `https://ui-avatars.com/api/?name=${encodeURIComponent(m.usuario_nome)}&background=random&size=30`;
                return `<tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="${avatar}" class="rounded-circle" width="30" height="30">
                            <div>
                                <div class="fw-medium small">${_escHtml(m.usuario_nome)}</div>
                                <div class="text-muted" style="font-size:.7rem">${_escHtml(m.usuario_email)}</div>
                            </div>
                        </div>
                    </td>
                    <td>${perfBadge}</td>
                    <td class="fw-medium">${_escHtml(m.funcao)}</td>
                    <td>${m.carga_horaria ? m.carga_horaria+'h' : '—'}</td>
                    <td>${entrada}</td><td>${saida}</td><td>${stBadge}</td>
                    <td>
                        <button class="btn btn-xs btn-outline-danger" title="Encerrar"
                            onclick="toggleStatus(${m.id_participacao},'${m.status==='ativo'?'inativo':'ativo'}',true)">
                            <i class="bi bi-${m.status==='ativo'?'person-x':'person-check'}"></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');
            wrap.classList.remove('d-none');
        })
        .catch(err => {
            document.getElementById('spinner_equipe').classList.add('d-none');
            document.getElementById('tbody_membros').innerHTML = `<tr><td colspan="8" class="text-danger text-center">Erro: ${err.message}</td></tr>`;
            document.getElementById('tabela_membros_wrap').classList.remove('d-none');
        });
}

function carregarProducoesProjeto(idProjeto) {
    document.getElementById('tbody_producoes_wrap').innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
    fetch(`pages-adm/api-export.php?tipo=projeto&formato=json&id_projeto=${idProjeto}`, { cache: 'no-store' })
        .then(r => r.text())
        .then(html => {
            // Como o endpoint retorna HTML, vamos listar via query separada
            document.getElementById('tbody_producoes_wrap').innerHTML =
                `<div class="text-center py-2">
                    <a href="pages-adm/api-export.php?tipo=projeto&formato=html&id_projeto=${idProjeto}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Abrir relatório completo de produções em nova aba
                    </a>
                </div>`;
        });
}

function abaEquipe(painel, btn) {
    document.getElementById('painel_membros').classList.toggle('d-none', painel !== 'membros');
    document.getElementById('painel_producoes').classList.toggle('d-none', painel !== 'producoes');
    document.querySelectorAll('.nav-pills .nav-link').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (painel === 'producoes' && projetoAtualId) carregarProducoesProjeto(projetoAtualId);
}

// ── Modal Vincular ───────────────────────────────────────────
function abrirModalVincular() {
    projetoAtualId   = null;
    document.getElementById('vincular_titulo').textContent = 'Vincular Usuário ao Projeto';
    document.getElementById('vl_id_projeto').value   = '';
    document.getElementById('vl_id_projeto').disabled = false;
    resetarFormVincular();
    new bootstrap.Modal(document.getElementById('modalVincular')).show();
}
function abrirModalVincularProjeto(idProjeto, nomeProjeto) {
    projetoAtualId = idProjeto;
    document.getElementById('vincular_titulo').textContent = `Vincular ao: ${nomeProjeto}`;
    document.getElementById('vl_id_projeto').value   = idProjeto;
    document.getElementById('vl_id_projeto').disabled = true;
    resetarFormVincular();
    new bootstrap.Modal(document.getElementById('modalVincular')).show();
}
function abrirModalVincularAtual() {
    bootstrap.Modal.getInstance(document.getElementById('modalEquipe')).hide();
    setTimeout(() => abrirModalVincularProjeto(projetoAtualId, projetoAtualNome), 300);
}

function resetarFormVincular() {
    document.getElementById('vl_id_participacao').value = '';
    document.getElementById('vl_funcao').value   = '';
    document.getElementById('vl_carga').value    = '';
    document.getElementById('vl_entrada').value  = '';
    document.getElementById('vl_saida').value    = '';
    document.getElementById('vl_status').value   = 'ativo';
    document.getElementById('vl_feedback').innerHTML = '';
    tipoMembroAtual = null;
    ['professor','aluno'].forEach(t => {
        document.getElementById('wrap_' + t).style.display = 'none';
        document.getElementById('btnTipo' + t.charAt(0).toUpperCase() + t.slice(1)).className =
            `btn flex-fill btn-outline-${t==='professor'?'info':'primary'}`;
    });
    document.getElementById('wrap_status_vl').style.display = 'none';
}

function selecionarTipoMembro(tipo) {
    tipoMembroAtual = tipo;
    const outro = tipo === 'professor' ? 'aluno' : 'professor';
    document.getElementById('wrap_' + tipo).style.display   = '';
    document.getElementById('wrap_' + outro).style.display  = 'none';
    const corAtivo  = tipo === 'professor' ? 'info' : 'primary';
    const corOutro  = tipo === 'professor' ? 'primary' : 'info';
    const label = tipo.charAt(0).toUpperCase() + tipo.slice(1);
    const labelOutro = outro.charAt(0).toUpperCase() + outro.slice(1);
    document.getElementById('btnTipo' + label).className      = `btn flex-fill btn-${corAtivo}`;
    document.getElementById('btnTipo' + labelOutro).className = `btn flex-fill btn-outline-${corOutro}`;

    // Sugerir função
    if (!document.getElementById('vl_funcao').value) {
        document.getElementById('vl_funcao').value = tipo === 'professor' ? 'Professor Orientador' : 'Bolsista';
    }
}

function salvarVinculo() {
    const idPart = document.getElementById('vl_id_participacao').value;
    const idProj = document.getElementById('vl_id_projeto').value;
    const fb = document.getElementById('vl_feedback');

    let idUsuario = '';
    if (!idPart) {
        if (!tipoMembroAtual) { fb.innerHTML = '<div class="alert alert-warning">Selecione o tipo de membro (professor ou aluno).</div>'; return; }
        idUsuario = tipoMembroAtual === 'professor'
            ? document.getElementById('vl_id_professor').value
            : document.getElementById('vl_id_aluno').value;
        if (!idProj)     { fb.innerHTML = '<div class="alert alert-danger">Selecione o projeto.</div>'; return; }
        if (!idUsuario)  { fb.innerHTML = '<div class="alert alert-danger">Selecione o usuário.</div>'; return; }
    }
    if (!document.getElementById('vl_funcao').value.trim()) {
        fb.innerHTML = '<div class="alert alert-danger">Informe a função.</div>'; return;
    }

    const acao = idPart ? 'atualizar' : 'criar';
    const body = new FormData();
    if (idPart)   body.append('id_participacao', idPart);
    if (!idPart)  body.append('id_usuario', idUsuario);
    body.append('id_projeto',    idProj);
    body.append('funcao',        document.getElementById('vl_funcao').value);
    body.append('carga_horaria', document.getElementById('vl_carga').value);
    body.append('data_entrada',  document.getElementById('vl_entrada').value);
    body.append('data_saida',    document.getElementById('vl_saida').value);
    body.append('status',        document.getElementById('vl_status').value);

    fetch(`pages-adm/api-participacoes.php?acao=${acao}`, { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('modalVincular')).hide(); carregarPagina('participacoes'); }, 1200);
        })
        .catch(() => { fb.innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
}

function editarPart(id, idProj, nome, funcao, cargaH, entrada, saida, status) {
    document.getElementById('vincular_titulo').textContent = `Editar: ${nome}`;
    document.getElementById('vl_id_participacao').value = id;
    document.getElementById('vl_id_projeto').value      = idProj;
    document.getElementById('vl_id_projeto').disabled   = true;
    document.getElementById('vl_funcao').value   = funcao;
    document.getElementById('vl_carga').value    = cargaH;
    document.getElementById('vl_entrada').value  = entrada;
    document.getElementById('vl_saida').value    = saida;
    document.getElementById('vl_status').value   = status;
    document.getElementById('wrap_status_vl').style.display = '';
    document.getElementById('vl_feedback').innerHTML = '';
    tipoMembroAtual = 'editar';
    // Ocultar botões de tipo quando editando
    document.getElementById('wrap_professor').style.display = 'none';
    document.getElementById('wrap_aluno').style.display     = 'none';
    new bootstrap.Modal(document.getElementById('modalVincular')).show();
}

function toggleStatus(id, novoStatus, recarregarEquipe = false) {
    if (!confirm(`Confirma alteração para "${novoStatus}"?`)) return;
    const body = new FormData();
    body.append('id_participacao', id);
    body.append('status', novoStatus);
    fetch('pages-adm/api-participacoes.php?acao=alterarStatus', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            alert(data.mensagem);
            if (data.sucesso) {
                if (recarregarEquipe && projetoAtualId) carregarMembros(projetoAtualId);
                else carregarPagina('participacoes');
            }
        });
}

function excluirPart(id) {
    if (!confirm('Excluir este vínculo permanentemente?')) return;
    const body = new FormData();
    body.append('id_participacao', id);
    fetch('pages-adm/api-participacoes.php?acao=excluir', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('participacoes'); });
}

// ── Exportação ───────────────────────────────────────────────
function exportar(tipo, formato) {
    if (tipo === 'geral') {
        window.open(`pages-adm/api-export.php?tipo=geral&formato=${formato}`, '_blank');
    }
}
function abrirExportProjeto(formato) {
    formatoExportAtual = formato;
    document.getElementById('export_titulo').textContent = formato === 'html' ? 'Exportar Relatório HTML' : 'Exportar CSV';
    new bootstrap.Modal(document.getElementById('modalExport')).show();
}
function confirmarExport(formato) {
    const idProj = document.getElementById('export_projeto').value;
    const tipo   = idProj ? 'projeto' : 'geral';
    const url    = `pages-adm/api-export.php?tipo=${tipo}&formato=${formato}${idProj ? '&id_projeto='+idProj : ''}`;
    window.open(url, '_blank');
    bootstrap.Modal.getInstance(document.getElementById('modalExport')).hide();
}

// ── Filtro tabela geral ──────────────────────────────────────
function filtrarTabela() {
    const busca  = document.getElementById('filtroBusca').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value;
    const perfil = document.getElementById('filtroPerfil').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaPart tbody tr[data-status]');
    let n = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!status || tr.dataset.status === status)
                && (!perfil || tr.dataset.perfil.includes(perfil));
        tr.style.display = ok ? '' : 'none';
        if (ok) n++;
    });
    document.getElementById('contadorPart').textContent = n + ' resultados';
}
</script>
