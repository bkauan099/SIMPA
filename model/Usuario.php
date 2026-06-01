<?php
class Usuario
{
    private $pdo;

    public function __construct($conexao)
    {
        $this->pdo = $conexao;
    }

    // ── ADMIN ─────────────────────────────────────────────────────────────────

    public function obterEstatisticas()
    {
        $stats = ['total' => 0, 'ativos' => 0, 'inativos' => 0, 'admins' => 0];

        $stats['total']    = $this->pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $stats['ativos']   = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'")->fetchColumn();
        $stats['inativos'] = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'inativo'")->fetchColumn();
        $stats['admins']   = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'admin'")->fetchColumn();

        return $stats;
    }

    public function listarUsuarios()
    {
        $sql = "SELECT id_usuario, nome, email, perfil, status FROM usuarios ORDER BY id_usuario ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── PROFESSOR ─────────────────────────────────────────────────────────────

    public function buscarAlunosPorNome($termo)
    {
        $sql = "SELECT id_usuario, nome, matricula, curso
                FROM usuarios
                WHERE unaccent(nome) ILIKE unaccent(?) AND perfil = 'aluno'
                ORDER BY nome ASC
                LIMIT 6";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["$termo%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function vincularAoProjeto($id_usuario, $id_projeto, $carga_horaria)
    {
        $sqlCheck = "SELECT 1 FROM participacao WHERE id_usuario = ? AND id_projeto = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id_usuario, $id_projeto]);

        if ($stmtCheck->fetch()) {
            return ['sucesso' => false, 'erro' => 'duplicado'];
        }

        $sql = "INSERT INTO participacao (id_usuario, id_projeto, carga_horaria, data_entrada, funcao)
                VALUES (?, ?, ?, CURRENT_DATE, 'Bolsista')";
        $stmt = $this->pdo->prepare($sql);
        $sucesso = $stmt->execute([$id_usuario, $id_projeto, (int)$carga_horaria]);

        return ['sucesso' => $sucesso];
    }

    public function removerDoProjeto($id_usuario, $id_projeto)
    {
        $sql = "DELETE FROM participacao WHERE id_usuario = ? AND id_projeto = ? AND funcao <> 'Orientador'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $id_projeto]);
    }
}
