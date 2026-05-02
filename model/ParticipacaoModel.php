<?php
// model/ParticipacaoModel.php

class ParticipacaoModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas() {
        $stats = [
            'total'                     => 0,
            'ativos'                    => 0,
            'encerrados'                => 0,
            'projetos_com_participantes'=> 0,
        ];
        $stats['total']      = $this->pdo->query("SELECT COUNT(*) FROM participacao")->fetchColumn();
        $stats['ativos']     = $this->pdo->query("SELECT COUNT(*) FROM participacao WHERE CAST(status AS TEXT) = 'ativo'")->fetchColumn();
        $stats['encerrados'] = $this->pdo->query("SELECT COUNT(*) FROM participacao WHERE CAST(status AS TEXT) = 'inativo'")->fetchColumn();
        $stats['projetos_com_participantes'] = $this->pdo->query(
            "SELECT COUNT(DISTINCT id_projeto) FROM participacao"
        )->fetchColumn();
        return $stats;
    }

    public function listarTodas() {
        $sql = "
            SELECT
                pa.id_participacao,
                pa.funcao,
                pa.carga_horaria,
                pa.data_entrada,
                pa.data_saida,
                CAST(pa.status AS TEXT) AS status,
                u.nome                  AS usuario_nome,
                u.email                 AS usuario_email,
                CAST(u.perfil AS TEXT)  AS usuario_perfil,
                p.titulo                AS projeto_titulo,
                p.id_projeto
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            JOIN projetos  p ON pa.id_projeto = p.id_projeto
            ORDER BY pa.id_participacao ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "
            SELECT pa.*, u.nome AS usuario_nome, p.titulo AS projeto_titulo
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            JOIN projetos  p ON pa.id_projeto = p.id_projeto
            WHERE pa.id_participacao = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $sql = "
            INSERT INTO participacao (id_projeto, id_usuario, funcao, carga_horaria, data_entrada, data_saida, status)
            VALUES (:id_projeto, :id_usuario, :funcao, :carga_horaria, :data_entrada, :data_saida, :status)
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_projeto'    => $dados['id_projeto'],
            ':id_usuario'    => $dados['id_usuario'],
            ':funcao'        => $dados['funcao'],
            ':carga_horaria' => $dados['carga_horaria'] ?: null,
            ':data_entrada'  => $dados['data_entrada'],
            ':data_saida'    => $dados['data_saida'] ?? null,
            ':status'        => $dados['status'] ?? 'ativo',
        ]);
    }

    public function atualizar($id, $dados) {
        $sql = "
            UPDATE participacao
            SET id_projeto = :id_projeto, id_usuario = :id_usuario, funcao = :funcao,
                carga_horaria = :carga_horaria, data_entrada = :data_entrada,
                data_saida = :data_saida, status = :status
            WHERE id_participacao = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'            => $id,
            ':id_projeto'    => $dados['id_projeto'],
            ':id_usuario'    => $dados['id_usuario'],
            ':funcao'        => $dados['funcao'],
            ':carga_horaria' => $dados['carga_horaria'] ?: null,
            ':data_entrada'  => $dados['data_entrada'],
            ':data_saida'    => $dados['data_saida'] ?? null,
            ':status'        => $dados['status'],
        ]);
    }

    public function alterarStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE participacao SET status = :status WHERE id_participacao = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function excluir($id) {
        $stmt = $this->pdo->prepare("DELETE FROM participacao WHERE id_participacao = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function listarPorProjeto($id_projeto) {
        $sql = "
            SELECT
                pa.id_participacao,
                pa.funcao,
                pa.carga_horaria,
                pa.data_entrada,
                pa.data_saida,
                CAST(pa.status AS TEXT) AS status,
                u.nome                  AS usuario_nome,
                u.email                 AS usuario_email,
                CAST(u.perfil AS TEXT)  AS usuario_perfil,
                u.matricula             AS usuario_matricula,
                u.curso                 AS usuario_curso
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            WHERE pa.id_projeto = :id_projeto
            ORDER BY pa.funcao ASC, u.nome ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_projeto' => (int)$id_projeto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
