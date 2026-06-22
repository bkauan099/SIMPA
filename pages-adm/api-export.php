<?php
ob_start();
require_once '../lib/Guard.php';
Guard::apenasAdmin();
if (empty($_SESSION['id_usuario'])) { http_response_code(403); exit('Acesso negado.'); }

require_once '../conexao/conexao.php';
require_once '../model/RelatorioExportModel.php';

$formato    = $_GET['formato']    ?? 'csv';   // csv | html | txt
$tipo       = $_GET['tipo']       ?? 'geral'; // geral | projeto
$id_projeto = (int)($_GET['id_projeto'] ?? 0);
$filtroStatus = $_GET['status']   ?? '';

$model = new RelatorioExportModel($pdo);

// ── CSV genérico ─────────────────────────────────────────────────────────────
function csvLinha(array $cols): string {
    return implode(';', array_map(function ($c) {
        $c = (string) ($c ?? '');
        // Neutraliza CSV/Formula Injection: Excel/LibreOffice interpretam células
        // que começam com =, +, -, @ como fórmula ao abrir o arquivo
        if (preg_match('/^[=+\-@\t\r]/', $c)) {
            $c = "'" . $c;
        }
        return '"' . str_replace('"', '""', $c) . '"';
    }, $cols)) . "\n";
}

// ── Relatório Geral (todos os projetos) ──────────────────────────────────────
if ($tipo === 'geral') {
    $projetos = $model->listarProjetosParaExport($filtroStatus ?: null);
    $agora    = date('d/m/Y H:i');
    $nomeArq  = 'SIMPA_Relatorio_Geral_' . date('Ymd_His');

    if ($formato === 'csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"{$nomeArq}.csv\"");
        echo "\xEF\xBB\xBF"; // BOM UTF-8
        echo csvLinha(['ID','Título','Tipo','Área','Status','Data Início','Data Fim','Membros','Produções']);
        foreach ($projetos as $p) {
            echo csvLinha([
                $p['id_projeto'], $p['titulo'], $p['tipo'] ?? '—', $p['area'] ?? '—',
                ucfirst($p['status']),
                $p['data_inicio'] ? date('d/m/Y', strtotime($p['data_inicio'])) : '—',
                $p['data_fim']    ? date('d/m/Y', strtotime($p['data_fim']))    : '—',
                $p['total_membros'], $p['total_producoes'],
            ]);
        }
        exit;
    }

    // HTML para impressão
    header('Content-Type: text/html; charset=UTF-8');
    echo gerarHtmlGeral($projetos, $agora, $filtroStatus);
    exit;
}

