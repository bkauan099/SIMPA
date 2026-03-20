<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Cronograma</h3>
        <p class="text-muted mb-0">Agenda de atividades, entregas e reuniões dos projetos</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-calendar-event"></i></div>
            <div><h4 class="mb-0 fw-bold" id="totalEventos">0</h4><small class="text-muted">Eventos este mês</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-alarm"></i></div>
            <div><h4 class="mb-0 fw-bold">4</h4><small class="text-muted">Prazos próximos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold">2</h4><small class="text-muted">Reuniões agendadas</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-square"></i></div>
            <div><h4 class="mb-0 fw-bold">7</h4><small class="text-muted">Entregas pendentes</small></div>
        </div>
    </div>
</div>

<div class="row g-3">

    <!-- CALENDÁRIO -->
    <div class="col-lg-5">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-sm btn-outline-secondary" id="btnPrev"><i class="bi bi-chevron-left"></i></button>
                <h6 class="fw-bold mb-0" id="calMesAno"></h6>
                <button class="btn btn-sm btn-outline-secondary" id="btnNext"><i class="bi bi-chevron-right"></i></button>
            </div>

            <!-- Dias da semana -->
            <div class="d-flex text-center mb-1">
                <?php foreach(['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $d): ?>
                <div style="width:14.28%;font-size:0.72rem;font-weight:600;color:#64748b;"><?= $d ?></div>
                <?php endforeach; ?>
            </div>

            <!-- Grid do calendário -->
            <div id="calGrid" class="d-flex flex-wrap"></div>

            <!-- Legenda -->
            <div class="d-flex flex-wrap gap-3 mt-3" style="font-size:0.75rem;color:#64748b;">
                <span><span class="dot-cal" style="background:#3b82f6;"></span> Entrega</span>
                <span><span class="dot-cal" style="background:#8b5cf6;"></span> Reunião</span>
                <span><span class="dot-cal" style="background:#10b981;"></span> Evento</span>
                <span><span class="dot-cal" style="background:#f59e0b;"></span> Prazo</span>
            </div>
        </div>
    </div>

    <!-- ATIVIDADES DO MÊS -->
    <div class="col-lg-7">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="tituloLista">Atividades do Mês</h5>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary filtro-tipo active" data-tipo="todos">Todos</button>
                    <button class="btn btn-sm btn-outline-primary filtro-tipo" data-tipo="Entrega">Entregas</button>
                    <button class="btn btn-sm btn-outline-secondary filtro-tipo" data-tipo="Reunião" style="border-color:#8b5cf6;color:#8b5cf6;">Reuniões</button>
                    <button class="btn btn-sm btn-outline-success filtro-tipo" data-tipo="Evento">Eventos</button>
                </div>
            </div>
            <div id="listaAtividades" style="max-height:420px;overflow-y:auto;"></div>
        </div>
    </div>
</div>

<style>
.dot-cal {
    display: inline-block;
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-right: 4px;
}
.cal-day {
    width: 14.28%;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    cursor: pointer;
    border-radius: 8px;
    position: relative;
    transition: background 0.15s;
    gap: 2px;
    padding: 2px;
}
.cal-day:hover { background: #f1f5f9; }
.cal-day.hoje { background: #0F2557; color: white; font-weight: 700; }
.cal-day.hoje:hover { background: #193A82; }
.cal-day.selecionado { background: #3b82f6; color: white; font-weight: 600; }
.cal-day.selecionado:hover { background: #2563eb; }
.cal-day.hoje.selecionado { background: #0F2557; outline: 3px solid #3b82f6; outline-offset: -2px; }
.cal-day.tem-evento .dots { display: flex; gap: 2px; }
.dots { display: flex; gap: 2px; min-height: 6px; }
.dot-ev {
    width: 5px; height: 5px;
    border-radius: 50%;
    flex-shrink: 0;
}
.atividade-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}
.atividade-item:last-child { border-bottom: none; }
.atividade-data {
    min-width: 42px;
    text-align: center;
    background: #f8fafc;
    border-radius: 8px;
    padding: 4px 6px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #0F2557;
    line-height: 1.3;
}
</style>

<script>
(function() {
    const eventos = [
        { dia: 5,  hora: '09:00', titulo: 'Reunião de Kickoff',         aluno: 'Todos',   projeto: 'SIMPA UEMA',   tipo: 'Reunião',  status: 'Pendente'     },
        { dia: 10, hora: '10:00', titulo: 'Entrega Relatório Parcial',  aluno: 'João',    projeto: 'SIMPA UEMA',   tipo: 'Entrega',  status: 'Pendente'     },
        { dia: 14, hora: '14:00', titulo: 'Apresentação de Resultados', aluno: 'Todos',   projeto: 'PROEXAE',      tipo: 'Evento',   status: 'Pendente'     },
        { dia: 18, hora: '10:00', titulo: 'Revisão do Artigo',          aluno: 'João',    projeto: 'SIMPA UEMA',   tipo: 'Entrega',  status: 'Pendente'     },
        { dia: 20, hora: '15:00', titulo: 'Prazo Bolsa CNPq',           aluno: 'Augusto', projeto: 'Inovação Tec.', tipo: 'Prazo',   status: 'Urgente'      },
        { dia: 22, hora: '14:00', titulo: 'Entrega Relatório Final',    aluno: 'Augusto', projeto: 'Inovação Tec.', tipo: 'Entrega', status: 'Em Andamento' },
        { dia: 24, hora: '09:00', titulo: 'Reunião de Orientação',      aluno: 'Todos',   projeto: 'Projeto Social', tipo: 'Reunião', status: 'Pendente'    },
        { dia: 25, hora: '10:00', titulo: 'Apresentação Final',         aluno: 'Todos',   projeto: 'PROEXAE',      tipo: 'Evento',   status: 'Pendente'     },
        { dia: 28, hora: '11:00', titulo: 'Submissão de Artigo',        aluno: 'Aian',    projeto: 'Projeto Social', tipo: 'Prazo',  status: 'Pendente'     },
        { dia: 30, hora: '15:00', titulo: 'Reunião de Acompanhamento',  aluno: 'Aian',    projeto: 'Inovação Tec.', tipo: 'Reunião', status: 'Pendente'    },
    ];

    const corTipo = {
        'Entrega': '#3b82f6',
        'Reunião': '#8b5cf6',
        'Evento':  '#10b981',
        'Prazo':   '#f59e0b',
    };
    const badgeTipo = {
        'Entrega': 'bg-primary',
        'Reunião': 'badge-reuniao',
        'Evento':  'bg-success',
        'Prazo':   'bg-warning text-dark',
    };
    const badgeStatus = {
        'Pendente':     'bg-warning text-dark',
        'Em Andamento': 'bg-info text-dark',
        'Concluído':    'bg-success',
        'Urgente':      'bg-danger',
    };

    // Estilo extra para badge reunião
    const style = document.createElement('style');
    style.textContent = '.badge-reuniao { background:#8b5cf6 !important; color:white; }';
    document.head.appendChild(style);

    const hoje = new Date();
    let ano = hoje.getFullYear();
    let mes = hoje.getMonth();

    const nomesMeses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

    let filtroAtivo = 'todos';

    function renderCalendario() {
        const grid = document.getElementById('calGrid');
        const titulo = document.getElementById('calMesAno');
        titulo.textContent = nomesMeses[mes] + ' ' + ano;

        const primeiroDia = new Date(ano, mes, 1).getDay();
        const diasNoMes   = new Date(ano, mes + 1, 0).getDate();
        const ehMesAtual  = (ano === hoje.getFullYear() && mes === hoje.getMonth());

        // Agrupar eventos por dia
        const porDia = {};
        eventos.forEach(e => { (porDia[e.dia] = porDia[e.dia] || []).push(e); });

        document.getElementById('totalEventos').textContent = eventos.length;

        grid.innerHTML = '';

        // Células vazias antes do dia 1
        for (let i = 0; i < primeiroDia; i++) {
            const cell = document.createElement('div');
            cell.className = 'cal-day';
            grid.appendChild(cell);
        }

        for (let d = 1; d <= diasNoMes; d++) {
            const cell = document.createElement('div');
            cell.className = 'cal-day' + (ehMesAtual && d === hoje.getDate() ? ' hoje' : '');

            const num = document.createElement('span');
            num.textContent = d;
            cell.appendChild(num);

            const dots = document.createElement('div');
            dots.className = 'dots';
            if (porDia[d]) {
                porDia[d].slice(0, 3).forEach(ev => {
                    const dot = document.createElement('span');
                    dot.className = 'dot-ev';
                    dot.style.background = corTipo[ev.tipo] || '#94a3b8';
                    dots.appendChild(dot);
                });
                cell.classList.add('tem-evento');
            }
            cell.appendChild(dots);

            cell.addEventListener('click', function() {
                // Remover seleção anterior
                document.querySelectorAll('.cal-day.selecionado').forEach(function(el) {
                    el.classList.remove('selecionado');
                });
                // Selecionar dia clicado
                this.classList.add('selecionado');
                renderLista(d);
            });

            grid.appendChild(cell);
        }
    }

    function renderLista(diaFiltro) {
        const lista = document.getElementById('listaAtividades');
        const titulo = document.getElementById('tituloLista');

        let itens = eventos;
        if (diaFiltro) {
            itens = itens.filter(e => e.dia === diaFiltro);
            titulo.textContent = 'Atividades — Dia ' + diaFiltro;
        } else {
            titulo.textContent = 'Atividades do Mês';
        }
        if (filtroAtivo !== 'todos') {
            itens = itens.filter(e => e.tipo === filtroAtivo);
        }

        if (itens.length === 0) {
            lista.innerHTML = '<p class="text-muted text-center py-4">Nenhuma atividade encontrada.</p>';
            return;
        }

        lista.innerHTML = itens.map(ev => `
            <div class="atividade-item">
                <div class="atividade-data">
                    <div style="font-size:1rem;">${String(ev.dia).padStart(2,'0')}</div>
                    <div style="font-weight:400;color:#64748b;">${nomesMeses[mes].slice(0,3)}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                        <span class="fw-medium" style="font-size:0.9rem;">${ev.titulo}</span>
                        <div class="d-flex gap-1">
                            <span class="badge ${badgeTipo[ev.tipo] || 'bg-secondary'}">${ev.tipo}</span>
                            <span class="badge ${badgeStatus[ev.status] || 'bg-secondary'}">${ev.status}</span>
                        </div>
                    </div>
                    <div class="text-muted mt-1" style="font-size:0.78rem;">
                        <i class="bi bi-clock me-1"></i>${ev.hora}
                        &nbsp;·&nbsp;<i class="bi bi-person me-1"></i>${ev.aluno}
                        &nbsp;·&nbsp;<i class="bi bi-folder me-1"></i>${ev.projeto}
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Navegação mês anterior/próximo
    document.getElementById('btnPrev').addEventListener('click', function() {
        mes--; if (mes < 0) { mes = 11; ano--; }
        renderCalendario();
        renderLista(null);
    });
    document.getElementById('btnNext').addEventListener('click', function() {
        mes++; if (mes > 11) { mes = 0; ano++; }
        renderCalendario();
        renderLista(null);
    });

    // Filtros de tipo
    document.querySelectorAll('.filtro-tipo').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filtro-tipo').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filtroAtivo = this.dataset.tipo;
            renderLista(null);
        });
    });

    renderCalendario();
    renderLista(null);
})();
</script>