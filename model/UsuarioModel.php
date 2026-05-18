<?php
// model/UsuarioModel.php  (substitui model/Usuario.php com CRUD completo)

class UsuarioModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas() {
        $stats = [
            'total'   => 0,
            'ativos'  => 0,
            'inativos'=> 0,
            'admins'  => 0,
        ];

        $stats['total']    = $this->pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $stats['ativos']   = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'")->fetchColumn();
        $stats['inativos'] = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'inativo'")->fetchColumn();
        $stats['admins']   = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'admin'")->fetchColumn();

        return $stats;
    }

    public function listarTodos() {
        $sql = "SELECT id_usuario, nome, email, matricula, perfil, curso, status, cadastrado_por FROM usuarios ORDER BY id_usuario ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $sql = "
            INSERT INTO usuarios (nome, email, senha, matricula, perfil, curso, status, cadastrado_por)
            VALUES (:nome, :email, :senha, :matricula, :perfil, :curso, :status, :cadastrado_por)
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome'          => $dados['nome'],
            ':email'         => $dados['email'],
            ':senha'         => password_hash($dados['senha'], PASSWORD_DEFAULT),
            ':matricula'     => $dados['matricula'] ?? null,
            ':perfil'        => $dados['perfil'],
            ':curso'         => $dados['curso'] ?? null,
            ':status'        => $dados['status'] ?? 'ativo',
            ':cadastrado_por'=> $dados['cadastrado_por'] ?? null,
        ]);
    }

    public function atualizar($id, $dados) {
        $sql = "
            UPDATE usuarios
            SET nome = :nome, email = :email, matricula = :matricula,
                perfil = :perfil, curso = :curso, status = :status
            WHERE id_usuario = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'        => $id,
            ':nome'      => $dados['nome'],
            ':email'     => $dados['email'],
            ':matricula' => $dados['matricula'] ?? null,
            ':perfil'    => $dados['perfil'],
            ':curso'     => $dados['curso'] ?? null,
            ':status'    => $dados['status'],
        ]);
    }

    public function alterarStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET status = :status WHERE id_usuario = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function redefinirSenha($id, $novaSenha) {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id_usuario = :id");
        return $stmt->execute([':senha' => $hash, ':id' => $id]);
    }

    public function listarAtivos() {
        $stmt = $this->pdo->query("SELECT id_usuario, nome, email, perfil FROM usuarios WHERE status = 'ativo' ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
