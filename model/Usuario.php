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
        // Força a carga horária a ser um número para o banco não dar erro
        $ch = (int)$carga_horaria;

        $sql = "INSERT INTO participacao (id_usuario, id_projeto, carga_horaria, data_entrada, funcao) 
            VALUES (?, ?, ?, CURRENT_DATE, 'Bolsista')";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $id_projeto, $ch]);
    }

    // Remove um aluno do projeto
    public function removerDoProjeto($id_usuario, $id_projeto)
    {
        $sql = "DELETE FROM participacao WHERE id_usuario = ? AND id_projeto = ? AND funcao <> 'Orientador'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $id_projeto]);
    }
}
