<?php

class Projeto {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas() {
        $stats = ['projetos_ativos' => 0, 'total_usuarios' => 0, 'pendencias' => 8, 'notificacoes' => 3];
        
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM projetos WHERE status = 'ativo'");
        $stats['projetos_ativos'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM usuarios");
        $stats['total_usuarios'] = $stmt->fetchColumn();

        return $stats;
    }

    public function obterEstatisticasProfessor($id_professor) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT p.id_projeto) AS ativos
            FROM projetos p
            JOIN participacao pa ON pa.id_projeto = p.id_projeto
            WHERE pa.id_usuario = ? AND p.status = 'ativo'
        ");
        $stmt->execute([$id_professor]);
        $ativos = (int)$stmt->fetchColumn();

        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT pa2.id_usuario) AS alunos
            FROM participacao pa2
            JOIN usuarios u ON pa2.id_usuario = u.id_usuario
            WHERE pa2.id_projeto IN (
                SELECT id_projeto FROM participacao WHERE id_usuario = ?
            ) AND CAST(u.perfil AS TEXT) ILIKE '%aluno%'
        ");
        $stmt->execute([$id_professor]);
        $alunos = (int)$stmt->fetchColumn();

        return ['ativos' => $ativos, 'alunos' => $alunos];
    }

    public function obterDadosGrafico($id_professor) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(CAST(p.tipo AS TEXT), 'Sem tipo') AS nome, COUNT(*) AS total
            FROM projetos p
            JOIN participacao pa ON pa.id_projeto = p.id_projeto
            WHERE pa.id_usuario = ? AND p.status = 'ativo'
            GROUP BY p.tipo
            ORDER BY total DESC
        ");
        $stmt->execute([$id_professor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProjetosAtivos() {
        $sql = "
            SELECT p.id_projeto, p.titulo, p.status,
                (SELECT u.nome FROM participacao pa JOIN usuarios u ON pa.id_usuario = u.id_usuario 
                 WHERE pa.id_projeto = p.id_projeto AND pa.funcao ILIKE '%Orientador%' LIMIT 1) AS orientador,
                (SELECT COUNT(id_participacao) FROM participacao WHERE id_projeto = p.id_projeto) AS total_participantes
            FROM projetos p WHERE p.status = 'ativo' ORDER BY p.id_projeto ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>