// ── Relatório de Projeto Específico ──────────────────────────────────────────
if ($tipo === 'projeto' && $id_projeto) {
    $proj     = $model->dadosProjeto($id_projeto);
    $membros  = $model->membrosProjeto($id_projeto);
    $prods    = $model->producoesProjeto($id_projeto);
    $agora    = date('d/m/Y H:i');
    $nomeArq  = 'SIMPA_Projeto_' . $id_projeto . '_' . date('Ymd_His');

    if ($formato === 'csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"{$nomeArq}.csv\"");
        echo "\xEF\xBB\xBF";

        // Seção: Projeto
        echo csvLinha(['=== DADOS DO PROJETO ===']);
        echo csvLinha(['ID', $proj['id_projeto']]);
        echo csvLinha(['Título', $proj['titulo']]);
        echo csvLinha(['Tipo', $proj['tipo_nome'] ?? '—']);
        echo csvLinha(['Área', $proj['area'] ?? '—']);
        echo csvLinha(['Status', ucfirst($proj['status'])]);
        echo csvLinha(['Início', $proj['data_inicio'] ? date('d/m/Y', strtotime($proj['data_inicio'])) : '—']);
        echo csvLinha(['Fim', $proj['data_fim'] ? date('d/m/Y', strtotime($proj['data_fim'])) : '—']);
        echo csvLinha(['Descrição', $proj['descricao'] ?? '—']);
        echo "\n";

        // Seção: Membros
        echo csvLinha(['=== EQUIPE / MEMBROS ===']);
        echo csvLinha(['Nome','E-mail','Matrícula','Perfil','Função','C.H. (h/sem)','Entrada','Saída','Status']);
        foreach ($membros as $m) {
            echo csvLinha([
                $m['nome'], $m['email'], $m['matricula'] ?? '—',
                $m['perfil'], $m['funcao'],
                $m['carga_horaria'] ?? '—',
                $m['data_entrada'] ? date('d/m/Y', strtotime($m['data_entrada'])) : '—',
                $m['data_saida']   ? date('d/m/Y', strtotime($m['data_saida']))   : '—',
                ucfirst($m['status']),
            ]);
        }
        echo "\n";

        // Seção: Produções
        echo csvLinha(['=== ATIVIDADES E PRODUÇÕES ===']);
        echo csvLinha(['Título','Tipo','Status','Data Registro','Arquivo/URL']);
        foreach ($prods as $pd) {
            $tipoLabel = match(strtolower($pd['tipo'] ?? '')) {
                'relatorio'    => 'Relatório',
                'artigo'       => 'Publicação / Artigo',
                'apresentacao' => 'Evento / Apresentação',
                'certificado'  => 'Certificado',
                default        => ucfirst($pd['tipo'] ?? 'Outro'),
            };
            echo csvLinha([
                $pd['titulo'], $tipoLabel, ucfirst($pd['status']),
                $pd['data_registro'] ? date('d/m/Y', strtotime($pd['data_registro'])) : '—',
                $pd['caminho'] ?? '—',
            ]);
        }
        exit;
    }

    // HTML para impressão/PDF
    header('Content-Type: text/html; charset=UTF-8');
    echo gerarHtmlProjeto($proj, $membros, $prods, $agora);
    exit;
}

