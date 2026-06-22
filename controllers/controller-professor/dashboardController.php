<?php
// controllers/controller-professor/dashboardController.php

require_once __DIR__ . '/../../model/Projeto.php';

class DashboardController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $id_professor = $_SESSION['id_usuario'] ?? null;
            if (!$id_professor) {
                echo "<div class='alert alert-danger'>Sessão expirada. Faça login novamente.</div>";
                return;
            }

            $projetoModel = new Projeto($this->pdo);

            $estatisticas      = $projetoModel->obterEstatisticasProfessor($id_professor);
            $distribuicaoTipos = $projetoModel->obterDadosGrafico($id_professor);

            // Docs pendentes (producoes status='pendente' nos projetos do professor)
            $stmtDocs = $this->pdo->prepare("
                SELECT COUNT(DISTINCT pr.id_producao)
                FROM producoes pr
                JOIN participacao pa ON pr.id_projeto = pa.id_projeto
                WHERE pa.id_usuario = ? AND pr.status = 'pendente'
            ");
            $stmtDocs->execute([$id_professor]);
            $estatisticas['docs_pendentes'] = (int)$stmtDocs->fetchColumn();

            // Tarefas vencendo (prazo hoje ou passado, não concluídas)
            $stmtTarefas = $this->pdo->prepare("
                SELECT COUNT(DISTINCT a.id)
                FROM agenda_items a
                JOIN participacao pa ON a.id_projeto = pa.id_projeto
                WHERE pa.id_usuario = ?
                  AND a.id_projeto IS NOT NULL
                  AND COALESCE(
                      (SELECT pr.status FROM producoes pr
                       WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
                       ORDER BY pr.id_producao DESC LIMIT 1),
                      'pendente') != 'concluido'
                  AND a.data <= CURRENT_DATE
            ");
            $stmtTarefas->execute([$id_professor]);
            $estatisticas['tarefas_vencendo'] = (int)$stmtTarefas->fetchColumn();

            // Agenda: hoje e amanhã
            $stmtAgenda = $this->pdo->prepare("
                SELECT DISTINCT ON (a.id)
                    a.id, a.titulo,
                    COALESCE(a.tipo, 'Tarefa') AS tipo,
                    a.data,
                    COALESCE(CAST(a.hora AS TEXT), '') AS hora,
                    p.titulo AS nome_projeto
                FROM agenda_items a
                JOIN participacao pa ON a.id_projeto = pa.id_projeto
                LEFT JOIN projetos p ON a.id_projeto = p.id_projeto
                WHERE pa.id_usuario = ?
                  AND a.id_projeto IS NOT NULL
                  AND a.data IN (CURRENT_DATE, CURRENT_DATE + INTERVAL '1 day')
                ORDER BY a.id, a.data ASC, a.hora ASC NULLS LAST
                LIMIT 10
            ");
            $stmtAgenda->execute([$id_professor]);
            $agenda = $stmtAgenda->fetchAll(PDO::FETCH_ASSOC);

            // Atenção necessária: alunos com tarefas atrasadas/urgentes
            $stmtAtencao = $this->pdo->prepare("
                SELECT
                    u.id_usuario,
                    u.nome,
                    COUNT(a.id) AS total_atrasadas,
                    MAX(a.prioridade) AS max_prioridade
                FROM agenda_items a
                JOIN participacao pa ON a.id_projeto = pa.id_projeto
                JOIN usuarios u ON a.id_usuario = u.id_usuario
                WHERE pa.id_usuario = ?
                  AND a.id_projeto IS NOT NULL
                  AND COALESCE(
                      (SELECT pr.status FROM producoes pr
                       WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
                       ORDER BY pr.id_producao DESC LIMIT 1),
                      'pendente') != 'concluido'
                  AND a.data < CURRENT_DATE
                GROUP BY u.id_usuario, u.nome
                ORDER BY total_atrasadas DESC
                LIMIT 5
            ");
            $stmtAtencao->execute([$id_professor]);
            $atencaoNecessaria = $stmtAtencao->fetchAll(PDO::FETCH_ASSOC);

            // Atividade recente: uploads recentes nos projetos do professor
            $stmtAtiv = $this->pdo->prepare("
                SELECT
                    pr.id_producao,
                    COALESCE(pr.titulo, pr.tipo) AS nome_arquivo,
                    pr.data_registro,
                    pr.status,
                    (SELECT u2.nome FROM participacao pa3
                     JOIN usuarios u2 ON pa3.id_usuario = u2.id_usuario
                     WHERE pa3.id_projeto = pr.id_projeto AND u2.perfil = 'aluno'
                     ORDER BY pa3.id_participacao ASC LIMIT 1) AS nome_aluno,
                    p.titulo AS nome_projeto
                FROM producoes pr
                JOIN projetos p ON pr.id_projeto = p.id_projeto
                WHERE pr.id_projeto IN (
                    SELECT id_projeto FROM participacao WHERE id_usuario = ?
                )
                ORDER BY pr.data_registro DESC
                LIMIT 8
            ");
            $stmtAtiv->execute([$id_professor]);
            $atividadeRecente = $stmtAtiv->fetchAll(PDO::FETCH_ASSOC);

            // Documentos pendentes (lista)
            $stmtDocList = $this->pdo->prepare("
                SELECT
                    pr.id_producao,
                    COALESCE(pr.titulo, pr.tipo) AS nome_arquivo,
                    pr.data_registro,
                    pr.caminho,
                    (SELECT u2.nome FROM participacao pa3
                     JOIN usuarios u2 ON pa3.id_usuario = u2.id_usuario
                     WHERE pa3.id_projeto = pr.id_projeto AND u2.perfil = 'aluno'
                     ORDER BY pa3.id_participacao ASC LIMIT 1) AS nome_aluno,
                    p.titulo AS nome_projeto
                FROM producoes pr
                JOIN projetos p ON pr.id_projeto = p.id_projeto
                WHERE pr.id_projeto IN (
                    SELECT id_projeto FROM participacao WHERE id_usuario = ?
                ) AND pr.status = 'pendente'
                ORDER BY pr.data_registro DESC
                LIMIT 5
            ");
            $stmtDocList->execute([$id_professor]);
            $documentosPendentes = $stmtDocList->fetchAll(PDO::FETCH_ASSOC);

            require __DIR__ . '/../../views/view-professor/pagina-inicial.view.php';

        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar o painel.</div>";
        }
    }
}