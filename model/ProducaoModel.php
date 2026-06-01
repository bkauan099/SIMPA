<?php
// model/ProducaoModel.php
// Tabela real no banco: producoes
// status_geral ENUM: 'ativo', 'inativo' (sem 'pendente' - ajustado abaixo)

class ProducaoModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas() {
        $stats = ['total' => 0, 'aprovados' => 0, 'pendentes' => 0, 'rejeitados' => 0];

        $stats['total']      = $this->pdo->query("SELECT COUNT(*) FROM producoes")->fetchColumn();
        $stats['aprovados']  = $this->pdo->query("SELECT COUNT(*) FROM producoes WHERE status = 'ativo'")->fetchColumn();
        $stats['pendentes']  = $this->pdo->query("SELECT COUNT(*) FROM producoes WHERE status = 'pendente'")->fetchColumn();
        $stats['rejeitados'] = $this->pdo->query("SELECT COUNT(*) FROM producoes WHERE status = 'inativo'")->fetchColumn();

        return $stats;
    }

    public function listarTodas() {
        $sql = "
            SELECT
                pd.id_producao,
                pd.titulo,
                pd.tipo,
                pd.caminho,
                pd.data_registro,
                pd.status,
                p.titulo  AS projeto_titulo,
                p.id_projeto
            FROM producoes pd
            JOIN projetos p ON pd.id_projeto = p.id_projeto
            ORDER BY pd.data_registro DESC NULLS LAST
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "
            SELECT pd.*, p.titulo AS projeto_titulo
            FROM producoes pd
            JOIN projetos p ON pd.id_projeto = p.id_projeto
            WHERE pd.id_producao = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $sql = "INSERT INTO producoes (id_projeto, titulo, tipo, caminho, status)
                VALUES (:id_projeto, :titulo, :tipo, :caminho, :status)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_projeto' => (int)$dados['id_projeto'],
            ':titulo'     => $dados['titulo'],
            ':tipo'       => $dados['tipo']    ?? 'outro',
            ':caminho'    => $dados['caminho'] ?? '',
            ':status'     => in_array($dados['status'] ?? '', ['ativo','inativo','pendente'])
                                ? $dados['status'] : 'pendente',
        ]);
    }

    public function alterarStatus($id, $status) {
        $validos = ['ativo', 'inativo', 'pendente'];
        if (!in_array($status, $validos)) return false;
        $stmt = $this->pdo->prepare("UPDATE producoes SET status = :status WHERE id_producao = :id");
        return $stmt->execute([':status' => $status, ':id' => (int)$id]);
    }

    public function excluir($id) {
        $stmt = $this->pdo->prepare("DELETE FROM producoes WHERE id_producao = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
?>
