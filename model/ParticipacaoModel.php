<?php
class ParticipacaoModel {
    private $pdo;

    public function __construct($conexao) { $this->pdo = $conexao; }

    public function obterEstatisticas() {
        return [
            'total'                      => (int)$this->pdo->query("SELECT COUNT(*) FROM participacao")->fetchColumn(),
            'ativos'                     => (int)$this->pdo->query("SELECT COUNT(*) FROM participacao WHERE CAST(status AS TEXT)='ativo'")->fetchColumn(),
            'encerrados'                 => (int)$this->pdo->query("SELECT COUNT(*) FROM participacao WHERE CAST(status AS TEXT)='inativo'")->fetchColumn(),
            'projetos_com_participantes' => (int)$this->pdo->query("SELECT COUNT(DISTINCT id_projeto) FROM participacao")->fetchColumn(),
        ];
    }

    public function listarTodas() {
        $sql = "
            SELECT pa.id_participacao, pa.funcao, pa.carga_horaria,
                   pa.data_entrada, pa.data_saida, CAST(pa.status AS TEXT) AS status,
                   u.nome AS usuario_nome, u.email AS usuario_email,
                   CAST(u.perfil AS TEXT) AS usuario_perfil,
                   p.titulo AS projeto_titulo, p.id_projeto
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            JOIN projetos  p ON pa.id_projeto = p.id_projeto
            ORDER BY p.titulo ASC, pa.funcao ASC, u.nome ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Resumo de cada projeto com orientador e contagem de membros */
    public function listarProjetosComEquipe() {
        $sql = "
            SELECT
                p.id_projeto, p.titulo, CAST(p.status AS TEXT) AS status,
                p.data_inicio, p.data_fim,
                COUNT(pa.id_participacao) AS total_membros,
                COUNT(pa.id_participacao) FILTER (WHERE CAST(pa.status AS TEXT)='ativo') AS membros_ativos,
                (SELECT u2.nome FROM participacao pa2
                 JOIN usuarios u2 ON pa2.id_usuario = u2.id_usuario
                 WHERE pa2.id_projeto = p.id_projeto
                   AND (CAST(u2.perfil AS TEXT) ILIKE '%professor%' OR CAST(pa2.funcao AS TEXT) ILIKE '%orientador%')
                   AND CAST(pa2.status AS TEXT)='ativo'
                 LIMIT 1) AS orientador
            FROM projetos p
            LEFT JOIN participacao pa ON pa.id_projeto = p.id_projeto
            GROUP BY p.id_projeto, p.titulo, p.status, p.data_inicio, p.data_fim
            ORDER BY p.titulo ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Membros detalhados de um projeto específico */
    public function listarPorProjeto($id_projeto) {
        $sql = "
            SELECT pa.id_participacao, pa.funcao, pa.carga_horaria,
                   pa.data_entrada, pa.data_saida, CAST(pa.status AS TEXT) AS status,
                   u.nome AS usuario_nome, u.email AS usuario_email,
                   CAST(u.perfil AS TEXT) AS usuario_perfil,
                   u.matricula, u.curso
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            WHERE pa.id_projeto = :id
            ORDER BY
                CASE CAST(u.perfil AS TEXT)
                    WHEN 'admin' THEN 1
                    WHEN 'professor_orientador' THEN 2
                    ELSE 3
                END,
                u.nome ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int)$id_projeto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        // Impedir duplicata ativa
        $chk = $this->pdo->prepare(
            "SELECT id_participacao FROM participacao WHERE id_projeto=:p AND id_usuario=:u AND CAST(status AS TEXT)='ativo'"
        );
        $chk->execute([':p' => $dados['id_projeto'], ':u' => $dados['id_usuario']]);
        if ($chk->fetch()) return ['ok' => false, 'msg' => 'Este usuário já está vinculado ativamente a este projeto.'];

        $stmt = $this->pdo->prepare("
            INSERT INTO participacao (id_projeto, id_usuario, funcao, carga_horaria, data_entrada, data_saida, status)
            VALUES (:id_projeto, :id_usuario, :funcao, :carga_horaria, :data_entrada, :data_saida, :status)
        ");
        $ok = $stmt->execute([
            ':id_projeto'    => (int)$dados['id_projeto'],
            ':id_usuario'    => (int)$dados['id_usuario'],
            ':funcao'        => $dados['funcao'],
            ':carga_horaria' => $dados['carga_horaria'] ?: null,
            ':data_entrada'  => $dados['data_entrada'],
            ':data_saida'    => $dados['data_saida'] ?: null,
            ':status'        => $dados['status'] ?? 'ativo',
        ]);
        return ['ok' => $ok, 'msg' => $ok ? 'Participação criada!' : 'Erro ao criar.'];
    }

    public function atualizar($id, $dados) {
        $stmt = $this->pdo->prepare("
            UPDATE participacao SET funcao=:funcao, carga_horaria=:carga_horaria,
            data_entrada=:data_entrada, data_saida=:data_saida, status=:status
            WHERE id_participacao=:id
        ");
        return $stmt->execute([
            ':id'            => (int)$id,
            ':funcao'        => $dados['funcao'],
            ':carga_horaria' => $dados['carga_horaria'] ?: null,
            ':data_entrada'  => $dados['data_entrada'],
            ':data_saida'    => $dados['data_saida'] ?: null,
            ':status'        => $dados['status'],
        ]);
    }

    public function alterarStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE participacao SET status=:s WHERE id_participacao=:id");
        return $stmt->execute([':s' => $status, ':id' => (int)$id]);
    }

    public function excluir($id) {
        $stmt = $this->pdo->prepare("DELETE FROM participacao WHERE id_participacao=:id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
?>
