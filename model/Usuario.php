<?php
// model/Usuario.php

class Usuario {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    // Busca os números reais para os cards do topo
    public function obterEstatisticas() {
        $stats = [
            'total' => 0,
            'ativos' => 0,
            'inativos' => 0,
            'admins' => 0
        ];

        $stats['total'] = $this->pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $stats['ativos'] = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'")->fetchColumn();
        $stats['inativos'] = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'inativo'")->fetchColumn();
        
        // CORREÇÃO: Usando o sinal de igual (=) no lugar do ILIKE, já que o banco usa um ENUM
        $stats['admins'] = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'admin'")->fetchColumn();

        return $stats;
    }

    // Busca a lista completa de usuários
    public function listarUsuarios() {
        $sql = "SELECT id_usuario, nome, email, perfil, status FROM usuarios ORDER BY id_usuario ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>