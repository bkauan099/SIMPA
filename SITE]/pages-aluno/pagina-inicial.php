<div class="container-fluid mt-2">
    <div class="row g-3" style="min-height:0;">

        <!-- ESQUERDA: Tarefas e Eventos -->
        <div class="col-lg-8" style="display:flex;flex-direction:column;">
            <div class="card card-custom p-4" style="flex:1;">
                <h4 class="mb-3">Tarefas e Eventos</h4>

                <!-- Vínculo -->
                <div class="d-flex align-items-center gap-2 mb-3 p-3 rounded flex-wrap"
                     style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <i class="bi bi-person-badge text-primary"></i>
                    <span class="fw-medium" style="font-size:0.9rem;">Projeto Social Comunitário</span>
                    <span class="badge bg-light text-dark border ms-1">Projeto Especial</span>
                    <span class="text-muted" style="font-size:0.8rem;">· Prof. João Varela</span>
                </div>

                <h6><i class="bi bi-list-check"></i> Tarefas</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr><th>Título</th><th>Data</th><th>Hora</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Revisão do Artigo</td><td>20/11/2023</td><td>10:00</td></tr>
                            <tr><td>Entrega Relatório</td><td>22/11/2023</td><td>14:00</td></tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4"><i class="bi bi-calendar-event"></i> Eventos</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-success">
                            <tr><th>Título</th><th>Data</th><th>Hora</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Apresentação Final</td><td>25/11/2023</td><td>10:00</td></tr>
                            <tr><td>Workshop Engenharia</td><td>28/11/2023</td><td>19:00</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- DIREITA: Calendário + Certificados + Carga Horária -->
        <div class="col-lg-4" style="display:flex;flex-direction:column;gap:16px;">

            <!-- CALENDÁRIO -->
            <div class="card card-custom p-3">
                <h6 class="text-center mb-2" id="mesAtual"></h6>
                <div class="d-flex text-center fw-bold mb-1" style="font-size:0.75rem;">
                    <div class="w-100">Dom</div><div class="w-100">Seg</div><div class="w-100">Ter</div>
                    <div class="w-100">Qua</div><div class="w-100">Qui</div><div class="w-100">Sex</div>
                    <div class="w-100">Sáb</div>
                </div>
                <div id="calendar" class="d-flex flex-wrap"></div>
                <div class="d-flex align-items-center gap-1 mt-2" style="font-size:0.72rem;color:#64748b;">
                    <span class="dot-compromisso" style="display:inline-block;"></span> Compromisso
                </div>
            </div>

            <!-- CERTIFICADOS -->
            <div class="card card-custom p-3 text-center">
                <h6><i class="bi bi-award"></i> Certificados</h6>
                <h2 class="mb-0">32</h2>
                <small class="text-muted">Conquistados</small>
            </div>

            <!-- CARGA HORÁRIA — cresce para preencher o espaço restante -->
            <div class="card card-custom p-3 text-center" style="flex:1;display:flex;flex-direction:column;justify-content:center;">
                <h6><i class="bi bi-clock"></i> Carga Horária</h6>
                <h2 class="mb-0">285h</h2>
                <small class="text-muted">Acumuladas</small>
            </div>

        </div>
    </div>
</div>

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

<script>
const compromissos = {
    20: ['📌 Revisão do Artigo — 10:00'],
    22: ['📌 Entrega Relatório — 14:00'],
    25: ['🎤 Apresentação Final — 10:00'],
    28: ['🎤 Workshop Engenharia — 19:00']
};

function gerarCalendario() {
    const calendar = document.getElementById('calendar');
    const mesAtualTexto = document.getElementById('mesAtual');
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
        const temComp = compromissos[dia] !== undefined;
        const dot = temComp
            ? '<span class="dot-compromisso"></span>'
            : '<span style="width:5px;height:5px;display:block;"></span>';
        calendar.innerHTML +=
            `<div class="calendar-day${isHoje ? ' today' : ''}" onclick="showDay(${dia})">
                <span>${dia}</span>${dot}
            </div>`;
    }
}

function showDay(dia) {
    const itens = compromissos[dia];
    const corpo = itens
        ? itens.map(i => `<div class="mb-1">${i}</div>`).join('')
        : 'Nenhuma atividade neste dia.';
    document.getElementById('modalContent').innerHTML =
        `<strong>Dia ${dia}</strong><br><br>${corpo}`;
    new bootstrap.Modal(document.getElementById('dayModal')).show();
}

setTimeout(gerarCalendario, 100);
</script>
