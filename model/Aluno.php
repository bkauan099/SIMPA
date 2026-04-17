<?php

class Aluno {

    // Aqui guardamos a conexão com o banco de dados
    private $pdo;

    // Esse método roda automaticamente quando criamos um objeto Aluno
    // Recebe a conexão e guarda ela para usar nas consultas abaixo
    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    // Busca a carga horária total de um aluno somando todas as participações dele
    public function obterCargaHorariaTotal($id_usuario) {
        $sql = "SELECT COALESCE(SUM(carga_horaria), 0) FROM participacao WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    // Busca o projeto ativo do aluno (para exibir no cabeçalho de tarefas)
    public function obterProjetoAtivo($id_usuario) {
        $sql = "
            SELECT p.titulo
            FROM projetos p
            JOIN participacao pa ON p.id_projeto = pa.id_projeto
            WHERE pa.id_usuario = :id AND pa.status = 'ativo'
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    public function obterAgenda($id_usuario) {
        $sql = "
            SELECT titulo, data, hora, tipo
            FROM agenda_items
            WHERE id_usuario = :id
            ORDER BY data ASC
            LIMIT 50
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tarefas = [];
        $eventos = [];
        foreach ($itens as $item) {
            if ($item['tipo'] === 'tarefa') $tarefas[] = $item;
            else                            $eventos[] = $item;
        }
        return ['tarefas' => $tarefas, 'eventos' => $eventos];
    }
}
?>
