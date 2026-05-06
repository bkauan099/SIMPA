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
    $mapa = [];
    $mesAtual = date('Y-m');
    foreach ($tarefas as $item) {
        if (str_starts_with($item['data'], $mesAtual)) {
            $dia = (int) date('j', strtotime($item['data']));
            $hora = $item['hora'] ? substr($item['hora'], 0, 5) : '';
            $icone = !empty($item['concluido']) ? '✅' : '📌';
            $mapa[$dia][] = $icone . ' ' . $item['titulo'] . ($hora ? ' — ' . $hora : '');
        }
    }
    foreach ($eventos as $item) {
        if (str_starts_with($item['data'], $mesAtual)) {
            $dia = (int) date('j', strtotime($item['data']));
            $hora = $item['hora'] ? substr($item['hora'], 0, 5) : '';
            $icone = !empty($item['concluido']) ? '✅' : '📌';
            $mapa[$dia][] = $icone . ' ' . $item['titulo'] . ($hora ? ' — ' . $hora : '');
        }
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
        const temCompromisso = window.compromissosPorDia[dia] !== undefined;
        const dot = temCompromisso ? '<span class="dot-compromisso"></span>' : '';
        calendar.innerHTML += `<div class="calendar-day${isHoje ? ' today' : ''}" onclick="showDay(${dia})"><span>${dia}</span>${dot}</div>`;
    }
};

window.showDay = function(dia) {
    const itens = window.compromissosPorDia[dia];
    const corpo = itens
        ? itens.map(i => `<div class="mb-1">${i}</div>`).join('')
        : 'Nenhuma atividade neste dia.';
    document.getElementById('modalContent').innerHTML = `<strong>Dia ${dia}</strong><br><br>${corpo}`;
    new bootstrap.Modal(document.getElementById('dayModal')).show();
};

setTimeout(window.gerarCalendario, 300);
</script>

<!-- MODAL -->
<div class="modal fade" id="dayModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atividades do Dia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent"></div>
        </div>
    </div>
</div>
