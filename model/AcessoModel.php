<?php
// model/AcessoModel.php
// NOTA: usa CAST(status AS TEXT) em todas as comparações para evitar
// erro do Supabase com o tipo ENUM status_acesso

class AcessoModel {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function obterEstatisticas($dias = 30) {
        $intervalo  = (int)$dias;
        $filtroData = $intervalo > 0 ? "AND data >= NOW() - INTERVAL '{$intervalo} days'" : '';

        $total   = 0; $sucesso = 0; $falha = 0; $unicos = 0;
        try {
            $total = (int)$this->pdo->query(
                "SELECT COUNT(*) FROM acessos WHERE 1=1 $filtroData"
            )->fetchColumn();

            $sucesso = (int)$this->pdo->query(
                "SELECT COUNT(*) FROM acessos WHERE CAST(status AS TEXT) ILIKE '%sucesso%' $filtroData"
            )->fetchColumn();

            $falha = (int)$this->pdo->query(
                "SELECT COUNT(*) FROM acessos WHERE CAST(status AS TEXT) NOT ILIKE '%sucesso%' $filtroData"
            )->fetchColumn();

            $unicos = (int)$this->pdo->query(
                "SELECT COUNT(DISTINCT id_usuario) FROM acessos
                 WHERE id_usuario IS NOT NULL
                   AND CAST(status AS TEXT) ILIKE '%sucesso%'
                   $filtroData"
            )->fetchColumn();
        } catch (Exception $e) {
            // fallback seguro
        }

        return [
            'total_acessos'   => $total,
            'acessos_sucesso' => $sucesso,
            'acessos_falha'   => $falha,
            'usuarios_unicos' => $unicos,
        ];
    }

    public function listarRecentes($dias = 30, $limite = 200) {
        $intervalo  = (int)$dias;
        $limite     = (int)$limite;
        $filtroData = $intervalo > 0 ? "AND a.data >= NOW() - INTERVAL '{$intervalo} days'" : '';

        $sql = "
            SELECT
                a.id_acesso,
                a.data,
                a.email,
                CAST(a.status AS TEXT) AS status,
                u.nome                 AS usuario_nome,
                CAST(u.perfil AS TEXT) AS usuario_perfil
            FROM acessos a
            LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
            WHERE 1=1 $filtroData
            ORDER BY a.data DESC
            LIMIT $limite
        ";
        try {
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function acessosPorDia($dias = 30) {
        $intervalo = (int)$dias;
        $sql = "
            SELECT
                DATE(data) AS dia,
                COUNT(*) FILTER (WHERE CAST(status AS TEXT) ILIKE '%sucesso%') AS sucesso,
                COUNT(*) FILTER (WHERE CAST(status AS TEXT) NOT ILIKE '%sucesso%') AS falha
            FROM acessos
            WHERE data >= NOW() - INTERVAL '{$intervalo} days'
            GROUP BY dia
            ORDER BY dia ASC
        ";
        try {
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
