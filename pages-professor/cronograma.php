<?php
$id_professor = $_SESSION['id_usuario'] ?? 5;

// Garante colunas necessárias
try {
    $pdo->exec("ALTER TABLE agenda_items ADD COLUMN IF NOT EXISTS id_projeto INTEGER");
} catch (PDOException $e) {}

// Stats para os cards
$stats_cron = ['total_mes' => 0, 'prazos' => 0, 'reunioes' => 0, 'entregas' => 0];
try {
    $stmt = $pdo->prepare("
        SELECT
            COUNT(DISTINCT CASE
                WHEN EXTRACT(MONTH FROM a.data) = EXTRACT(MONTH FROM CURRENT_DATE)
                 AND EXTRACT(YEAR  FROM a.data) = EXTRACT(YEAR  FROM CURRENT_DATE)
                THEN a.id END) AS total_mes,
            COUNT(DISTINCT CASE
                WHEN a.tipo = 'Prazo'
                 AND a.data >= CURRENT_DATE
                 AND a.data <= CURRENT_DATE + INTERVAL '7 days'
                THEN a.id END) AS prazos,
            COUNT(DISTINCT CASE
                WHEN a.tipo = 'Reunião'
                 AND (a.concluido IS NULL OR a.concluido = false)
                THEN a.id END) AS reunioes,
            COUNT(DISTINCT CASE
                WHEN a.tipo = 'Entrega'
                 AND (a.concluido IS NULL OR a.concluido = false)
                THEN a.id END) AS entregas
        FROM agenda_items a
        JOIN participacao par ON a.id_projeto = par.id_projeto
        WHERE par.id_usuario = :id
          AND a.id_projeto IS NOT NULL
          AND a.data IS NOT NULL
    ");
    $stmt->execute([':id' => $id_professor]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) $stats_cron = $row;
} catch (PDOException $e) {}

// Busca todos os eventos do professor para o calendário
$eventos_js = '[]';
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT ON (a.id)
            a.id,
            a.titulo,
            COALESCE(a.tipo, 'tarefa') AS tipo,
            a.data,
            COALESCE(CAST(a.hora AS TEXT), '00:00') AS hora,
            COALESCE(a.concluido, false) AS concluido,
            COALESCE(a.status_tarefa, 'pendente') AS status_tarefa,
            COALESCE(u.nome, 'Todos') AS nome_aluno,
            COALESCE(p.titulo, '') AS nome_projeto
        FROM agenda_items a
        JOIN participacao par ON a.id_projeto = par.id_projeto
        LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
        LEFT JOIN projetos p ON a.id_projeto = p.id_projeto
        WHERE par.id_usuario = :id
          AND a.id_projeto IS NOT NULL
          AND a.data IS NOT NULL
        ORDER BY a.id, a.data ASC
    ");
    $stmt->execute([':id' => $id_professor]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $events = [];
    foreach ($rows as $r) {
        $date = new DateTime($r['data']);
        $hora = substr($r['hora'], 0, 5);

        if ($r['concluido'] || $r['status_tarefa'] === 'concluida') {
            $status = 'Concluído';
        } elseif ($r['status_tarefa'] === 'em_andamento') {
            $status = 'Em Andamento';
        } elseif ($date < $today) {
            $status = 'Urgente';
        } else {
            $status = 'Pendente';
        }

        // Normaliza tipo para exibição
        $tipo = $r['tipo'] === 'tarefa' ? 'Tarefa' : $r['tipo'];

        $events[] = [
            'dia'     => (int)$date->format('j'),
            'mes'     => (int)$date->format('n') - 1,
            'ano'     => (int)$date->format('Y'),
            'hora'    => $hora,
            'titulo'  => $r['titulo'],
            'aluno'   => $r['nome_aluno'],
            'projeto' => $r['nome_projeto'],
            'tipo'    => $tipo,
            'status'  => $status,
        ];
    }
    $eventos_js = json_encode($events, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    $eventos_js = '[]';
}
?>

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
            <div><h4 class="mb-0 fw-bold" id="totalEventos"><?= intval($stats_cron['total_mes']) ?></h4><small class="text-muted">Eventos este mês</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-alarm"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= intval($stats_cron['prazos']) ?></h4><small class="text-muted">Prazos próximos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= intval($stats_cron['reunioes']) ?></h4><small class="text-muted">Reuniões agendadas</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-square"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= intval($stats_cron['entregas']) ?></h4><small class="text-muted">Entregas pendentes</small></div>
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
                <span><span class="dot-cal" style="background:#64748b;"></span> Tarefa</span>
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
                <div class="d-flex gap-1 flex-wrap">
                    <button class="btn btn-sm btn-outline-secondary filtro-tipo active" data-tipo="todos">Todos</button>
                    <button class="btn btn-sm btn-outline-secondary filtro-tipo" data-tipo="Tarefa" style="border-color:#64748b;color:#64748b;">Tarefas</button>
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
    // Dados vindos do banco de dados
    const todosEventos = <?= $eventos_js ?>;

    const corTipo = {
        'Tarefa':  '#64748b',
        'Entrega': '#3b82f6',
        'Reunião': '#8b5cf6',
        'Evento':  '#10b981',
        'Prazo':   '#f59e0b',
    };
    const badgeTipo = {
        'Tarefa':  'badge-tarefa',
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

    const style = document.createElement('style');
    style.textContent = '.badge-reuniao { background:#8b5cf6 !important; color:white; } .badge-tarefa { background:#64748b !important; color:white; }';
    document.head.appendChild(style);

    const hoje = new Date();
    let ano = hoje.getFullYear();
    let mes = hoje.getMonth();

    const nomesMeses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

    let filtroAtivo = 'todos';

    function eventosMes(m, y) {
        return todosEventos.filter(e => e.mes === m && e.ano === y);
    }

    function renderCalendario() {
        const grid   = document.getElementById('calGrid');
        const titulo = document.getElementById('calMesAno');
        titulo.textContent = nomesMeses[mes] + ' ' + ano;

        const primeiroDia = new Date(ano, mes, 1).getDay();
        const diasNoMes   = new Date(ano, mes + 1, 0).getDate();
        const ehMesAtual  = (ano === hoje.getFullYear() && mes === hoje.getMonth());

        const eventos = eventosMes(mes, ano);

        // Atualiza card "Eventos este mês"
        document.getElementById('totalEventos').textContent = eventos.length;

        const porDia = {};
        eventos.forEach(e => { (porDia[e.dia] = porDia[e.dia] || []).push(e); });

        grid.innerHTML = '';

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
                document.querySelectorAll('.cal-day.selecionado').forEach(function(el) {
                    el.classList.remove('selecionado');
                });
                this.classList.add('selecionado');
                renderLista(d);
            });

            grid.appendChild(cell);
        }
    }

    function renderLista(diaFiltro) {
        const lista  = document.getElementById('listaAtividades');
        const titulo = document.getElementById('tituloLista');

        let itens = eventosMes(mes, ano);

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
                    <div style="font-weight:400;color:#64748b;">${nomesMeses[ev.mes].slice(0,3)}</div>
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
