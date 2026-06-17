<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
require_once __DIR__ . '/../conexao/conexao.php';
if (!$id_usuario) { echo '<p class="text-danger p-4">Sessão expirada. Recarregue a página.</p>'; exit; }

// Matrícula do aluno — usada como filtro de ownership no caminho
$stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$stmtMat->execute([':id' => $id_usuario]);
$matricula = $stmtMat->fetchColumn();

$certificados = [];
if ($matricula) {
    try {
        $prefixo = 'uploads/certificados/aluno/' . $matricula . '/%';
        $stmt = $pdo->prepare("
            SELECT
                p.id_producao,
                p.titulo,
                p.tipo          AS nome_arquivo,
                p.caminho,
                p.data_registro,
                proj.titulo     AS projeto
            FROM producoes p
            JOIN projetos proj ON proj.id_projeto = p.id_projeto
            WHERE p.caminho LIKE :prefix
            ORDER BY p.data_registro DESC
        ");
        $stmt->execute([':prefix' => $prefixo]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $c) {
            if (!empty($c['caminho']) && file_exists(__DIR__ . '/../' . ltrim($c['caminho'], '/\\')))
                $certificados[] = $c;
        }
    } catch (Exception $e) { /* silencioso */ }
}

$total      = count($certificados);
$projetos   = $total ? count(array_unique(array_column($certificados, 'projeto'))) : 0;
$umMesAtras = new DateTime('-30 days');
$recentes   = count(array_filter($certificados, fn($c) =>
    !empty($c['data_registro']) && new DateTime($c['data_registro']) >= $umMesAtras));

$extsVisualizavel = ['pdf','jpg','jpeg','png','gif','webp','svg',
    'txt','py','php','js','ts','html','css','json','md','csv'];

function iconeArquivoCert(string $ext): string {
    return match(true) {
        $ext === 'pdf'                              => 'bi-file-earmark-pdf text-danger',
        in_array($ext, ['doc','docx'])              => 'bi-file-earmark-word text-primary',
        in_array($ext, ['xls','xlsx'])              => 'bi-file-earmark-excel text-success',
        in_array($ext, ['jpg','jpeg','png',
                         'gif','webp','svg'])       => 'bi-file-earmark-image text-info',
        default                                     => 'bi-file-earmark text-muted',
    };
}
?>

<!-- Banner principal -->
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
    <div style="position:absolute;top:-60%;left:-5%;width:35%;height:220%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.04),transparent);transform:rotate(-12deg);pointer-events:none;"></div>
    <div style="position:absolute;top:50%;right:48px;transform:translateY(-50%);width:190px;height:190px;border-radius:50%;border:1px solid rgba(255,255,255,0.07);pointer-events:none;"></div>
    <div style="position:absolute;top:50%;right:68px;transform:translateY(-50%);width:150px;height:150px;border-radius:50%;border:1px solid rgba(255,255,255,0.05);pointer-events:none;"></div>

    <div style="position:absolute;right:32px;top:50%;transform:translateY(-50%);width:86px;height:86px;border-radius:50%;background:rgba(251,191,36,0.12);border:2px solid rgba(251,191,36,0.3);display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px rgba(251,191,36,0.2);">
        <i class="bi bi-award-fill" style="font-size:2.6rem;color:#fbbf24;filter:drop-shadow(0 0 10px rgba(251,191,36,0.55));"></i>
    </div>

    <i class="bi bi-star-fill" style="position:absolute;top:18px;right:148px;font-size:0.45rem;color:rgba(251,191,36,0.65);"></i>
    <i class="bi bi-star-fill" style="position:absolute;top:36px;right:172px;font-size:0.3rem;color:rgba(251,191,36,0.45);"></i>
    <i class="bi bi-star-fill" style="position:absolute;bottom:22px;right:142px;font-size:0.38rem;color:rgba(251,191,36,0.55);"></i>
    <i class="bi bi-star-fill" style="position:absolute;bottom:38px;right:168px;font-size:0.28rem;color:rgba(251,191,36,0.35);"></i>

    <div style="position:relative;z-index:1;padding-right:130px;">
        <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(251,191,36,0.15);border:1px solid rgba(251,191,36,0.38);border-radius:20px;padding:4px 13px;font-size:0.67rem;font-weight:700;color:#fbbf24;letter-spacing:0.5px;margin-bottom:13px;">
            <i class="bi bi-patch-check-fill"></i> CONQUISTAS ACADÊMICAS
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
            <h2 style="color:#fff;font-weight:900;margin:0;font-size:1.75rem;letter-spacing:-0.3px;line-height:1.1;">
                Meus Certificados
            </h2>
            <span style="background:rgba(251,191,36,0.18);border:1px solid rgba(251,191,36,0.4);border-radius:20px;padding:3px 11px;font-size:0.78rem;font-weight:800;color:#fbbf24;">
                <?= $total ?> <?= $total === 1 ? 'certificado' : 'certificados' ?>
            </span>
        </div>
        <p style="color:rgba(255,255,255,0.58);margin:0 0 16px;font-size:0.82rem;">
            Documentos emitidos pelo seu professor após avaliação
        </p>
        <div style="width:44px;height:3px;border-radius:2px;background:linear-gradient(90deg,#fbbf24,rgba(251,191,36,0.15));"></div>
    </div>
</div>

<?php if (empty($certificados)): ?>
<!-- Estado vazio -->
<div class="content-card text-center py-5">
    <div style="display:inline-flex;flex-direction:column;align-items:center;gap:14px;">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#1d4ed8,#60a5fa);display:flex;align-items:center;justify-content:center;opacity:0.3;">
            <i class="bi bi-award-fill" style="color:#fff;font-size:2.2rem;"></i>
        </div>
        <div>
            <div class="fw-bold text-muted mb-1">Nenhum certificado disponível ainda</div>
            <div class="text-muted" style="font-size:0.83rem;">Conclua suas tarefas e aguarde a avaliação do professor.</div>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Filtro -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroCertBusca" class="form-control border-start-0"
                       placeholder="Buscar por título ou projeto..." oninput="filtrarCertificados()">
            </div>
        </div>
        <div class="col-12 col-md-3 text-muted small text-center" id="contadorCerts">
            <?= $total ?> resultado(s)
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="content-card">
    <h5 class="fw-bold mb-3">Meus Certificados</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaCertificados">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ARQUIVO</th>
                    <th>TÍTULO</th>
                    <th>PROJETO</th>
                    <th>DATA</th>
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificados as $cert):
                    $nome     = $cert['nome_arquivo'];
                    $ext      = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
                    $podeVer  = in_array($ext, $extsVisualizavel);
                    $icon     = iconeArquivoCert($ext);
                    $proxyUrl = 'pages-aluno/servir-certificado.php?id=' . $cert['id_producao'];
                    $buscaKey = strtolower($cert['titulo'] . ' ' . $cert['projeto']);
                    $dataFmt  = !empty($cert['data_registro']) ? date('d/m/Y', strtotime($cert['data_registro'])) : '—';
                ?>
                <tr data-busca="<?= htmlspecialchars($buscaKey) ?>">
                    <td>
                        <i class="bi <?= $icon ?> me-2 fs-5"></i>
                        <span class="fw-medium"><?= htmlspecialchars($nome) ?></span>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($cert['titulo']) ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($cert['projeto']) ?></td>
                    <td class="text-muted small"><?= $dataFmt ?></td>
                    <td class="text-center">
                        <?php if ($podeVer): ?>
                        <button class="btn btn-sm btn-outline-primary me-1"
                                data-caminho="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                                data-nome="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                                onclick="abrirModalVisualizar(this.dataset.caminho, this.dataset.nome)"
                                title="Visualizar">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                           download="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                           class="btn btn-sm btn-outline-secondary" title="Baixar">
                            <i class="bi bi-download"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
function filtrarCertificados() {
    const busca  = (document.getElementById('filtroCertBusca')?.value || '').toLowerCase();
    const linhas = document.querySelectorAll('#tabelaCertificados tbody tr[data-busca]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const ok = !busca || tr.dataset.busca.includes(busca);
        tr.style.display = ok ? '' : 'none';
        if (ok) visiveis++;
    });
    const cnt = document.getElementById('contadorCerts');
    if (cnt) cnt.textContent = visiveis + ' resultado(s)';
}
</script>