// ── Funções de geração de HTML ───────────────────────────────────────────────
function gerarHtmlGeral(array $projetos, string $agora, string $filtro): string {
    $total   = count($projetos);
    $filtroTx = $filtro ? " — Status: " . htmlspecialchars(ucfirst($filtro)) : '';
    $linhas  = '';
    foreach ($projetos as $i => $p) {
        $st = ucfirst($p['status']);
        $cor = match($p['status']) { 'ativo' => '#22c55e', 'concluido' => '#3b82f6', 'pendente' => '#f59e0b', default => '#94a3b8' };
        $linhas .= "<tr>
            <td>" . ($i+1) . "</td>
            <td><strong>" . htmlspecialchars($p['titulo']) . "</strong></td>
            <td>" . htmlspecialchars($p['tipo'] ?? '') . "</td>
            <td>" . htmlspecialchars($p['area'] ?? '') . "</td>
            <td><span style='background:{$cor};color:#fff;padding:2px 8px;border-radius:4px;font-size:.75rem'>{$st}</span></td>
            <td>" . ($p['data_inicio'] ? date('d/m/Y', strtotime($p['data_inicio'])) : '—') . "</td>
            <td>" . ($p['data_fim']    ? date('d/m/Y', strtotime($p['data_fim']))    : '—') . "</td>
            <td style='text-align:center'>{$p['total_membros']}</td>
            <td style='text-align:center'>{$p['total_producoes']}</td>
        </tr>";
    }
    return gerarLayoutHtml("Relatório Geral de Projetos{$filtroTx}", $agora, "
        <p>Total de projetos: <strong>{$total}</strong></p>
        <table><thead><tr>
            <th>#</th><th>Título</th><th>Tipo</th><th>Área</th><th>Status</th>
            <th>Início</th><th>Fim</th><th>Membros</th><th>Produções</th>
        </tr></thead><tbody>{$linhas}</tbody></table>
    ");
}

function gerarHtmlProjeto(array $proj, array $membros, array $prods, string $agora): string {
    $statusCor = match($proj['status']) { 'ativo' => '#22c55e', 'concluido' => '#3b82f6', 'pendente' => '#f59e0b', default => '#94a3b8' };

    // Equipe
    $linhasMem = '';
    foreach ($membros as $m) {
        $perf = match(strtolower($m['perfil'])) { 'professor_orientador' => 'Professor', 'admin' => 'Admin', default => 'Aluno' };
        $st   = $m['status'] === 'ativo' ? '<span style="color:#22c55e">●</span> Ativo' : '<span style="color:#ef4444">●</span> Inativo';
        $linhasMem .= "<tr>
            <td>" . htmlspecialchars($m['nome']) . "</td><td>" . htmlspecialchars($m['email']) . "</td>
            <td>" . htmlspecialchars($m['matricula'] ?? '—') . "</td><td>{$perf}</td>
            <td><strong>" . htmlspecialchars($m['funcao']) . "</strong></td>
            <td>{$m['carga_horaria']}h</td>
            <td>" . ($m['data_entrada'] ? date('d/m/Y', strtotime($m['data_entrada'])) : '—') . "</td>
            <td>" . ($m['data_saida']   ? date('d/m/Y', strtotime($m['data_saida']))   : '—') . "</td>
            <td>{$st}</td>
        </tr>";
    }

    // Produções agrupadas por tipo
    $grupos = ['relatorio' => 'Relatórios', 'artigo' => 'Publicações / Artigos', 'apresentacao' => 'Eventos / Apresentações', 'certificado' => 'Certificados'];
    $secProd = '';
    foreach ($grupos as $tipoKey => $tipoLabel) {
        $filtrados = array_filter($prods, fn($p) => strtolower($p['tipo'] ?? '') === $tipoKey);
        if (!$filtrados) continue;
        $rows = '';
        foreach ($filtrados as $pd) {
            $stCorPd = match($pd['status']) { 'ativo' => '#22c55e', 'pendente' => '#f59e0b', default => '#ef4444' };
            $stLabelPd = match($pd['status']) { 'ativo' => 'Aprovado', 'pendente' => 'Pendente', default => 'Rejeitado' };
            $caminhoEsc = htmlspecialchars($pd['caminho'] ?? '');
            $link = $pd['caminho'] ? "<a href='{$caminhoEsc}' style='color:#3b82f6'>{$caminhoEsc}</a>" : '—';
            $rows .= "<tr>
                <td><strong>" . htmlspecialchars($pd['titulo']) . "</strong></td>
                <td><span style='background:{$stCorPd};color:#fff;padding:1px 6px;border-radius:3px;font-size:.72rem'>{$stLabelPd}</span></td>
                <td>" . ($pd['data_registro'] ? date('d/m/Y', strtotime($pd['data_registro'])) : '—') . "</td>
                <td>{$link}</td>
            </tr>";
        }
        $secProd .= "<h3 style='margin:28px 0 8px;color:#2B3C50;font-size:1rem'>{$tipoLabel} (" . count($filtrados) . ")</h3>
            <table><thead><tr><th>Título</th><th>Status</th><th>Data</th><th>Arquivo / URL</th></tr></thead>
            <tbody>{$rows}</tbody></table>";
    }

    // Outros não categorizados
    $outros = array_filter($prods, fn($p) => !in_array(strtolower($p['tipo'] ?? ''), array_keys($grupos)));
    if ($outros) {
        $rows = '';
        foreach ($outros as $pd) {
            $rows .= "<tr><td>" . htmlspecialchars($pd['titulo']) . "</td><td>" . htmlspecialchars(ucfirst($pd['tipo'] ?? '')) . "</td><td>" . htmlspecialchars($pd['status']) . "</td>
                <td>" . ($pd['data_registro'] ? date('d/m/Y', strtotime($pd['data_registro'])) : '—') . "</td></tr>";
        }
        $secProd .= "<h3 style='margin:28px 0 8px;color:#2B3C50;font-size:1rem'>Outros (" . count($outros) . ")</h3>
            <table><thead><tr><th>Título</th><th>Tipo</th><th>Status</th><th>Data</th></tr></thead>
            <tbody>{$rows}</tbody></table>";
    }

    $dataInicio = $proj['data_inicio'] ? date('d/m/Y', strtotime($proj['data_inicio'])) : '—';
    $dataFim    = $proj['data_fim']    ? date('d/m/Y', strtotime($proj['data_fim']))    : 'Em andamento';

    $conteudo = "
        <div style='background:#f8fafc;border-radius:8px;padding:16px 20px;margin-bottom:24px;border-left:4px solid {$statusCor}'>
            <table style='width:100%;border:none'>
                <tr>
                    <td><strong>Tipo:</strong> " . htmlspecialchars($proj['tipo_nome'] ?? '—') . "</td>
                    <td><strong>Área:</strong> " . htmlspecialchars($proj['area'] ?? '—') . "</td>
                    <td><strong>Status:</strong> <span style='color:{$statusCor};font-weight:700'>" . ucfirst($proj['status']) . "</span></td>
                </tr>
                <tr>
                    <td><strong>Início:</strong> {$dataInicio}</td>
                    <td><strong>Fim:</strong> {$dataFim}</td>
                    <td><strong>Membros:</strong> " . count($membros) . " &nbsp; <strong>Produções:</strong> " . count($prods) . "</td>
                </tr>
            </table>
            " . ($proj['descricao'] ? "<p style='margin:10px 0 0;color:#64748b;font-size:.85rem'>" . htmlspecialchars($proj['descricao']) . "</p>" : '') . "
        </div>

        <h2 style='color:#2B3C50;font-size:1.05rem;margin:24px 0 8px;border-bottom:1px solid #e2e8f0;padding-bottom:6px'>
            👥 Equipe do Projeto (" . count($membros) . " membros)
        </h2>
        <table>
            <thead><tr><th>Nome</th><th>E-mail</th><th>Matrícula</th><th>Perfil</th><th>Função</th><th>C.H.</th><th>Entrada</th><th>Saída</th><th>Status</th></tr></thead>
            <tbody>{$linhasMem}</tbody>
        </table>

        <h2 style='color:#2B3C50;font-size:1.05rem;margin:32px 0 8px;border-bottom:1px solid #e2e8f0;padding-bottom:6px'>
            📁 Atividades e Produções (" . count($prods) . " registros)
        </h2>
        " . ($secProd ?: "<p style='color:#94a3b8'>Nenhuma produção cadastrada para este projeto.</p>");

    return gerarLayoutHtml("Relatório do Projeto: " . htmlspecialchars($proj['titulo']), $agora, $conteudo);
}

function gerarLayoutHtml(string $titulo, string $agora, string $conteudo): string {
    return "<!DOCTYPE html><html lang='pt-BR'><head>
        <meta charset='UTF-8'>
        <title>$titulo</title>
        <style>
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family: Arial, sans-serif; font-size: .85rem; color: #1e293b; background:#fff; padding: 32px; }
            .cabecalho { display:flex; align-items:center; justify-content:space-between; border-bottom:3px solid #2B3C50; padding-bottom:16px; margin-bottom:24px; }
            .cabecalho h1 { font-size:1.15rem; color:#2B3C50; }
            .cabecalho small { color:#64748b; font-size:.75rem; }
            table { width:100%; border-collapse:collapse; margin-bottom:16px; font-size:.78rem; }
            th { background:#2B3C50; color:#fff; padding:7px 10px; text-align:left; font-weight:600; }
            td { padding:6px 10px; border-bottom:1px solid #f1f5f9; }
            tr:nth-child(even) td { background:#f8fafc; }
            h3 { font-size:.9rem; color:#2B3C50; }
            @media print {
                body { padding: 16px; }
                .no-print { display:none; }
            }
        </style>
    </head><body>
        <div class='cabecalho'>
            <div>
                <h1>$titulo</h1>
                <small>SIMPA — Sistema Integrado de Monitoramento de Projetos Acadêmicos — UEMA ProExae</small>
            </div>
            <div style='text-align:right'>
                <small>Gerado em: $agora</small><br>
                <button class='no-print' onclick='window.print()' style='margin-top:6px;padding:6px 14px;background:#2B3C50;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:.78rem'>🖨 Imprimir</button>
            </div>
        </div>
        $conteudo
    </body></html>";
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Parâmetros inválidos.']);
?>
