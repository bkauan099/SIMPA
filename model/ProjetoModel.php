<?php
// model/ProjetoModel.php

class ProjetoModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas() {
        $stats = [
            'total'     => 0,
            'ativos'    => 0,
            'pendentes' => 0,
            'concluidos'=> 0,
        ];

        $stats['total']      = $this->pdo->query("SELECT COUNT(*) FROM projetos")->fetchColumn();
        $stats['ativos']     = $this->pdo->query("SELECT COUNT(*) FROM projetos WHERE status = 'ativo'")->fetchColumn();
        $stats['pendentes']  = $this->pdo->query("SELECT COUNT(*) FROM projetos WHERE status = 'pendente'")->fetchColumn();
        $stats['concluidos'] = $this->pdo->query("SELECT COUNT(*) FROM projetos WHERE status = 'concluido'")->fetchColumn();

        return $stats;
    }

    public function listarTodos() {
        $sql = "
            SELECT
                p.id_projeto,
                p.titulo,
                p.area,
                p.status,
                p.data_inicio,
                p.data_fim,
                tp.nome AS tipo_nome,
                (
                    SELECT u.nome
                    FROM participacao pa
                    JOIN usuarios u ON pa.id_usuario = u.id_usuario
                    WHERE pa.id_projeto = p.id_projeto
                      AND pa.funcao ILIKE '%Orientador%'
                    LIMIT 1
                ) AS orientador,
                (
                    SELECT COUNT(*)
                    FROM participacao pa2
                    WHERE pa2.id_projeto = p.id_projeto
                ) AS total_participantes
            FROM projetos p
            LEFT JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            ORDER BY p.id_projeto ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "
            SELECT p.*, tp.nome AS tipo_nome
            FROM projetos p
            LEFT JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            WHERE p.id_projeto = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTipos() {
        $stmt = $this->pdo->query("SELECT id_tipo, nome FROM tipo_projetos ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $sql = "
            INSERT INTO projetos (id_tipo, titulo, area, descricao, data_inicio, data_fim, status)
            VALUES (:id_tipo, :titulo, :area, :descricao, :data_inicio, :data_fim, :status)
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_tipo'     => $dados['id_tipo'],
            ':titulo'      => $dados['titulo'],
            ':area'        => $dados['area'],
            ':descricao'   => $dados['descricao'],
            ':data_inicio' => $dados['data_inicio'],
            ':data_fim'    => $dados['data_fim'],
            ':status'      => $dados['status'] ?? 'pendente',
        ]);
    }

    public function atualizar($id, $dados) {
        $sql = "
            UPDATE projetos
            SET id_tipo = :id_tipo, titulo = :titulo, area = :area,
                descricao = :descricao, data_inicio = :data_inicio,
                data_fim = :data_fim, status = :status
            WHERE id_projeto = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'          => $id,
            ':id_tipo'     => $dados['id_tipo'],
            ':titulo'      => $dados['titulo'],
            ':area'        => $dados['area'],
            ':descricao'   => $dados['descricao'],
            ':data_inicio' => $dados['data_inicio'],
            ':data_fim'    => $dados['data_fim'],
            ':status'      => $dados['status'],
        ]);
    }

    public function alterarStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE projetos SET status = :status WHERE id_projeto = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
?>
