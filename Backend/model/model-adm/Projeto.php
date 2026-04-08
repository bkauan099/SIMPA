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