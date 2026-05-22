<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Usuários</h3>
        <p class="text-muted mb-0">Gerencie acessos, perfis e status dos usuários do sistema</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalNovoUsuario()"><i class="bi bi-person-plus me-2"></i>Novo Usuário</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4><small class="text-muted">Total de Usuários</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-person-check"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4><small class="text-muted">Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-person-x"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['inativos'] ?></h4><small class="text-muted">Inativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-person-badge"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['admins'] ?></h4><small class="text-muted">Administradores</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por nome, email ou matrícula" oninput="filtrarUsuarios()">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" id="filtroStatus" onchange="filtrarUsuarios()">
                <option value="">Status</option>
                <option value="ativo">Ativos</option>
                <option value="inativo">Inativos</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroPerfil" onchange="filtrarUsuarios()">
                <option value="">Perfil (Todos)</option>
                <option value="admin">Administrador</option>
                <option value="professor_orientador">Professor</option>
                <option value="aluno">Aluno</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Lista de Usuários</h5>
        <div class="text-muted small" id="contadorUsuarios"><?= count($listaUsuarios) ?> resultados</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaUsuarios">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>NOME</th><th>EMAIL</th><th>MATRÍCULA</th><th>PERFIL</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaUsuarios)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaUsuarios as $user): ?>
                    <tr data-status="<?= htmlspecialchars($user['status']) ?>"
                        data-perfil="<?= htmlspecialchars(strtolower($user['perfil'])) ?>"
                        data-busca="<?= htmlspecialchars(strtolower($user['nome'].$user['email'].($user['matricula']??''))) ?>">
                        <td class="fw-bold text-muted">#<?= htmlspecialchars($user['id_usuario']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['nome']) ?>&background=random" class="rounded-circle" width="32">
                                <span class="fw-medium"><?= htmlspecialchars($user['nome']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['matricula'] ?? '—') ?></td>
                        <td>
                            <?php
                                $perfil = strtolower($user['perfil']);
                                if (str_contains($perfil, 'admin')) echo '<span class="badge bg-secondary">Administrador</span>';
                                elseif (str_contains($perfil, 'orientador') || str_contains($perfil, 'professor')) echo '<span class="badge bg-info text-dark">Professor</span>';
                                else echo '<span class="badge bg-light text-dark border">Aluno</span>';
                            ?>
                        </td>
                        <td>
                            <?php if ($user['status'] === 'ativo'): ?>
                                <span class="status-ativo">Ativo</span>
                            <?php else: ?>
                                <span class="status-inativo">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" title="Editar"
                                onclick="editarUsuario(<?= $user['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($user['nome'])) ?>', '<?= htmlspecialchars(addslashes($user['email'])) ?>', '<?= htmlspecialchars(addslashes($user['matricula'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($user['perfil'])) ?>', '<?= htmlspecialchars(addslashes($user['curso'] ?? '')) ?>', '<?= $user['status'] ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning ms-1" title="Redefinir Senha"
                                onclick="abrirModalSenha(<?= $user['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($user['nome'])) ?>')">
                                <i class="bi bi-key"></i>
                            </button>
                            <?php if ($user['status'] === 'inativo'): ?>
                                <button class="btn btn-sm btn-outline-success ms-1" title="Ativar"
                                    onclick="alterarStatusUsuario(<?= $user['id_usuario'] ?>, 'ativo')">
                                    <i class="bi bi-person-check"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-danger ms-1" title="Desativar"
                                    onclick="alterarStatusUsuario(<?= $user['id_usuario'] ?>, 'inativo')">
                                    <i class="bi bi-person-x"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL CRIAR/EDITAR USUÁRIO -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalUsuarioTitulo">Novo Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="usr_id">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Nome Completo <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="usr_nome" placeholder="Nome completo">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="usr_email" placeholder="email@exemplo.com">
            </div>
            <div id="campoSenhaWrap" class="col-md-6">
                <label class="form-label fw-medium">Senha <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="usr_senha" placeholder="Mínimo 6 caracteres">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Matrícula</label>
                <input type="text" class="form-control" id="usr_matricula" placeholder="Nº de matrícula">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Perfil <span class="text-danger">*</span></label>
                <select class="form-select" id="usr_perfil">
                    <option value="aluno">Aluno</option>
                    <option value="professor_orientador">Professor Orientador</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Curso</label>
                <input type="text" class="form-control" id="usr_curso" placeholder="Ex: Ciência da Computação">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" id="usr_status">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
        </div>
        <div id="usr_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarUsuario()"><i class="bi bi-save me-1"></i>Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL REDEFINIR SENHA -->
