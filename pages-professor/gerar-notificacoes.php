<?php
// Gera o array $notificacoes para o professor com base em $pdo e $id_usuario já definidos.

$notificacoes = [];

// 1. Documentos pendentes enviados por alunos
try {
    $s = $pdo->prepare(
        "SELECT pr.titulo AS doc_titulo, u.nome AS aluno_nome
         FROM producoes pr
         LEFT JOIN projetos p ON pr.id_projeto = p.id_projeto
         LEFT JOIN participacao par ON p.id_projeto = par.id_projeto AND par.id_usuario != :id_prof
         LEFT JOIN usuarios u ON par.id_usuario = u.id_usuario
         WHERE pr.status = 'pendente'
           AND p.id_projeto IN (
               SELECT id_projeto FROM participacao WHERE id_usuario = :id_prof2
           )
         ORDER BY pr.id_producao DESC LIMIT 15"
    );
    $s->execute([':id_prof' => $id_usuario, ':id_prof2' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $quem = !empty($r['aluno_nome']) ? htmlspecialchars($r['aluno_nome']) : 'Um aluno';
        $notificacoes[] = [
            'icone' => 'bi-file-earmark-arrow-up-fill',
            'cor'   => '#3b82f6',
            'texto' => $quem . ' enviou um documento: <strong>' . htmlspecialchars($r['doc_titulo']) . '</strong>',
        ];
    }
} catch (Exception $e) {}

// 2. Tarefas com prazo vencendo hoje
try {
    $s = $pdo->prepare(
        "SELECT a.titulo, u.nome AS aluno_nome
         FROM agenda_items a
         LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
         JOIN participacao par ON a.id_projeto = par.id_projeto
         WHERE par.id_usuario = :id_prof
           AND a.id_projeto IS NOT NULL
           AND a.data = CURRENT_DATE
           AND (a.concluido = false OR a.concluido IS NULL)
         ORDER BY a.titulo ASC LIMIT 10"
    );
    $s->execute([':id_prof' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $quem = !empty($r['aluno_nome']) ? ' (' . htmlspecialchars($r['aluno_nome']) . ')' : '';
        $notificacoes[] = [
            'icone' => 'bi-alarm-fill',
            'cor'   => '#f97316',
            'texto' => 'Prazo hoje: <strong>' . htmlspecialchars($r['titulo']) . '</strong>' . $quem,
        ];
    }
} catch (Exception $e) {}

// 3. Tarefas em atraso (últimos 7 dias)
try {
    $s = $pdo->prepare(
        "SELECT a.titulo, a.data, u.nome AS aluno_nome
         FROM agenda_items a
         LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
         JOIN participacao par ON a.id_projeto = par.id_projeto
         WHERE par.id_usuario = :id_prof
           AND a.id_projeto IS NOT NULL
           AND a.data < CURRENT_DATE
           AND a.data >= CURRENT_DATE - INTERVAL '7 days'
           AND (a.concluido = false OR a.concluido IS NULL)
         ORDER BY a.data ASC LIMIT 10"
    );
    $s->execute([':id_prof' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $dt   = new DateTime($r['data']);
        $quem = !empty($r['aluno_nome']) ? ' — ' . htmlspecialchars($r['aluno_nome']) : '';
        $notificacoes[] = [
            'icone' => 'bi-exclamation-triangle-fill',
            'cor'   => '#ef4444',
            'texto' => 'Tarefa em atraso: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (venceu em ' . $dt->format('d/m/Y') . ')' . $quem,
        ];
    }
} catch (Exception $e) {}

// 4. Tarefas próximas nos próximos 7 dias
try {
    $s = $pdo->prepare(
        "SELECT a.titulo, a.data, u.nome AS aluno_nome
         FROM agenda_items a
         LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
         JOIN participacao par ON a.id_projeto = par.id_projeto
         WHERE par.id_usuario = :id_prof
           AND a.id_projeto IS NOT NULL
           AND a.data > CURRENT_DATE
           AND a.data <= CURRENT_DATE + INTERVAL '7 days'
           AND (a.concluido = false OR a.concluido IS NULL)
         ORDER BY a.data ASC LIMIT 10"
    );
    $s->execute([':id_prof' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $dt   = new DateTime($r['data']);
        $quem = !empty($r['aluno_nome']) ? ' (' . htmlspecialchars($r['aluno_nome']) . ')' : '';
        $notificacoes[] = [
            'icone' => 'bi-calendar-check',
            'cor'   => '#f59e0b',
            'texto' => 'Prazo em breve: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (vence em ' . $dt->format('d/m/Y') . ')' . $quem,
        ];
    }
} catch (Exception $e) {}

$notificacoes = array_slice($notificacoes, 0, 30);
