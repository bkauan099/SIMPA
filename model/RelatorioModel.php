<?php
// model/RelatorioModel.php

class RelatorioModel {
    private $pdo;

    private function count($sql) {
        try { return (int)$this->pdo->query($sql)->fetchColumn(); }
        catch (Exception $e) { return 0; }
    }

    private function fetch($sql) {
        try { return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC); }
        catch (Exception $e) { return []; }
    }

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function resumoGeral() {
        return [
            'total_projetos'      => $this->count("SELECT COUNT(*) FROM projetos"),
            'projetos_ativos'     => $this->count("SELECT COUNT(*) FROM projetos WHERE CAST(status AS TEXT) = 'ativo'"),
            'projetos_concluidos' => $this->count("SELECT COUNT(*) FROM projetos WHERE CAST(status AS TEXT) = 'concluido'"),
            'total_usuarios'      => $this->count("SELECT COUNT(*) FROM usuarios"),
            'usuarios_ativos'     => $this->count("SELECT COUNT(*) FROM usuarios WHERE CAST(status AS TEXT) = 'ativo'"),
            'total_participacoes' => $this->count("SELECT COUNT(*) FROM participacao"),
            'total_producoes'     => $this->count("SELECT COUNT(*) FROM producoes"),
            'producoes_aprovadas' => $this->count("SELECT COUNT(*) FROM producoes WHERE CAST(status AS TEXT) = 'ativo'"),
            'total_acessos'       => $this->count("SELECT COUNT(*) FROM acessos"),
        ];
    }

    public function projetosPorStatus() {
        return $this->fetch("SELECT CAST(status AS TEXT) AS status, COUNT(*) AS total FROM projetos GROUP BY status ORDER BY total DESC");
    }

    public function projetosPorTipo() {
        return $this->fetch("
            SELECT COALESCE(tp.nome, 'Sem tipo') AS tipo, COUNT(p.id_projeto) AS total
            FROM tipo_projetos tp
            LEFT JOIN projetos p ON p.id_tipo = tp.id_tipo
            GROUP BY tp.nome ORDER BY total DESC
        ");
    }

    public function usuariosPorPerfil() {
        return $this->fetch("SELECT CAST(perfil AS TEXT) AS perfil, COUNT(*) AS total FROM usuarios GROUP BY perfil ORDER BY total DESC");
    }

    public function topProjetosPorParticipantes($limite = 10) {
        return $this->fetch("
            SELECT p.titulo, COUNT(pa.id_participacao) AS total_participantes, CAST(p.status AS TEXT) AS status
            FROM projetos p
            LEFT JOIN participacao pa ON pa.id_projeto = p.id_projeto
            GROUP BY p.id_projeto, p.titulo, p.status
            ORDER BY total_participantes DESC
            LIMIT " . (int)$limite);
    }

    public function producoesPorTipo() {
        return $this->fetch("SELECT COALESCE(tipo, 'outro') AS tipo, COUNT(*) AS total FROM producoes GROUP BY tipo ORDER BY total DESC");
    }

    public function acessosPorMes() {
        return $this->fetch("
            SELECT
                TO_CHAR(data, 'MM/YYYY') AS mes,
                COUNT(*) FILTER (WHERE CAST(status AS TEXT) ILIKE '%sucesso%') AS sucesso,
                COUNT(*) FILTER (WHERE CAST(status AS TEXT) NOT ILIKE '%sucesso%') AS falha
            FROM acessos
            WHERE data >= NOW() - INTERVAL '6 months'
            GROUP BY TO_CHAR(data, 'MM/YYYY'), DATE_TRUNC('month', data)
            ORDER BY DATE_TRUNC('month', data) ASC
        ");
    }

    public function participacoesPorFuncao() {
        return $this->fetch("SELECT funcao, COUNT(*) AS total FROM participacao GROUP BY funcao ORDER BY total DESC LIMIT 10");
    }

    public function projetosPorMes() {
        return $this->fetch("
            SELECT
                TO_CHAR(data_inicio, 'MM/YYYY') AS mes,
                COUNT(*) AS total
            FROM projetos
            WHERE data_inicio IS NOT NULL
              AND data_inicio >= NOW() - INTERVAL '12 months'
            GROUP BY TO_CHAR(data_inicio, 'MM/YYYY'), DATE_TRUNC('month', data_inicio)
            ORDER BY DATE_TRUNC('month', data_inicio) ASC
        ");
    }
}
?>
