<?php

class Aluno {

    // Aqui guardamos a conexão com o banco de dados
    private $pdo;

    // Esse método roda automaticamente quando criamos um objeto Aluno
    // Recebe a conexão e guarda ela para usar nas consultas abaixo
    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    // Busca a carga horária total de um aluno somando todas as participações dele
    public function obterCargaHorariaTotal($id_usuario) {
        $sql = "SELECT COALESCE(SUM(carga_horaria), 0) FROM participacao WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    public function obterProjetosAtivos($id_usuario) {
        $sql = "
            SELECT p.titulo
            FROM projetos p
            JOIN participacao pa ON p.id_projeto = pa.id_projeto
            WHERE pa.id_usuario = :id AND pa.status = 'ativo'
            ORDER BY p.titulo ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obterEstatisticasProjetos($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM participacao WHERE id_usuario = :id AND status = 'ativo'");
        $stmt->execute([':id' => $id_usuario]);
        $ativos = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM participacao WHERE id_usuario = :id AND status = 'concluido'");
        $stmt->execute([':id' => $id_usuario]);
        $concluidos = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(carga_horaria), 0) FROM participacao WHERE id_usuario = :id");
        $stmt->execute([':id' => $id_usuario]);
        $cargaTotal = $stmt->fetchColumn();

        return [
            'ativos'     => $ativos,
            'total'      => $ativos + $concluidos,
            'concluidos' => $concluidos,
            'carga'      => $cargaTotal,
        ];
    }

