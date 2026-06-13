<?php
// Gera o array $notificacoes com base em $pdo e $id_usuario já definidos.

$_stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$_stmtMat->execute([':id' => $id_usuario]);
$_matricula_notif = $_stmtMat->fetchColumn();

$notificacoes = [];

// Tarefas recém-cadastradas (últimas 24h)
try {
    $s = $pdo->prepare(
        "SELECT titulo, data FROM agenda_items
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
        ];
    }
} catch (Exception $e) {}

// Documentos aprovados, reprovados ou para refazer
if ($_matricula_notif) {
    try {
        $s = $pdo->prepare(
            "SELECT titulo, status FROM producoes
             WHERE caminho LIKE :prefix
               AND status IN ('concluido', 'cancelado', 'refazer')
               AND data_registro >= NOW() - INTERVAL '15 days'
             ORDER BY id_producao DESC LIMIT 10"
        );
        $s->execute([':prefix' => 'uploads/alunos/' . $_matricula_notif . '/%']);
        foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $titulo = htmlspecialchars($r['titulo']);
            if ($r['status'] === 'concluido') {
                $notificacoes[] = [
                    'icone' => 'bi-check-circle-fill',
                    'cor'   => '#22c55e',
                    'texto' => 'Documento <strong>' . $titulo . '</strong> foi aprovado',
                ];
            } elseif ($r['status'] === 'refazer') {
                $notificacoes[] = [
                    'icone' => 'bi-arrow-repeat',
                    'cor'   => '#ea580c',
                    'texto' => 'Documento <strong>' . $titulo . '</strong> foi reprovado. Acesse a página de tarefas para reenviar com as correções',
                ];
            } else {
                $notificacoes[] = [
                    'icone' => 'bi-x-circle-fill',
                    'cor'   => '#ef4444',
                    'texto' => 'Documento <strong>' . $titulo . '</strong> foi reprovado sem direito à correção',
                ];
            }
        }
    } catch (Exception $e) {}
}

$notificacoes = array_slice($notificacoes, 0, 30);
