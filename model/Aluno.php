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

    // Busca o projeto ativo do aluno (para exibir no cabeçalho de tarefas)
    public function obterProjetoAtivo($id_usuario) {
        $sql = "
            SELECT p.titulo
            FROM projetos p
            JOIN participacao pa ON p.id_projeto = pa.id_projeto
            WHERE pa.id_usuario = :id AND pa.status = 'ativo'
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchColumn();
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
                p.titulo,
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
                ) AS orientador
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
            SELECT titulo, data, hora, tipo
            FROM agenda_items
            WHERE id_usuario = :id
            ORDER BY data ASC
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
            LEFT JOIN tipo_projetos tp ON p.id_tipo

    }
}
?>
