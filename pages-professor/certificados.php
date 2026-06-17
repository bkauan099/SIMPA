<?php
require_once 'conexao/conexao.php';

$id_professor = $_SESSION['id_usuario'] ?? 0;

try {
    // Projetos coordenados pelo professor
    $stmt = $pdo->prepare("
        SELECT p.id_projeto, p.titulo
        FROM projetos p
        JOIN participacao pa ON p.id_projeto = pa.id_projeto
        WHERE pa.id_usuario = :id
          AND (pa.funcao ILIKE '%professor%' OR pa.funcao ILIKE '%coordenador%' OR pa.funcao ILIKE '%orientador%')
        ORDER BY p.titulo ASC
    ");
    $stmt->execute([':id' => $id_professor]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Certificados já emitidos
    $ids_projetos = array_column($projetos, 'id_projeto');
    $certificados = [];
    if (!empty($ids_projetos)) {
        $placeholders = implode(',', array_fill(0, count($ids_projetos), '?'));
        $stmt2 = $pdo->prepare("
            SELECT
                pr.id_producao,
                pr.titulo,
                pr.tipo            AS nome_arquivo,
                pr.caminho,
                pr.data_registro,
                pj.titulo          AS projeto,
                pj.id_projeto,
                u.nome             AS nome_aluno,
                u.matricula        AS matricula_aluno
            FROM producoes pr
            JOIN projetos  pj ON pj.id_projeto = pr.id_projeto
            LEFT JOIN usuarios u ON pr.caminho LIKE CONCAT('uploads/certificados/aluno/', u.matricula, '/%')
            WHERE pr.id_projeto IN ($placeholders)
              AND pr.caminho LIKE 'uploads/certificados/aluno/%'
            ORDER BY pr.data_registro DESC
        ");
        $stmt2->execute($ids_projetos);
        $certificados = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    $total    = count($certificados);
    $recentes = count(array_filter($certificados, fn($c) =>
        !empty($c['data_registro']) && new DateTime($c['data_registro']) >= new DateTime('-30 days')));

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

function iconeArquivoProfCert(string $ext): string {
    return match(true) {
        $ext === 'pdf'                         => 'bi-file-earmark-pdf text-danger',
        in_array($ext, ['doc','docx'])         => 'bi-file-earmark-word text-primary',
        in_array($ext, ['jpg','jpeg','png',
                         'gif','webp','svg'])  => 'bi-file-earmark-image text-info',
        default                                => 'bi-file-earmark text-muted',
    };
}
?>

<!-- Banner -->
<div style="
    background: linear-gradient(135deg, #0a1628 0%, #1e3a8a 30%, #1d4ed8 65%, #4f93f5 100%);
    border-radius: 20px;
    padding: 32px 32px 28px;
    margin-bottom: 24px;
    box-shadow: 0 10px 40px rgba(10,22,40,0.45), 0 2px 8px rgba(29,78,216,0.25);
    position: relative;
    overflow: hidden;
    min-height: 150px;
">
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);background-size:22px 22px;pointer-events:none;"></div>
    <div style="position:absolute;top:50%;right:48px;transform:translateY(-50%);width:190px;height:190px;border-radius:50%;border:1px solid rgba(255,255,255,0.07);pointer-events:none;"></div>

    <div style="position:absolute;right:32px;top:50%;transform:translateY(-50%);width:86px;height:86px;border-radius:50%;background:rgba(251,191,36,0.12);border:2px solid rgba(251,191,36,0.3);display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px rgba(251,191,36,0.2);">
        <i class="bi bi-award-fill" style="font-size:2.6rem;color:#fbbf24;filter:drop-shadow(0 0 10px rgba(251,191,36,0.55));"></i>
    </div>

    <div style="position:relative;z-index:1;padding-right:130px;">
        <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(251,191,36,0.15);border:1px solid rgba(251,191,36,0.38);border-radius:20px;padding:4px 13px;font-size:0.67rem;font-weight:700;color:#fbbf24;letter-spacing:0.5px;margin-bottom:13px;">
            <i class="bi bi-patch-check-fill"></i> EMISSÃO DE CERTIFICADOS
        </div>
        <h2 style="color:#fff;font-weight:900;margin:0 0 6px;font-size:1.75rem;letter-spacing:-0.3px;line-height:1.1;">
            Certificados
        </h2>
        <p style="color:rgba(255,255,255,0.58);margin:0 0 18px;font-size:0.82rem;">
            Emita e gerencie certificados para os alunos dos seus projetos
        </p>
        <div style="width:44px;height:3px;border-radius:2px;background:linear-gradient(90deg,#fbbf24,rgba(251,191,36,0.15));margin-bottom:15px;"></div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:10px 18px;min-width:110px;">
                <div style="color:rgba(255,255,255,0.55);font-size:0.65rem;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">Total</div>
                <div style="color:#fff;font-size:1.4rem;font-weight:800;line-height:1.2;"><?= $total ?></div>
            </div>
            <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:10px 18px;min-width:110px;">
                <div style="color:rgba(255,255,255,0.55);font-size:0.65rem;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">Projetos</div>
                <div style="color:#fff;font-size:1.4rem;font-weight:800;line-height:1.2;"><?= count($projetos) ?></div>
            </div>
            <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:10px 18px;min-width:110px;">
                <div style="color:rgba(255,255,255,0.55);font-size:0.65rem;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">Recentes</div>
                <div style="color:#fbbf24;font-size:1.4rem;font-weight:800;line-height:1.2;"><?= $recentes ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Botão emitir -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0">Certificados Emitidos</h5>
    <button class="btn btn-primary" onclick="abrirModalEmitir()">
        <i class="bi bi-plus-circle me-2"></i>Emitir Certificado
    </button>
</div>

<?php if (empty($certificados)): ?>
<div class="content-card text-center py-5">
    <div style="display:inline-flex;flex-direction:column;align-items:center;gap:14px;">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#1d4ed8,#60a5fa);display:flex;align-items:center;justify-content:center;opacity:0.3;">
            <i class="bi bi-award-fill" style="color:#fff;font-size:2.2rem;"></i>
        </div>
        <div>
            <div class="fw-bold text-muted mb-1">Nenhum certificado emitido ainda</div>
            <div class="text-muted" style="font-size:0.83rem;">Clique em "Emitir Certificado" para enviar o primeiro.</div>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Filtro -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0"
                       placeholder="Buscar por título, aluno ou projeto..." oninput="filtrarCerts()">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <select class="form-select" id="filtroProjeto" onchange="filtrarCerts()">
                <option value="">Todos os Projetos</option>
                <?php foreach ($projetos as $pj): ?>
                    <option value="<?= $pj['id_projeto'] ?>"><?= htmlspecialchars($pj['titulo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 text-muted small text-center" id="contadorCerts">
            <?= $total ?> resultado(s)
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="content-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaCerts">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ARQUIVO</th>
                    <th>TÍTULO</th>
                    <th>ALUNO</th>
                    <th>PROJETO</th>
                    <th>DATA</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificados as $cert):
                    $ext     = strtolower(pathinfo($cert['nome_arquivo'] ?? '', PATHINFO_EXTENSION));
                    $icon    = iconeArquivoProfCert($ext);
                    $dataFmt = !empty($cert['data_registro']) ? date('d/m/Y', strtotime($cert['data_registro'])) : '—';
                    $proxyUrl = 'pages-professor/servir-certificado.php?id=' . $cert['id_producao'];
                    $extsView = ['pdf','jpg','jpeg','png','gif','webp','svg'];
                    $podeVer  = in_array($ext, $extsView);
                    $buscaKey = strtolower(($cert['titulo'] ?? '') . ' ' . ($cert['nome_aluno'] ?? '') . ' ' . ($cert['projeto'] ?? ''));
                ?>
                <tr data-busca="<?= htmlspecialchars($buscaKey) ?>"
                    data-projeto="<?= $cert['id_projeto'] ?>">
                    <td>
                        <i class="bi <?= $icon ?> me-2 fs-5"></i>
                        <span class="fw-medium"><?= htmlspecialchars($cert['nome_arquivo'] ?? '') ?></span>
                    </td>
                    <td><?= htmlspecialchars($cert['titulo'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($cert['nome_aluno'] ?? '—') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($cert['projeto'] ?? '') ?></td>
                    <td class="text-muted small"><?= $dataFmt ?></td>
                    <td class="text-center">
                        <?php if ($podeVer): ?>
                        <button class="btn btn-sm btn-outline-primary me-1"
                                onclick="abrirVisualizador('<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>', '<?= htmlspecialchars($cert['nome_arquivo'] ?? '', ENT_QUOTES) ?>')"
                                title="Visualizar">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                           download="<?= htmlspecialchars($cert['nome_arquivo'] ?? 'certificado', ENT_QUOTES) ?>"
                           class="btn btn-sm btn-outline-secondary me-1" title="Baixar">
                            <i class="bi bi-download"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmarExcluir(<?= $cert['id_producao'] ?>, '<?= htmlspecialchars($cert['titulo'] ?? '', ENT_QUOTES) ?>')"
                                title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ===== MODAL EMITIR CERTIFICADO ===== -->
<div class="modal fade" id="modalEmitir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-award me-2 text-warning"></i>Emitir Certificado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form id="formEmitir" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Projeto <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectProjeto" name="id_projeto" required onchange="carregarAlunosDoProjeto()">
                            <option value="">— Selecione o projeto —</option>
                            <?php foreach ($projetos as $pj): ?>
                                <option value="<?= $pj['id_projeto'] ?>"><?= htmlspecialchars($pj['titulo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Aluno <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectAluno" name="id_aluno" required disabled>
                            <option value="">— Selecione o projeto primeiro —</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título do Certificado <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titulo" placeholder="Ex: Certificado de Participação" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Arquivo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="arquivo"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <div class="form-text">PDF, DOC, DOCX, JPG ou PNG · máx. 10 MB</div>
                    </div>

                    <div id="erroEmitir" class="alert alert-danger d-none py-2"></div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnEmitirSubmit">
                            <span id="spinEmitir" class="spinner-border spinner-border-sm me-2 d-none"></span>
                            <i class="bi bi-upload me-1"></i>Emitir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL EXCLUIR ===== -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4">
                <div style="width:56px;height:56px;border-radius:50%;background:#fff1f2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="bi bi-trash text-danger fs-4"></i>
                </div>
                <h6 class="fw-bold mb-1">Excluir certificado?</h6>
                <p class="text-muted small mb-3" id="txtNomeExcluir"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger btn-sm" id="btnConfirmarExcluir">
                        <span id="spinExcluir" class="spinner-border spinner-border-sm me-1 d-none"></span>
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL VISUALIZADOR ===== -->
<div class="modal fade" id="modalVisualizador" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="tituloVisualizador"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2" style="min-height:70vh;">
                <iframe id="iframeVisualizador" src="" style="width:100%;height:70vh;border:none;border-radius:8px;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // ── Filtro ──
    function filtrarCerts() {
        const busca   = (document.getElementById('filtroBusca')?.value || '').toLowerCase();
        const projeto = document.getElementById('filtroProjeto')?.value || '';
        const linhas  = document.querySelectorAll('#tabelaCerts tbody tr[data-busca]');
        let vis = 0;
        linhas.forEach(tr => {
            const okB = !busca   || tr.dataset.busca.includes(busca);
            const okP = !projeto || tr.dataset.projeto === projeto;
            tr.style.display = (okB && okP) ? '' : 'none';
            if (okB && okP) vis++;
        });
        const el = document.getElementById('contadorCerts');
        if (el) el.textContent = vis + ' resultado(s)';
    }
    window.filtrarCerts = filtrarCerts;

    // ── Modal emitir ──
    window.abrirModalEmitir = function() {
        document.getElementById('formEmitir').reset();
        document.getElementById('selectAluno').innerHTML = '<option value="">— Selecione o projeto primeiro —</option>';
        document.getElementById('selectAluno').disabled = true;
        document.getElementById('erroEmitir').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('modalEmitir')).show();
    };

    window.carregarAlunosDoProjeto = function() {
        const idProjeto = document.getElementById('selectProjeto').value;
        const sel = document.getElementById('selectAluno');
        sel.innerHTML = '<option value="">Carregando...</option>';
        sel.disabled = true;
        if (!idProjeto) {
            sel.innerHTML = '<option value="">— Selecione o projeto primeiro —</option>';
            return;
        }
        fetch('pages-professor/alunos-do-projeto.php?id_projeto=' + idProjeto, { cache: 'no-store' })
            .then(r => r.json())
            .then(lista => {
                if (!lista.length) {
                    sel.innerHTML = '<option value="">Nenhum aluno neste projeto</option>';
                } else {
                    sel.innerHTML = '<option value="">— Selecione o aluno —</option>' +
                        lista.map(a => `<option value="${a.id_usuario}" data-mat="${a.matricula}">${a.nome}</option>`).join('');
                    sel.disabled = false;
                }
            })
            .catch(() => { sel.innerHTML = '<option value="">Erro ao carregar</option>'; });
    };

    document.getElementById('formEmitir').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn  = document.getElementById('btnEmitirSubmit');
        const spin = document.getElementById('spinEmitir');
        const err  = document.getElementById('erroEmitir');
        btn.disabled = true; spin.classList.remove('d-none'); err.classList.add('d-none');

        fetch('controllers/controller-professor/upload-certificado.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(r => r.json())
        .then(res => {
            if (res.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalEmitir')).hide();
                location.reload();
            } else {
                err.textContent = res.mensagem || 'Erro ao emitir.';
                err.classList.remove('d-none');
            }
        })
        .catch(() => { err.textContent = 'Erro de conexão.'; err.classList.remove('d-none'); })
        .finally(() => { btn.disabled = false; spin.classList.add('d-none'); });
    });

    // ── Modal excluir ──
    let idParaExcluir = null;
    window.confirmarExcluir = function(id, titulo) {
        idParaExcluir = id;
        document.getElementById('txtNomeExcluir').textContent = titulo || 'este certificado';
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    };

    document.getElementById('btnConfirmarExcluir').addEventListener('click', function() {
        if (!idParaExcluir) return;
        const spin = document.getElementById('spinExcluir');
        this.disabled = true; spin.classList.remove('d-none');

        const fd = new FormData();
        fd.append('id_producao', idParaExcluir);
        fetch('controllers/controller-professor/excluir-certificado.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                bootstrap.Modal.getInstance(document.getElementById('modalExcluir')).hide();
                if (res.sucesso) location.reload();
                else alert(res.mensagem || 'Erro ao excluir.');
            })
            .catch(() => alert('Erro de conexão.'))
            .finally(() => { this.disabled = false; spin.classList.add('d-none'); });
    });

    // ── Visualizador ──
    window.abrirVisualizador = function(url, nome) {
        document.getElementById('tituloVisualizador').textContent = nome;
        document.getElementById('iframeVisualizador').src = url;
        new bootstrap.Modal(document.getElementById('modalVisualizador')).show();
    };
    document.getElementById('modalVisualizador').addEventListener('hidden.bs.modal', function() {
        document.getElementById('iframeVisualizador').src = '';
    });
})();
</script>