<div class="modal fade" id="modalSenha" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Redefinir Senha</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="senha_id_usuario">
        <p id="senha_nome_usuario" class="text-muted mb-3"></p>
        <label class="form-label fw-medium">Nova Senha <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="nova_senha" placeholder="Mínimo 6 caracteres">
        <div id="senha_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-warning" onclick="salvarNovaSenha()"><i class="bi bi-key me-1"></i>Redefinir</button>
      </div>
    </div>
  </div>
</div>

<script>
function abrirModalNovoUsuario() {
    document.getElementById('modalUsuarioTitulo').textContent = 'Novo Usuário';
    document.getElementById('usr_id').value = '';
    ['usr_nome','usr_email','usr_senha','usr_matricula','usr_curso'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('usr_perfil').value  = 'aluno';
    document.getElementById('usr_status').value  = 'ativo';
    document.getElementById('campoSenhaWrap').style.display = '';
    document.getElementById('usr_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

function editarUsuario(id, nome, email, matricula, perfil, curso, status) {
    document.getElementById('modalUsuarioTitulo').textContent = 'Editar Usuário';
    document.getElementById('usr_id').value        = id;
    document.getElementById('usr_nome').value      = nome;
    document.getElementById('usr_email').value     = email;
    document.getElementById('usr_matricula').value = matricula;
    document.getElementById('usr_perfil').value    = perfil;
    document.getElementById('usr_curso').value     = curso;
    document.getElementById('usr_status').value    = status;
    document.getElementById('campoSenhaWrap').style.display = 'none';
    document.getElementById('usr_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

function salvarUsuario() {
    const id = document.getElementById('usr_id').value;
    const action = id ? 'pages-adm/api-usuarios.php?acao=atualizar' : 'pages-adm/api-usuarios.php?acao=criar';
    const body = new FormData();
    if (id) body.append('id_usuario', id);
    body.append('nome',      document.getElementById('usr_nome').value);
    body.append('email',     document.getElementById('usr_email').value);
    body.append('senha',     document.getElementById('usr_senha').value);
    body.append('matricula', document.getElementById('usr_matricula').value);
    body.append('perfil',    document.getElementById('usr_perfil').value);
    body.append('curso',     document.getElementById('usr_curso').value);
    body.append('status',    document.getElementById('usr_status').value);

    fetch(action, { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('usr_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide(); carregarPagina('usuarios'); }, 1200);
        })
        .catch(() => { document.getElementById('usr_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
}

function alterarStatusUsuario(id, status) {
    if (!confirm(`Confirma alteração do status para "${status}"?`)) return;
    const body = new FormData();
    body.append('id_usuario', id);
    body.append('status', status);
    fetch('pages-adm/api-usuarios.php?acao=alterarStatus', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('usuarios'); })
        .catch(() => alert('Erro de comunicação.'));
}

function abrirModalSenha(id, nome) {
    document.getElementById('senha_id_usuario').value = id;
    document.getElementById('senha_nome_usuario').textContent = 'Redefinindo senha de: ' + nome;
    document.getElementById('nova_senha').value = '';
    document.getElementById('senha_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalSenha')).show();
}

function salvarNovaSenha() {
    const body = new FormData();
    body.append('id_usuario', document.getElementById('senha_id_usuario').value);
    body.append('nova_senha', document.getElementById('nova_senha').value);
    fetch('pages-adm/api-usuarios.php?acao=redefinirSenha', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('senha_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('modalSenha')).hide(), 1500);
        })
        .catch(() => { document.getElementById('senha_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
}

function filtrarUsuarios() {
    const busca  = document.getElementById('filtroBusca').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value;
    const perfil = document.getElementById('filtroPerfil').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaUsuarios tbody tr[data-status]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!status || tr.dataset.status === status)
                && (!perfil || tr.dataset.perfil.includes(perfil));
        tr.style.display = ok ? '' : 'none';
        if (ok) visiveis++;
    });
    document.getElementById('contadorUsuarios').textContent = visiveis + ' resultados';
}
</script>