    public function listarProjetosAluno($id_usuario) {
        $sql = "
            SELECT
                p.id_projeto,
                p.titulo,
                p.descricao,
                p.area,
                p.data_inicio,
                p.data_fim,
                tp.nome      AS tipo,
                pa.funcao,
                pa.carga_horaria,
                pa.status,
                (
                    SELECT u.nome FROM participacao po
                    JOIN usuarios u ON po.id_usuario = u.id_usuario
                    WHERE po.id_projeto = p.id_projeto
                      AND po.funcao ILIKE '%orientador%'
                    LIMIT 1
                ) AS orientador,
                (
                    SELECT json_agg(
                        json_build_object('nome', u.nome, 'funcao', po.funcao)
                        ORDER BY
                            CASE WHEN LOWER(CAST(po.funcao AS TEXT)) LIKE '%orientador%' THEN 0 ELSE 1 END,
                            u.nome ASC
                    )
                    FROM participacao po
                    JOIN usuarios u ON po.id_usuario = u.id_usuario
                    WHERE po.id_projeto = p.id_projeto
                ) AS participantes
            FROM participacao pa
            JOIN projetos p     ON pa.id_projeto = p.id_projeto
            LEFT JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            WHERE pa.id_usuario = :id
            ORDER BY pa.status ASC, p.titulo ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterAgenda($id_usuario) {
        $sql = "
            SELECT ai.id, ai.titulo, ai.data, ai.hora, ai.tipo, ai.concluido,
                   COALESCE(proj.titulo, '—') AS projeto
            FROM agenda_items ai
            LEFT JOIN projetos proj ON proj.id_projeto = ai.id_projeto
            WHERE ai.id_usuario = :id
            ORDER BY ai.data ASC
            LIMIT 50
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tarefas = [];
        $eventos = [];
        foreach ($itens as $item) {
            if ($item['tipo'] === 'tarefa') $tarefas[] = $item;
            else                            $eventos[] = $item;
        }
        return ['tarefas' => $tarefas, 'eventos' => $eventos];
    }

    public function obterParticipacoes($id_usuario){
        $sql="
            SELECT
            p.titulo AS projeto,
            tp.nome AS tipo,
            pa.funcao,
            pa.carga_horaria,
            pa.status
            FROM participacao pa
            JOIN projetos p ON pa.id_projeto=p.id_projeto
            LEFT JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            WHERE pa.id_usuario= :id
            ORDER BY pa.status ASC, p.titulo ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id'=>$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterEstatisticasTarefas($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND tipo = 'tarefa' AND (concluido = false OR concluido IS NULL) AND data >= CURRENT_DATE");
        $stmt->execute([':id' => $id_usuario]);
        $pendentes = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND tipo = 'tarefa' AND (concluido = false OR concluido IS NULL) AND data < CURRENT_DATE AND data >= CURRENT_DATE - INTERVAL '7 days'");
        $stmt->execute([':id' => $id_usuario]);
        $naoConcluidos = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND tipo = 'tarefa' AND concluido = true AND data >= CURRENT_DATE - INTERVAL '7 days'");
        $stmt->execute([':id' => $id_usuario]);
        $concluidos = $stmt->fetchColumn();

        return [
            'pendentes'      => $pendentes,
            'nao_concluidos' => $naoConcluidos,
            'concluidos'     => $concluidos,
        ];
    }

    public function obterEstatisticasCronograma($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND (concluido = false OR concluido IS NULL) AND data >= CURRENT_DATE");
        $stmt->execute([':id' => $id_usuario]);
        $proximos = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND (concluido = false OR concluido IS NULL) AND data < CURRENT_DATE AND data >= CURRENT_DATE - INTERVAL '7 days'");
        $stmt->execute([':id' => $id_usuario]);
        $naoConcluidos = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND concluido = true AND data >= CURRENT_DATE - INTERVAL '7 days'");
        $stmt->execute([':id' => $id_usuario]);
        $concluidos = $stmt->fetchColumn();

        return ['proximos' => $proximos, 'nao_concluidos' => $naoConcluidos, 'concluidos' => $concluidos];
    }

    public function listarCronograma($id_usuario) {
        $sql = "
            SELECT
                ai.id, ai.titulo, ai.descricao, ai.tipo, ai.data, ai.hora, ai.concluido,
                COALESCE(proj.titulo, '—') AS projeto,
                COALESCE(ai.id_projeto::text, '') AS id_projeto_ref,
                lp.arquivos
            FROM agenda_items ai
            LEFT JOIN projetos proj ON proj.id_projeto = ai.id_projeto
            LEFT JOIN LATERAL (
                SELECT COALESCE(
                    json_agg(json_build_object('id', p.id_producao, 'caminho', 'pages-aluno/servir-arquivo.php?id=' || p.id_producao::text, 'nome', p.tipo)
                             ORDER BY p.id_producao ASC)
                    FILTER (WHERE p.id_producao IS NOT NULL),
                    '[]'::json
                ) AS arquivos
                FROM producoes p
                WHERE p.id_projeto = ai.id_projeto
                  AND p.titulo = ai.titulo
                  AND p.status != 'inativo'
            ) lp ON true
            WHERE ai.id_usuario = :id
              AND ai.data >= CURRENT_DATE - INTERVAL '7 days'
            ORDER BY ai.data ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleConcluido($id, $id_usuario) {
        $sql = "UPDATE agenda_items
                SET concluido     = NOT concluido,
                    status_tarefa = CASE WHEN concluido = false THEN 'concluido' ELSE 'pendente' END
                WHERE id = :id AND id_usuario = :id_usuario
                RETURNING concluido::int";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':id_usuario' => $id_usuario]);
        return (bool)(int)$stmt->fetchColumn();
    }

    public function listarAgendaAberta($id_usuario) {
        $sql = "
            SELECT
                ai.id, ai.titulo, ai.descricao, ai.tipo, ai.data, ai.hora, ai.concluido,
                COALESCE(proj.titulo, '—') AS projeto,
                COALESCE(ai.id_projeto::text, '') AS id_projeto_ref,
                lp.arquivos
            FROM agenda_items ai
            LEFT JOIN projetos proj ON proj.id_projeto = ai.id_projeto
            LEFT JOIN LATERAL (
                SELECT COALESCE(
                    json_agg(json_build_object('id', p.id_producao, 'caminho', 'pages-aluno/servir-arquivo.php?id=' || p.id_producao::text, 'nome', p.tipo)
                             ORDER BY p.id_producao ASC)
                    FILTER (WHERE p.id_producao IS NOT NULL),
                    '[]'::json
                ) AS arquivos
                FROM producoes p
                WHERE p.id_projeto = ai.id_projeto
                  AND p.titulo = ai.titulo
                  AND p.status != 'inativo'
            ) lp ON true
            WHERE ai.id_usuario = :id
              AND ai.tipo = 'tarefa'
              AND ai.data >= CURRENT_DATE - INTERVAL '7 days'
            ORDER BY ai.data ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodosRegistros($id_usuario) {
        $sql = "
            SELECT
                ai.id, ai.titulo, ai.descricao, ai.tipo,
                ai.data, ai.hora, ai.concluido, ai.created_at,
                COALESCE(proj.titulo, '—') AS projeto
            FROM agenda_items ai
            LEFT JOIN projetos proj ON proj.id_projeto = ai.id_projeto
            WHERE ai.id_usuario = :id
            ORDER BY ai.data DESC, ai.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterEstatisticasRegistros($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id");
        $stmt->execute([':id' => $id_usuario]);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND concluido = true");
        $stmt->execute([':id' => $id_usuario]);
        $concluidos = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agenda_items WHERE id_usuario = :id AND (concluido = false OR concluido IS NULL)");
        $stmt->execute([':id' => $id_usuario]);
        $pendentes = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM agenda_items
            WHERE id_usuario = :id
              AND date_trunc('month', data) = date_trunc('month', CURRENT_DATE)
        ");
        $stmt->execute([':id' => $id_usuario]);
        $este_mes = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("
            SELECT tipo, COUNT(*) AS qtd
            FROM agenda_items
            WHERE id_usuario = :id
            GROUP BY tipo
            ORDER BY qtd DESC
        ");
        $stmt->execute([':id' => $id_usuario]);
        $por_tipo = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'total'      => $total,
            'concluidos' => $concluidos,
            'pendentes'  => $pendentes,
            'este_mes'   => $este_mes,
            'por_tipo'   => $por_tipo,
        ];
    }
}
?>
