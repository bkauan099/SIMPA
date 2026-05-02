<?php
// model/ProducaoModel.php

class ProducaoModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas() {
        try {
            $total      = (int)$this->pdo->query("SELECT COUNT(*) FROM producoes")->fetchColumn();
            $aprovados  = (int)$this->pdo->query("SELECT COUNT(*) FROM producoes WHERE CAST(status AS TEXT) = 'ativo'")->fetchColumn();
            $pendentes  = (int)$this->pdo->query("SELECT COUNT(*) FROM producoes WHERE CAST(status AS TEXT) = 'pendente'")->fetchColumn();
            $rejeitados = (int)$this->pdo->query("SELECT COUNT(*) FROM producoes WHERE CAST(status AS TEXT) = 'inativo'")->fetchColumn();
        } catch (Exception $e) {
            $total = $aprovados = $pendentes = $rejeitados = 0;
        }
        return compact('total', 'aprovados', 'pendentes', 'rejeitados');
    }

    public function listarTodas() {
        $sql = "
            SELECT
                pd.id_producao,
                pd.titulo,
                pd.tipo,
                pd.caminho,
                pd.data_registro,
                CAST(pd.status AS TEXT) AS status,
                p.titulo  AS projeto_titulo,
                p.id_projeto
            FROM producoes pd
            JOIN projetos p ON pd.id_projeto = p.id_projeto
            ORDER BY pd.data_registro DESC NULLS LAST
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
            ':status'     => 'ativo', // aprovado direto pelo ADM
        ]);
    }

    public function alterarStatus($id, $status) {
        $validos = ['ativo', 'inativo', 'pendente'];
        if (!in_array($status, $validos)) return false;
        $stmt = $this->pdo->prepare("UPDATE producoes SET status = :status WHERE id_producao = :id");
        return $stmt->execute([':status' => $status, ':id' => (int)$id]);
    }

    public function excluir($id) {
        // Busca o caminho antes de deletar para remover o arquivo físico
        $stmt = $this->pdo->prepare("SELECT caminho FROM producoes WHERE id_producao = :id");
        $stmt->execute([':id' => (int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $this->pdo->prepare("DELETE FROM producoes WHERE id_producao = :id");
        $ok = $stmt2->execute([':id' => (int)$id]);

        // Remove arquivo físico se existir dentro de uploads/
        if ($ok && !empty($row['caminho']) && str_starts_with($row['caminho'], 'uploads/')) {
            $path = __DIR__ . '/../' . $row['caminho'];
            if (file_exists($path)) @unlink($path);
        }
        return $ok;
    }
}
?>
