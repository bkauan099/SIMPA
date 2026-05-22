<?php
class RelatorioExportModel {
    private $pdo;

    public function __construct($conexao) { $this->pdo = $conexao; }

    /** Dados completos de um projeto para exportação */
    public function dadosProjeto($id_projeto) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, CAST(p.status AS TEXT) AS status,
                   tp.nome AS tipo_nome
            FROM projetos p
            LEFT JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            WHERE p.id_projeto = :id
        ");
        $stmt->execute([':id' => (int)$id_projeto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function membrosProjeto($id_projeto) {
        $stmt = $this->pdo->prepare("
            SELECT u.nome, u.email, u.matricula, CAST(u.perfil AS TEXT) AS perfil,
                   pa.funcao, pa.carga_horaria, pa.data_entrada, pa.data_saida,
                   CAST(pa.status AS TEXT) AS status
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            WHERE pa.id_projeto = :id
            ORDER BY pa.funcao, u.nome
        ");
        $stmt->execute([':id' => (int)$id_projeto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function producoesProjeto($id_projeto) {
        $stmt = $this->pdo->prepare("
            SELECT titulo, tipo, caminho, data_registro, CAST(status AS TEXT) AS status
            FROM producoes
            WHERE id_projeto = :id
            ORDER BY data_registro DESC NULLS LAST, tipo
        ");
        $stmt->execute([':id' => (int)$id_projeto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Todos os projetos com filtro opcional de status */
    public function listarProjetosParaExport($status = null) {
        $where = $status ? "WHERE CAST(p.status AS TEXT) = " . $this->pdo->quote($status) : '';
        $sql = "
            SELECT p.id_projeto, p.titulo, CAST(p.status AS TEXT) AS status,
                   p.area, p.data_inicio, p.data_fim, tp.nome AS tipo,
                   COUNT(DISTINCT pa.id_participacao) AS total_membros,
                   COUNT(DISTINCT pd.id_producao) AS total_producoes
            FROM projetos p
            LEFT JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            LEFT JOIN participacao pa ON pa.id_projeto = p.id_projeto
            LEFT JOIN producoes pd ON pd.id_projeto = p.id_projeto
            $where
            GROUP BY p.id_projeto, p.titulo, p.status, p.area, p.data_inicio, p.data_fim, tp.nome
            ORDER BY p.titulo ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
