<?php
class Usuario
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Busca alunos para o dropdown de sugestões (Filtro por letras)
    public function buscarAlunosPorNome($termo)
    {
        // Usamos "$termo%" para buscar quem começa com aquela(s) letra(s)
        // Isso aproveita melhor os índices do PostgreSQL
        $sql = "SELECT id_usuario, nome, matricula, curso 
            FROM usuarios 
            WHERE nome ILIKE ? AND perfil = 'aluno' 
            ORDER BY nome ASC 
            LIMIT 6";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["$termo%"]); // O sinal de % apenas no final
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vincula um aluno ao projeto (Tabela participacao)
    public function vincularAoProjeto($id_usuario, $id_projeto, $carga_horaria)
    {
        // 1. Verificar se o aluno já está vinculado a este projeto específico
        $sqlCheck = "SELECT 1 FROM participacao WHERE id_usuario = ? AND id_projeto = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id_usuario, $id_projeto]);

        if ($stmtCheck->fetch()) {
            // Retorna um código de erro específico para o Controller tratar
            return ['sucesso' => false, 'erro' => 'duplicado'];
        }

        // 2. Se não existir, realiza o cadastro
        $sql = "INSERT INTO participacao (id_usuario, id_projeto, carga_horaria, data_entrada, funcao) 
            VALUES (?, ?, ?, CURRENT_DATE, 'Bolsista')";

        $stmt = $this->pdo->prepare($sql);
        $sucesso = $stmt->execute([$id_usuario, $id_projeto, (int)$carga_horaria]);

        return ['sucesso' => $sucesso];
    }

    // Remove um aluno do projeto
    public function removerDoProjeto($id_usuario, $id_projeto)
    {
        $sql = "DELETE FROM participacao WHERE id_usuario = ? AND id_projeto = ? AND funcao <> 'Orientador'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $id_projeto]);
    }
}
