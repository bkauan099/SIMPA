<div class="container-fluid mt-2">
    <div class="row g-3" style="min-height:0;">

        <!-- ESQUERDA: Tarefas e Eventos -->
        <div class="col-lg-8 d-flex flex-column">
            <div class="card card-custom p-4 flex-fill">
                <h4 class="mb-3">Tarefas e Eventos</h4>

                <!-- Mostra o projeto ativo do aluno, ou uma mensagem padrão -->
                <div class="d-flex align-items-center gap-2 mb-3 p-3 rounded flex-wrap"
                     style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <i class="bi bi-person-badge text-primary"></i>
                    <span class="fw-medium" style="font-size:0.9rem;">
                        <?= $projetoAtivo ? htmlspecialchars($projetoAtivo) : 'Nenhum projeto ativo' ?>
                    </span>
                </div>

                <!-- TABELA DE TAREFAS -->
                <h6><i class="bi bi-list-check"></i> Tarefas</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:50%">Título</th>
                                <th style="width:30%">Data</th>
                                <th style="width:20%">Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tarefas)): ?>
                                <tr><td colspan="3" class="text-muted text-center">Nenhuma tarefa cadastrada.</td></tr>
                            <?php else: ?>
                                <?php foreach ($tarefas as $tarefa): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($tarefa['titulo']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($tarefa['data'])) ?></td>
                                        <td><?= $tarefa['hora'] ? substr($tarefa['hora'], 0, 5) : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABELA DE EVENTOS -->
                <h6 class="mt-4"><i class="bi bi-calendar-event"></i> Eventos</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-success">
                            <tr>
                                <th style="width:50%">Título</th>
                                <th style="width:30%">Data</th>
                                <th style="width:20%">Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($eventos)): ?>
                                <tr><td colspan="3" class="text-muted text-center">Nenhum evento cadastrado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($eventos as $evento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($evento['titulo']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($evento['data'])) ?></td>
                                        <td><?= $evento['hora'] ? substr($evento['hora'], 0, 5) : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- DIREITA: Calendário + Carga Horária -->
        <div class="col-lg-4 d-flex flex-column gap-3">

            <!-- CALENDÁRIO (gerado pelo JavaScript) -->
            <div class="card card-custom p-3">
                <h6 class="text-center mb-2" id="mesAtual"></h6>
                <div class="d-flex text-center fw-bold mb-1" style="font-size:0.75rem;">
                    <div class="w-100">Dom</div><div class="w-100">Seg</div><div class="w-100">Ter</div>
                    <div class="w-100">Qua</div><div class="w-100">Qui</div><div class="w-100">Sex</div>
                    <div class="w-100">Sáb</div>
                </div>
                <div id="calendar" class="d-flex flex-wrap"></div>
                <div class="cal-legenda">
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#22c55e"></span>Concluída</div>
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#ef4444"></span>Não concluída</div>
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#eab308"></span>Próxima (≤7d)</div>
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#3b82f6"></span>Baixa prioridade</div>
                </div>
            </div>

            <!-- PAINEL DO DIA SELECIONADO -->
            <div class="card card-custom p-3 d-none" id="painelDia">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="font-size:0.9rem;" id="painelDiaTitulo"></span>
                    <button class="btn btn-sm btn-link text-muted p-0 lh-1" onclick="fecharPainelDia()" title="Fechar">
                        <i class="bi bi-x-lg" style="font-size:0.85rem;"></i>
                    </button>
                </div>
                <div id="painelDiaLista"></div>
            </div>

            <!-- CARGA HORÁRIA — dado real do banco -->
            <div class="card card-custom p-3 text-center flex-fill d-flex flex-column justify-content-center">
                <h6><i class="bi bi-clock"></i> Carga Horária</h6>
                <h2 class="mb-0"><?= $cargaHoraria ?>h</h2>
                <small class="text-muted">Acumuladas</small>
            </div>

        </div>
    </div>
</div>

<script>
window.compromissosPorDia = <?php
    $mapa    = [];
    $mesAtual = date('Y-m');
    $hoje     = date('Y-m-d');
    $sete     = date('Y-m-d', strtotime('+7 days'));

    foreach (array_merge($tarefas, $eventos) as $item) {
        if (!str_starts_with($item['data'], $mesAtual)) continue;

        $dia       = (int) date('j', strtotime($item['data']));
        $hora      = $item['hora'] ? substr($item['hora'], 0, 5) : '';
        $concluido = !empty($item['concluido']);
        $data      = $item['data'];

        if (!isset($mapa[$dia])) {
            $mapa[$dia] = ['verde'=>false,'vermelho'=>false,'amarelo'=>false,'azul'=>false,'itens'=>[]];
        }

        if ($concluido) {
            $mapa[$dia]['verde']   = true;
            $icone = '✅';
        } elseif ($data < $hoje) {
            $mapa[$dia]['vermelho'] = true;
            $icone = '❌';
        } elseif ($data <= $sete) {
            $mapa[$dia]['amarelo'] = true;
            $icone = '⚠️';
        } else {
            $mapa[$dia]['azul']    = true;
            $icone = '📌';
        }

        $mapa[$dia]['itens'][] = $icone . ' ' . $item['titulo'] . ($hora ? ' — ' . $hora : '');
    }
    echo json_encode($mapa);
?>;

window.gerarCalendario = function() {
    const calendar = document.getElementById('calendar');
    const mesAtualTexto = document.getElementById('mesAtual');
    if (!calendar || !mesAtualTexto) return;
    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = hoje.getMonth();
    const primeiroDia = new Date(ano, mes, 1).getDay();
    const diasNoMes  = new Date(ano, mes + 1, 0).getDate();
    const nomesMeses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    mesAtualTexto.innerText = nomesMeses[mes] + ' ' + ano;
    calendar.innerHTML = '';
    for (let i = 0; i < primeiroDia; i++) {
        calendar.innerHTML += '<div class="calendar-day"></div>';
    }
    for (let dia = 1; dia <= diasNoMes; dia++) {
        const isHoje = dia === hoje.getDate();
        const cats = window.compromissosPorDia[dia] || null;
        let dotsHtml = '';
        if (cats) {
            if (cats.verde)    dotsHtml += '<span class="dot dot-verde"></span>';
            if (cats.vermelho) dotsHtml += '<span class="dot dot-vermelho"></span>';
            if (cats.amarelo)  dotsHtml += '<span class="dot dot-amarelo"></span>';
            if (cats.azul)     dotsHtml += '<span class="dot dot-azul"></span>';
        }
        const dots = dotsHtml ? `<div class="dots-row">${dotsHtml}</div>` : '';
        calendar.innerHTML += `<div class="calendar-day${isHoje ? ' today' : ''}" onclick="showDay(${dia}, this)"><span>${dia}</span>${dots}</div>`;
    }
};

const _nomesMeses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                     'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

window.showDay = function(dia, el) {
    const cats   = window.compromissosPorDia[dia];
    const painel = document.getElementById('painelDia');
    const titulo = document.getElementById('painelDiaTitulo');
    const lista  = document.getElementById('painelDiaLista');
    const hoje   = new Date();

    titulo.textContent = `${dia} de ${_nomesMeses[hoje.getMonth()]}`;

    document.querySelectorAll('.calendar-day.selected').forEach(d => d.classList.remove('selected'));
    if (el && !el.classList.contains('today')) el.classList.add('selected');

    if (!cats || !cats.itens.length) {
        lista.innerHTML = '<p class="text-muted small mb-0 text-center py-2">Nenhuma atividade neste dia.</p>';
    } else {
        lista.innerHTML = cats.itens.map(item => `
            <div class="d-flex align-items-start gap-2 py-2" style="border-bottom:1px solid #f1f5f9;font-size:0.82rem;">
                <span class="flex-grow-1">${item}</span>
            </div>`).join('').replace(/<div[^>]*style="[^"]*border-bottom[^"]*"(?=[^<]*<\/div>\s*$)/, s => s.replace('border-bottom:1px solid #f1f5f9;', ''));
    }

    painel.classList.remove('d-none');
};

window.fecharPainelDia = function() {
    document.getElementById('painelDia').classList.add('d-none');
    document.querySelectorAll('.calendar-day.selected').forEach(d => d.classList.remove('selected'));
};

window.gerarCalendario();
</script>
