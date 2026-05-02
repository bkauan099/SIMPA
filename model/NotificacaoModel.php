<?php
// model/NotificacaoModel.php
// Gera notificações dinâmicas baseadas em dados do banco

class NotificacaoModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function listarParaAdm() {
        $notifs = [];

        try {
            // Projetos pendentes de aprovação
            $pendProj = $this->pdo->query(
                "SELECT id_projeto, titulo FROM projetos WHERE CAST(status AS TEXT) = 'pendente' ORDER BY id_projeto DESC LIMIT 5"
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pendProj as $p) {
                $notifs[] = [
                    'id'      => 'proj_' . $p['id_projeto'],
                    'tipo'    => 'projeto',
                    'icone'   => 'bi-folder-plus',
                    'cor'     => 'text-warning',
                    'titulo'  => 'Projeto pendente',
                    'texto'   => $p['titulo'],
                    'acao'    => 'projetos',
                ];
            }
        } catch (Exception $e) {}

        try {
            // Documentos aguardando aprovação
            $pendDocs = $this->pdo->query(
                "SELECT pd.id_producao, pd.titulo FROM producoes pd WHERE CAST(pd.status AS TEXT) = 'pendente' ORDER BY pd.id_producao DESC LIMIT 5"
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pendDocs as $d) {
                $notifs[] = [
                    'id'      => 'doc_' . $d['id_producao'],
                    'tipo'    => 'documento',
                    'icone'   => 'bi-file-earmark-check',
                    'cor'     => 'text-info',
                    'titulo'  => 'Documento para revisar',
                    'texto'   => $d['titulo'],
                    'acao'    => 'documentos',
                ];
            }
        } catch (Exception $e) {}

        try {
            // Usuários cadastrados nos últimos 7 dias
            $novosUsuarios = $this->pdo->query(
                "SELECT id_usuario, nome FROM usuarios WHERE created_at >= NOW() - INTERVAL '7 days' ORDER BY id_usuario DESC LIMIT 3"
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($novosUsuarios as $u) {
                $notifs[] = [
                    'id'      => 'usr_' . $u['id_usuario'],
                    'tipo'    => 'usuario',
                    'icone'   => 'bi-person-plus',
                    'cor'     => 'text-success',
                    'titulo'  => 'Novo usuário cadastrado',
                    'texto'   => $u['nome'],
                    'acao'    => 'usuarios',
                ];
            }
        } catch (Exception $e) {
            // created_at pode não existir - tenta sem esse campo
            try {
                $novosUsuarios = $this->pdo->query(
                    "SELECT id_usuario, nome FROM usuarios ORDER BY id_usuario DESC LIMIT 2"
                )->fetchAll(PDO::FETCH_ASSOC);
                foreach ($novosUsuarios as $u) {
                    $notifs[] = [
                        'id'      => 'usr_' . $u['id_usuario'],
                        'tipo'    => 'usuario',
                        'icone'   => 'bi-person-plus',
                        'cor'     => 'text-success',
                        'titulo'  => 'Usuário recente',
                        'texto'   => $u['nome'],
                        'acao'    => 'usuarios',
                    ];
                }
            } catch (Exception $e2) {}
        }

        return $notifs;
    }

    public function totalNaoLidas() {
        $total = 0;
        try {
            $total += (int)$this->pdo->query("SELECT COUNT(*) FROM projetos WHERE CAST(status AS TEXT) = 'pendente'")->fetchColumn();
        } catch (Exception $e) {}
        try {
            $total += (int)$this->pdo->query("SELECT COUNT(*) FROM producoes WHERE CAST(status AS TEXT) = 'pendente'")->fetchColumn();
        } catch (Exception $e) {}
        return $total;
    }
}
?>
