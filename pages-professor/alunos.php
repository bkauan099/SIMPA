<?php
require_once 'conexao/conexao.php';

// ID fixado conforme solicitado
$id_professor = 5;

try {
    // 1. Estatísticas
    $sql_stats = "SELECT 
        COUNT(DISTINCT pa.id_usuario) as total,
        COUNT(DISTINCT CASE WHEN u.status = 'ativo' THEN pa.id_usuario END) as ativos,
        SUM(pa.carga_horaria) as carga_total
        FROM participacao pa
        JOIN usuarios u ON pa.id_usuario = u.id_usuario
        WHERE pa.id_projeto IN (
            SELECT id_projeto FROM participacao 
            WHERE id_usuario = :id_professor 
            AND (funcao ILIKE '%professor%' OR funcao ILIKE '%coordenador%' OR funcao ILIKE '%orientador%')
        ) AND pa.id_usuario != :id_professor_ignore";

    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute(['id_professor' => $id_professor, 'id_professor_ignore' => $id_professor]);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    // 2. Lista de Alunos
    $sql_alunos = "SELECT 
        u.nome, u.email, u.matricula, u.status as status_usuario,
        p.titulo as projeto_nome,
        pa.id_projeto,
        pa.carga_horaria,
        pa.funcao,
        u.id_usuario
        FROM participacao pa
        JOIN usuarios u ON pa.id_usuario = u.id_usuario
        JOIN projetos p ON pa.id_projeto = p.id_projeto
        WHERE pa.id_projeto IN (
            SELECT id_projeto FROM participacao 
            WHERE id_usuario = :id_professor 
            AND (funcao ILIKE '%professor%' OR funcao ILIKE '%coordenador%' OR funcao ILIKE '%orientador%')
        ) AND pa.id_usuario != :id_professor_list
        ORDER BY u.nome ASC";

    $stmt_alunos = $pdo->prepare($sql_alunos);
    $stmt_alunos->execute(['id_professor' => $id_professor, 'id_professor_list' => $id_professor]);
    $alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);

    // 3. Dropdown de Projetos para o Modal Adicionar
    $sql_meus_projetos = "SELECT p.id_projeto, p.titulo 
                          FROM projetos p
                          JOIN participacao pa ON p.id_projeto = pa.id_projeto
                          WHERE pa.id_usuario = :id_prof 
                          AND (pa.funcao ILIKE '%professor%' OR pa.funcao ILIKE '%coordenador%' OR pa.funcao ILIKE '%orientador%')
                          ORDER BY p.titulo ASC";
    $stmt_meus_projs = $pdo->prepare($sql_meus_projetos);
    $stmt_meus_projs->execute(['id_prof' => $id_professor]);
    $meus_projetos = $stmt_meus_projs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro no Banco: " . $e->getMessage() . "</div>";
    exit;
}
?>

