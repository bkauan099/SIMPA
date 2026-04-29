<?php
// model/Projeto.php

class Projeto
{
    private $pdo;

    public function __construct($conexao)
    {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas()
    {
        $stats = ['projetos_ativos' => 0, 'total_usuarios' => 0, 'pendencias' => 8, 'notificacoes' => 3];

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM projetos WHERE status = 'ativo'");
        $stats['projetos_ativos'] = $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM usuarios");
        $stats['total_usuarios'] = $stmt->fetchColumn();

        return $stats;
    }

    public function listarProjetosAtivos()
    {
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

    public function obterEstatisticasProfessor($id_prof)
    {
        $stats = ['ativos' => 0, 'aguardando' => 0, 'alunos' => 0, 'concluidos' => 0];

        // 1. Projetos Ativos
        $sqlAtivos = "SELECT COUNT(DISTINCT p.id_projeto) FROM projetos p 
                      JOIN participacao pa ON p.id_projeto = pa.id_projeto 
                      WHERE pa.id_usuario = ? AND p.status = 'ativo'";
        $stmt = $this->pdo->prepare($sqlAtivos);
        $stmt->execute([$id_prof]);
        $stats['ativos'] = $stmt->fetchColumn();

        // 2. Aguardando Aprovação (Status 'pendente')
        $sqlAguard = "SELECT COUNT(DISTINCT p.id_projeto) FROM projetos p 
                      JOIN participacao pa ON p.id_projeto = pa.id_projeto 
                      WHERE pa.id_usuario = ? AND p.status = 'pendente'";
        $stmt = $this->pdo->prepare($sqlAguard);
        $stmt->execute([$id_prof]);
        $stats['aguardando'] = $stmt->fetchColumn();

        // 3. Alunos no Total (Contar participantes que não são o próprio professor)
        $sqlAlunos = "SELECT COUNT(DISTINCT pa2.id_usuario) 
                      FROM participacao pa1
                      JOIN participacao pa2 ON pa1.id_projeto = pa2.id_projeto
                      WHERE pa1.id_usuario = ? AND pa2.id_usuario <> pa1.id_usuario";
        $stmt = $this->pdo->prepare($sqlAlunos);
        $stmt->execute([$id_prof]);
        $stats['alunos'] = $stmt->fetchColumn();

        // 4. Projetos Concluídos
        $sqlConcluidos = "SELECT COUNT(DISTINCT p.id_projeto) FROM projetos p 
                          JOIN participacao pa ON p.id_projeto = pa.id_projeto 
                          WHERE pa.id_usuario = ? AND p.status = 'concluido'";
        $stmt = $this->pdo->prepare($sqlConcluidos);
        $stmt->execute([$id_prof]);
        $stats['concluidos'] = $stmt->fetchColumn();

        return $stats;
    }

    public function obterDadosGrafico($id_prof)
    {
        $sql = "SELECT tp.nome, COUNT(p.id_projeto) as total 
                    FROM tipo_projetos tp
                    JOIN projetos p ON tp.id_tipo = p.id_tipo
                    JOIN participacao pa ON p.id_projeto = pa.id_projeto
                    WHERE pa.id_usuario = ? 
                    AND p.status = 'ativo'
                    GROUP BY tp.nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_prof]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * MÉTODO CADASTRAR ATUALIZADO
     * Agora sincroniza a carga horária na tabela participação
     */
    public function cadastrar($dados, $id_usuario = null, $funcao = 'Orientador')
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Insere o projeto base na tabela projetos
            $sql = "INSERT INTO projetos (titulo, id_tipo, area, descricao, data_inicio, data_fim, status) 
                    VALUES (:titulo, :id_tipo, :area, :descricao, :data_inicio, :data_fim, 'pendente')";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titulo'      => $dados['titulo'],
                ':id_tipo'     => $dados['id_tipo'],
                ':area'        => $dados['area'],
                ':descricao'   => $dados['descricao'],
                ':data_inicio' => !empty($dados['data_inicio']) ? $dados['data_inicio'] : null,
                ':data_fim'    => !empty($dados['data_fim']) ? $dados['data_fim'] : null
            ]);

            // Recupera o ID do projeto que acabou de ser criado no Postgres/Supabase
            $id_projeto = $this->pdo->lastInsertId();

            // 2. Cria o vínculo na tabela participação incluindo a CARGA HORÁRIA
            if ($id_usuario && $id_projeto) {
                $sql_vinculo = "INSERT INTO participacao (id_projeto, id_usuario, funcao, carga_horaria) VALUES (?, ?, ?, ?)";
                $stmt_vinculo = $this->pdo->prepare($sql_vinculo);

                // Captura a carga horária vinda do formulário (name="carga_horaria")
                $carga = !empty($dados['carga_horaria']) ? $dados['carga_horaria'] : 0;

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

    public function listarProjetosPorProfessor($id_prof)
    {
        $sql = "SELECT 
                p.*, 
                tp.nome as tipo_nome,
                pa.carga_horaria, 
                (SELECT COUNT(pa2.id_participacao) 
                 FROM participacao pa2 
                 WHERE pa2.id_projeto = p.id_projeto 
                 AND pa2.id_usuario <> ?) as total_participantes
            FROM projetos p
            JOIN tipo_projetos tp ON p.id_tipo = tp.id_tipo
            JOIN participacao pa ON p.id_projeto = pa.id_projeto
            WHERE pa.id_usuario = ?
            ORDER BY p.id_projeto ASC"; // Alterado de DESC para ASC

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_prof, $id_prof]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editar($id_projeto, $dados)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Atualiza os dados principais do projeto
            $sql = "UPDATE projetos SET 
                    titulo = :titulo, 
                    id_tipo = :id_tipo, 
                    area = :area, 
                    descricao = :descricao, 
                    data_inicio = :data_inicio, 
                    data_fim = :data_fim 
                WHERE id_projeto = :id_projeto";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titulo'      => $dados['titulo'],
                ':id_tipo'     => $dados['id_tipo'],
                ':area'        => $dados['area'],
                ':descricao'   => $dados['descricao'],
                ':data_inicio' => $dados['data_inicio'],
                ':data_fim'    => $dados['data_fim'],
                ':id_projeto'  => $id_projeto
            ]);

            // 2. Atualiza a carga horária na tabela participação (do orientador logado)
            $sql_pa = "UPDATE participacao SET carga_horaria = ? WHERE id_projeto = ? AND funcao ILIKE '%Orientador%'";
            $stmt_pa = $this->pdo->prepare($sql_pa);
            $stmt_pa->execute([$dados['carga_horaria'], $id_projeto]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    public function listarParticipantes($id_projeto)
    {
        // Adicionamos 'u.status' na consulta
        $sql = "SELECT u.id_usuario, u.nome, u.matricula, u.curso, u.status, p.carga_horaria 
            FROM participacao p 
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.id_projeto = ? 
            AND u.perfil = 'aluno' 
            ORDER BY u.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_projeto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
