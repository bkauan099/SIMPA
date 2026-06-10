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
            SELECT
                COUNT(DISTINCT CASE WHEN p.status = 'ativo'    THEN p.id_projeto END) AS ativos,
                COUNT(DISTINCT CASE WHEN p.status = 'pendente' THEN p.id_projeto END) AS aguardando,
                COUNT(DISTINCT CASE WHEN p.status NOT IN ('ativo','pendente') THEN p.id_projeto END) AS concluidos,
                COUNT(DISTINCT CASE WHEN CAST(u.perfil AS TEXT) ILIKE '%aluno%' THEN pa2.id_usuario END) AS alunos
            FROM participacao pa
            JOIN projetos p ON pa.id_projeto = p.id_projeto
            LEFT JOIN participacao pa2 ON pa2.id_projeto = p.id_projeto
            LEFT JOIN usuarios u ON pa2.id_usuario = u.id_usuario
            WHERE pa.id_usuario = ?
        ");
        $stmt->execute([$id_professor]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'ativos'    => (int)($row['ativos']    ?? 0),
            'aguardando'=> (int)($row['aguardando'] ?? 0),
            'concluidos'=> (int)($row['concluidos'] ?? 0),
            'alunos'    => (int)($row['alunos']     ?? 0),
        ];
    }

    public function listarProjetosPorProfessor($id_professor) {
        $stmt = $this->pdo->prepare("
            SELECT
                p.*,
                tp.nome AS tipo_nome,
                pa.carga_horaria,
                (SELECT COUNT(pa2.id_participacao)
                 FROM participacao pa2
                 WHERE pa2.id_projeto = p.id_projeto
                   AND pa2.id_usuario <> ?) AS total_participantes
            FROM projetos p
            JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            JOIN participacao pa ON p.id_projeto = pa.id_projeto
            WHERE pa.id_usuario = ?
            ORDER BY p.id_projeto ASC
        ");
        $stmt->execute([$id_professor, $id_professor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarParticipantes($id_projeto) {
        $stmt = $this->pdo->prepare("
            SELECT u.id_usuario, u.nome, u.email, u.matricula, u.curso, u.status, pa.carga_horaria
            FROM participacao pa
            JOIN usuarios u ON pa.id_usuario = u.id_usuario
            WHERE pa.id_projeto = ?
            ORDER BY u.nome ASC
        ");
        $stmt->execute([$id_projeto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterDadosGrafico($id_professor) {
        $stmt = $this->pdo->prepare("
            SELECT tp.nome, COUNT(p.id_projeto) AS total
            FROM tipo_projetos tp
            JOIN projetos p ON p.id_tipo = tp.id_tipo
            JOIN participacao pa ON p.id_projeto = pa.id_projeto
            WHERE pa.id_usuario = ?
            GROUP BY tp.nome
            HAVING COUNT(p.id_projeto) > 0
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

    public function cadastrar($dados, $id_usuario = null, $funcao = 'Orientador')
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO projetos (titulo, id_tipo, area, descricao, data_inicio, data_fim, status)
                    VALUES (:titulo, :id_tipo, :area, :descricao, :data_inicio, :data_fim, 'pendente')";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titulo'      => $dados['titulo'],
                ':id_tipo'     => $dados['id_tipo'],
                ':area'        => $dados['area'],
                ':descricao'   => $dados['descricao'],
                ':data_inicio' => !empty($dados['data_inicio']) ? $dados['data_inicio'] : null,
                ':data_fim'    => !empty($dados['data_fim']) ? $dados['data_fim'] : null,
            ]);

            $id_projeto = $this->pdo->lastInsertId();

            if ($id_usuario && $id_projeto) {
                $carga = !empty($dados['carga_horaria']) ? $dados['carga_horaria'] : 0;
                $stmt_vinculo = $this->pdo->prepare(
                    "INSERT INTO participacao (id_projeto, id_usuario, funcao, carga_horaria) VALUES (?, ?, ?, ?)"
                );
                $stmt_vinculo->execute([$id_projeto, $id_usuario, $funcao, $carga]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    public function editar($id_projeto, $dados)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE projetos SET
                        titulo = :titulo, id_tipo = :id_tipo, area = :area,
                        descricao = :descricao, data_inicio = :data_inicio, data_fim = :data_fim
                    WHERE id_projeto = :id_projeto";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titulo'      => $dados['titulo'],
                ':id_tipo'     => $dados['id_tipo'],
                ':area'        => $dados['area'],
                ':descricao'   => $dados['descricao'],
                ':data_inicio' => $dados['data_inicio'],
                ':data_fim'    => $dados['data_fim'],
                ':id_projeto'  => $id_projeto,
            ]);

            $stmt_pa = $this->pdo->prepare(
                "UPDATE participacao SET carga_horaria = ? WHERE id_projeto = ? AND funcao ILIKE '%Orientador%'"
            );
            $stmt_pa->execute([$dados['carga_horaria'], $id_projeto]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
?>