<?php
// model/model-professor/Projeto.php

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
            $stats = ['ativos' => 0, 'alunos' => 0, 'pendencias' => 5, 'vencendo' => 3];

            // Projetos Ativos: Conta qualquer projeto vinculado ao ID na tabela participacao
            $sqlAtivos = "SELECT COUNT(DISTINCT p.id_projeto) 
                        FROM projetos p 
                        JOIN participacao pa ON p.id_projeto = pa.id_projeto 
                        WHERE pa.id_usuario = ? 
                        AND p.status = 'ativo'";
            $stmt = $this->pdo->prepare($sqlAtivos);
            $stmt->execute([$id_prof]);
            $stats['ativos'] = $stmt->fetchColumn();

            // Alunos: Conta todos os outros participantes dos projetos que este ID participa
            $sqlAlunos = "SELECT COUNT(DISTINCT pa2.id_usuario) 
                        FROM participacao pa1
                        JOIN participacao pa2 ON pa1.id_projeto = pa2.id_projeto
                        WHERE pa1.id_usuario = ? 
                        AND pa2.id_usuario <> pa1.id_usuario";
            $stmt = $this->pdo->prepare($sqlAlunos);
            $stmt->execute([$id_prof]);
            $stats['alunos'] = $stmt->fetchColumn();

            return $stats;
        }

        public function obterDadosGrafico($id_prof)
        {
            // Gráfico: Agrupa por tipo, filtrando apenas pelo ID do usuário vinculado
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
    }
?>