<style>
    #resultados_busca_geral {
        z-index: 2000 !important;
        background-color: white !important;
        cursor: pointer;
        display: none;
    }

    #resultados_busca_geral .list-group-item {
        cursor: pointer !important;
        pointer-events: auto !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Meus Alunos</h3>
        <p class="text-muted mb-0">Gestão de participantes vinculados aos seus projetos coordenados</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalAdicionar()">
        <i class="bi bi-person-plus me-2"></i>Adicionar Aluno
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $stats['total'] ?? 0 ?></h4>
                <small class="text-muted">Alunos Orientados</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-person-check"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $stats['ativos'] ?? 0 ?></h4>
                <small class="text-muted">Participantes Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= number_format($stats['carga_total'] ?? 0, 0, ',', '.') ?>h</h4>
                <small class="text-muted">Carga Horária Total</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-award"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">-</h4>
                <small class="text-muted">Certificados Gerados</small>
            </div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtro_aluno" class="form-control border-start-0" placeholder="Buscar por nome ou matrícula...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtro_projeto">
                <option value="">Todos os Projetos</option>
                <?php foreach (array_unique(array_column($alunos, 'projeto_nome')) as $p): ?>
                    <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtro_status">
                <option value="">Status (Todos)</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ALUNO / MATRÍCULA</th>
                    <th>PROJETO</th>
                    <th>FUNÇÃO</th>
                    <th>CARGA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody id="tabela_alunos_corpo">
                <?php if (!empty($alunos)): ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr class="linha-aluno">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($aluno['nome']) ?>&background=random" class="rounded-circle" width="32">
                                    <div>
                                        <span class="fw-medium d-block nome-txt"><?= htmlspecialchars($aluno['nome']) ?></span>
                                        <small class="text-muted matricula-txt"><?= htmlspecialchars($aluno['matricula']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="projeto-txt"><?= htmlspecialchars($aluno['projeto_nome']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($aluno['funcao'] ?? 'Bolsista') ?></span></td>
                            <td><?= $aluno['carga_horaria'] ?>h</td>
                            <td>
                                <?php
                                $status = strtolower($aluno['status_usuario'] ?? 'inativo');
                                $classe = ($status === 'ativo') ? 'status-ativo' : 'status-inativo';
                                ?>
                                <span class="<?= $classe ?> status-txt"><?= ucfirst($status) ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" title="Ver Detalhes"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-danger ms-1" title="Remover" onclick="confirmarRemocaoGeral(<?= $aluno['id_usuario'] ?>, <?= $aluno['id_projeto'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalAdicionarAluno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Vincular Novo Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 position-relative">
                    <label class="form-label small text-muted">Buscar Aluno</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="busca_aluno_geral" class="form-control border-start-0" placeholder="Nome ou Matrícula...">
                    </div>
                    <div id="resultados_busca_geral" class="list-group position-absolute w-100 shadow-lg" style="z-index: 1050; display: none;"></div>
                    <input type="hidden" id="id_aluno_vincular">
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Projeto</label>
                    <select class="form-select" id="projeto_vincular">
                        <option value="">Selecione o projeto...</option>
                        <?php foreach ($meus_projetos as $proj): ?>
                            <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small text-muted">Carga Horária (Total)</label>
                    <div class="input-group">
                        <input type="number" id="ch_vincular" class="form-control" placeholder="Ex: 100">
                        <span class="input-group-text bg-light text-muted">horas</span>
                    </div>
                </div>

                <button class="btn btn-primary w-100 fw-bold" id="btnConfirmarVinculo" onclick="executarVinculoGeral()">
                    Vincular Aluno
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarRemocaoGeral" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                <h5 class="fw-bold mt-3">Remover Aluno?</h5>
                <p class="text-muted">O aluno será desvinculado deste projeto.</p>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarRemocaoGeral" onclick="executarRemocaoGeral()">Remover Agora</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAvisoDuplicado" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="bi bi-info-circle-fill text-warning" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-2">Aluno já vinculado</h5>
                <p class="text-muted">Este aluno já faz parte deste projeto.</p>
                <button class="btn btn-warning w-100 text-white fw-bold" onclick="fecharAvisoDuplicado()">ENTENDI</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let alunoParaRemover = null;
        let projetoDeOrigem = null;

        // --- FUNÇÕES DE EXCLUSÃO ---
        window.confirmarRemocaoGeral = function(idU, idP) {
            alunoParaRemover = idU;
            projetoDeOrigem = idP;
            new bootstrap.Modal(document.getElementById('modalConfirmarRemocaoGeral')).show();
        };

        window.executarRemocaoGeral = function() {
            const btn = document.getElementById('btnConfirmarRemocaoGeral');

            // Salva o texto original para caso ocorra algum erro
            const originalHTML = btn.innerHTML;

            // Ativa o spinner e desabilita o botão
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Removendo...`;

            const formData = new FormData();
            formData.append('acao', 'remover');
            formData.append('id_usuario', alunoParaRemover);
            formData.append('id_projeto', projetoDeOrigem);

            fetch('controllers/controller-professor/gerenciar-participacao.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        location.reload();
                    } else {
                        alert(data.mensagem);
                        // Se der erro, volta o botão ao estado normal
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                })
                .catch(err => {
                    console.error("Erro na remoção:", err);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
        };

        // --- FUNÇÕES DE ADIÇÃO (VINCULAR) ---
        window.abrirModalAdicionar = function() {
            new bootstrap.Modal(document.getElementById('modalAdicionarAluno')).show();
        };

        // --- BUSCA DINÂMICA ---
        document.getElementById('busca_aluno_geral').addEventListener('input', function() {
            let termo = this.value.trim();
            const resDiv = document.getElementById('resultados_busca_geral');

            if (termo.length < 2) {
                resDiv.style.display = 'none';
                return;
            }

            fetch(`controllers/controller-professor/buscar-alunos.php?busca=${termo}`)
                .then(res => res.text())
                .then(html => {
                    resDiv.innerHTML = html;
                    resDiv.style.display = 'block';
                });
        });

        // --- SOLUÇÃO BLINDADA: CAPTURA DE CLIQUE GLOBAL ---
        // Usamos 'mousedown' porque ele acontece ANTES do input perder o foco
        document.addEventListener('mousedown', function(e) {
            // Verifica se o que você clicou é o item da lista ou algo dentro dele
            const item = e.target.closest('.item-aluno-lista');

            if (item) {
                // Se clicou, a gente pega os dados que guardamos no 'data-'
                const nome = item.getAttribute('data-nome');
                const id = item.getAttribute('data-id');

                console.log("Aluno capturado:", nome, id); // Verifique no F12 se isso aparece

                document.getElementById('busca_aluno_geral').value = nome;
                document.getElementById('id_aluno_vincular').value = id;
                document.getElementById('resultados_busca_geral').style.display = 'none';

                // Impede que o clique faça qualquer outra coisa
                e.preventDefault();
            } else {
                // Se clicar em qualquer outra coisa que não seja o input ou a lista, fecha a lista
                const resDiv = document.getElementById('resultados_busca_geral');
                const inputBusca = document.getElementById('busca_aluno_geral');
                if (resDiv && e.target !== inputBusca && !resDiv.contains(e.target)) {
                    resDiv.style.display = 'none';
                }
            }
        });

        window.executarVinculoGeral = function() {
            const idU = document.getElementById('id_aluno_vincular').value;
            const idP = document.getElementById('projeto_vincular').value;
            const ch = document.getElementById('ch_vincular').value;
            const btn = document.getElementById('btnConfirmarVinculo');

            if (!idU || !idP || !ch) {
                alert("Preencha todos os campos.");
                return;
            }

            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Vinculando...`;

            const formData = new FormData();
            formData.append('acao', 'vincular');
            formData.append('id_usuario', idU);
            formData.append('id_projeto', idP);
            formData.append('carga_horaria', ch);

            fetch('controllers/controller-professor/gerenciar-participacao.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        location.reload();
                    } else if (data.mensagem.includes("já está cadastrado")) {
                        // EXIBE O MODAL DE ERRO IGUAL NA OUTRA PÁGINA
                        const modalAviso = new bootstrap.Modal(document.getElementById('modalAvisoDuplicado'));
                        modalAviso.show();

                        // Restaura o botão para permitir correção
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    } else {
                        alert(data.mensagem);
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                })
                .catch(err => {
                    console.error("Erro na requisição:", err);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
        };

        window.fecharAvisoDuplicado = function() {
            const m = document.getElementById('modalAvisoDuplicado');
            bootstrap.Modal.getInstance(m).hide();
        };

        // --- FILTRO DA TABELA ---
        const filtrar = () => {
            const busca = document.getElementById('filtro_aluno').value.toLowerCase().trim();
            const projeto = document.getElementById('filtro_projeto').value.toLowerCase();
            const status = document.getElementById('filtro_status').value.toLowerCase();
            const linhas = document.querySelectorAll('.linha-aluno');

            linhas.forEach(linha => {
                const nome = linha.querySelector('.nome-txt').textContent.toLowerCase();
                const mat = linha.querySelector('.matricula-txt').textContent.toLowerCase();
                const proj = linha.querySelector('.projeto-txt').textContent.toLowerCase();
                const st = linha.querySelector('.status-txt').textContent.toLowerCase();

                const bateBusca = nome.startsWith(busca) || mat.startsWith(busca);
                const bateProj = projeto === "" || proj === projeto;
                const bateStatus = status === "" || st === status;

                linha.style.display = (bateBusca && bateProj && bateStatus) ? "" : "none";
            });
        };

        document.getElementById('filtro_aluno').addEventListener('input', filtrar);
        document.getElementById('filtro_projeto').addEventListener('change', filtrar);
        document.getElementById('filtro_status').addEventListener('change', filtrar);
    })();
</script>