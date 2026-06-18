<?php
// Gera o array $notificacoes com base em $pdo e $id_usuario já definidos.

$_stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$_stmtMat->execute([':id' => $id_usuario]);
$_matricula_notif = $_stmtMat->fetchColumn();

$notificacoes = [];

// Tarefas recém-cadastradas (últimas 24h)
try {
    $s = $pdo->prepare(
        "SELECT titulo, data, created_at FROM agenda_items
         WHERE id_usuario = :id AND tipo = 'tarefa'
           AND created_at >= NOW() - INTERVAL '24 hours'
         ORDER BY created_at DESC LIMIT 10"
    );
    $s->execute([':id' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $dt = new DateTime($r['data']);
        $notificacoes[] = [
            'icone' => 'bi-plus-circle-fill',
            'cor'   => '#ef4444',
            'texto' => 'Nova tarefa: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (prazo: ' . $dt->format('d/m/Y') . ')',
            'ts'    => strtotime($r['created_at']),
        ];
    }
} catch (Exception $e) {}

// Tarefas em atraso (últimos 15 dias)
try {
    $s = $pdo->prepare(
        "SELECT titulo, data FROM agenda_items
         WHERE id_usuario = :id AND tipo = 'tarefa'
           AND (concluido = false OR concluido IS NULL)
           AND data < CURRENT_DATE
           AND data >= CURRENT_DATE - INTERVAL '15 days'
         ORDER BY data ASC LIMIT 10"
    );
    $s->execute([':id' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $dt = new DateTime($r['data']);
        $notificacoes[] = [
            'icone' => 'bi-exclamation-triangle-fill',
            'cor'   => '#ef4444',
            'texto' => 'Tarefa em atraso: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (venceu em ' . $dt->format('d/m/Y') . ')',
            'ts'    => strtotime($r['data']),
        ];
    }
} catch (Exception $e) {}

// Tarefas que vencem hoje
try {
    $s = $pdo->prepare(
        "SELECT titulo FROM agenda_items
         WHERE id_usuario = :id AND tipo = 'tarefa'
           AND (concluido = false OR concluido IS NULL)
           AND data = CURRENT_DATE
         ORDER BY titulo ASC LIMIT 10"
    );
    $s->execute([':id' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $notificacoes[] = [
            'icone' => 'bi-alarm-fill',
            'cor'   => '#f97316',
            'texto' => 'Prazo hoje: <strong>' . htmlspecialchars($r['titulo']) . '</strong> — entregue antes da meia-noite!',
            'ts'    => time(),
        ];
    }
} catch (Exception $e) {}

// Tarefas próximas (próximos 7 dias, exceto hoje)
try {
    $s = $pdo->prepare(
        "SELECT titulo, data FROM agenda_items
         WHERE id_usuario = :id AND tipo = 'tarefa'
           AND (concluido = false OR concluido IS NULL)
           AND data > CURRENT_DATE
           AND data <= CURRENT_DATE + INTERVAL '7 days'
         ORDER BY data ASC LIMIT 10"
    );
    $s->execute([':id' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $dt = new DateTime($r['data']);
        $notificacoes[] = [
            'icone' => 'bi-calendar-check',
            'cor'   => '#f59e0b',
            'texto' => 'Tarefa próxima: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (vence em ' . $dt->format('d/m/Y') . ')',
            // Data é futura — espelha a distância como se fosse passada, senão
            // uma tarefa que vence em 5 dias ficaria "mais recente" que algo
            // que aconteceu agora mesmo
            'ts'    => time() - (strtotime($r['data']) - time()),
        ];
    }
} catch (Exception $e) {}

// Eventos desta semana
try {
    $s = $pdo->prepare(
        "SELECT titulo, data FROM agenda_items
         WHERE id_usuario = :id AND tipo = 'evento'
           AND (concluido = false OR concluido IS NULL)
           AND data >= CURRENT_DATE
           AND data <= CURRENT_DATE + INTERVAL '7 days'
         ORDER BY data ASC LIMIT 10"
    );
    $s->execute([':id' => $id_usuario]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $dt    = new DateTime($r['data']);
        $hoje  = (new DateTime())->format('Y-m-d');
        $label = $r['data'] === $hoje ? 'hoje' : 'em ' . $dt->format('d/m/Y');
        $notificacoes[] = [
            'icone' => 'bi-calendar-event-fill',
            'cor'   => '#3b82f6',
            'texto' => 'Evento esta semana: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (' . $label . ')',
            'ts'    => time() - (strtotime($r['data']) - time()),
        ];
    }
} catch (Exception $e) {}

// Documentos aprovados, reprovados ou para refazer
if ($_matricula_notif) {
    try {
        $s = $pdo->prepare(
            "SELECT titulo, status, data_registro FROM producoes
             WHERE caminho LIKE :prefix
               AND status IN ('concluido', 'cancelado', 'refazer')
             ORDER BY id_producao DESC LIMIT 10"
        );
        $s->execute([':prefix' => 'uploads/producoes/aluno/' . $_matricula_notif . '/%']);
        foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $titulo = htmlspecialchars($r['titulo']);
            $ts     = strtotime($r['data_registro']) ?: time();
            if ($r['status'] === 'concluido') {
                $notificacoes[] = [
                    'icone' => 'bi-check-circle-fill',
                    'cor'   => '#22c55e',
                    'texto' => 'Documento <strong>' . $titulo . '</strong> foi aprovado',
                    'ts'    => $ts,
                ];
            } elseif ($r['status'] === 'refazer') {
                $notificacoes[] = [
                    'icone' => 'bi-arrow-repeat',
                    'cor'   => '#ea580c',
                    'texto' => 'Documento <strong>' . $titulo . '</strong> foi reprovado. Acesse a página de tarefas para reenviar com as correções',
                    'ts'    => $ts,
                ];
            } else {
                $notificacoes[] = [
                    'icone' => 'bi-x-circle-fill',
                    'cor'   => '#ef4444',
                    'texto' => 'Documento <strong>' . $titulo . '</strong> foi reprovado sem direito à correção',
                    'ts'    => $ts,
                ];
            }
        }
    } catch (Exception $e) {}
}

// Ordena por timestamp — mais recente primeiro (os blocos acima são gerados
// em grupos por categoria, então sem isso eles ficavam um tipo inteiro de
// cada vez, em vez de intercalados por data real)
usort($notificacoes, fn($a, $b) => ($b['ts'] ?? 0) <=> ($a['ts'] ?? 0));

$notificacoes = array_slice($notificacoes, 0, 30);
$notificacoes = array_map(fn($n) => ['icone' => $n['icone'], 'cor' => $n['cor'], 'texto' => $n['texto']], $notificacoes);
