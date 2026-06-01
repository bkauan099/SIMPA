<?php
class NotificacaoModel {
    private $pdo;
    public function __construct($conexao) { $this->pdo = $conexao; }

    public function listarParaAdm() {
        $notifs = [];
        try {
            $rows = $this->pdo->query("SELECT id_projeto, titulo FROM projetos WHERE CAST(status AS TEXT)='pendente' ORDER BY id_projeto DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) $notifs[] = ['id'=>'proj_'.$r['id_projeto'],'tipo'=>'projeto','icone'=>'bi-folder-plus','titulo'=>'Projeto pendente','texto'=>$r['titulo'],'acao'=>'projetos'];
        } catch(Exception $e){}
        try {
            $rows = $this->pdo->query("SELECT id_producao, titulo FROM producoes WHERE CAST(status AS TEXT)='pendente' ORDER BY id_producao DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) $notifs[] = ['id'=>'doc_'.$r['id_producao'],'tipo'=>'documento','icone'=>'bi-file-earmark-check','titulo'=>'Documento para revisar','texto'=>$r['titulo'],'acao'=>'documentos'];
        } catch(Exception $e){}
        return $notifs;
    }

    public function totalNaoLidas() {
        $t = 0;
        try { $t += (int)$this->pdo->query("SELECT COUNT(*) FROM projetos WHERE CAST(status AS TEXT)='pendente'")->fetchColumn(); } catch(Exception $e){}
        try { $t += (int)$this->pdo->query("SELECT COUNT(*) FROM producoes WHERE CAST(status AS TEXT)='pendente'")->fetchColumn(); } catch(Exception $e){}
        return $t;
    }
}
?>
