<?php
/**
 * lib/Logger.php
 * Sistema de auditoria do SIMPA — salva logs na tabela logs_auditoria do Supabase.
 *
 * Tabela necessária (ver logs/criar_tabela_logs.sql):
 *   logs_auditoria (id, criado_em, modulo, acao, descricao, id_usuario, nome_usuario, ip, contexto)
 */
class Logger {

    // ── Módulos ──────────────────────────────────────────────────────────────
    const USUARIOS      = 'USUARIOS';
    const PROJETOS      = 'PROJETOS';
    const PARTICIPACOES = 'PARTICIPACOES';
    const DOCUMENTOS    = 'DOCUMENTOS';
    const PERFIL        = 'PERFIL';
    const LOGIN         = 'LOGIN';
    const SISTEMA       = 'SISTEMA';

    // ── Ações ────────────────────────────────────────────────────────────────
    const CRIAR         = 'CRIAR';
    const EDITAR        = 'EDITAR';
    const EXCLUIR       = 'EXCLUIR';
    const ATIVAR        = 'ATIVAR';
    const DESATIVAR     = 'DESATIVAR';
    const APROVAR       = 'APROVAR';
    const REJEITAR      = 'REJEITAR';
    const ALTERAR_SENHA = 'ALTERAR_SENHA';
    const VINCULAR      = 'VINCULAR';
    const DESVINCULAR   = 'DESVINCULAR';
    const LOGIN_OK      = 'LOGIN_OK';
    const LOGIN_FALHA   = 'LOGIN_FALHA';
    const EXPORTAR      = 'EXPORTAR';

    private static ?PDO $pdo = null;

    /**
     * Define a conexão PDO a ser usada.
     * Chamado automaticamente pelo registrar() se não configurado.
     */
    public static function setPDO(PDO $pdo): void {
        self::$pdo = $pdo;
    }

    /**
     * Registra uma ação no banco de dados.
     *
     * @param string $modulo    Constante de módulo (ex: Logger::USUARIOS)
     * @param string $acao      Constante de ação (ex: Logger::CRIAR)
     * @param string $descricao Descrição legível da ação
     * @param array  $contexto  Dados adicionais salvos como JSON
     */
    public static function registrar(
        string $modulo,
        string $acao,
        string $descricao,
        array  $contexto = []
    ): void {
        if (!self::$pdo) return; // sem conexão, silencia

        $idUsuario   = $_SESSION['id_usuario'] ?? null;
        $nomeUsuario = $_SESSION['nome']       ?? 'Sistema';
        $ip          = self::resolverIP();

        try {
            $stmt = self::$pdo->prepare("
                INSERT INTO logs_auditoria
                    (modulo, acao, descricao, id_usuario, nome_usuario, ip, contexto)
                VALUES
                    (:modulo, :acao, :descricao, :id_usuario, :nome_usuario, :ip, :contexto)
            ");
            $stmt->execute([
                ':modulo'       => strtoupper($modulo),
                ':acao'         => strtoupper($acao),
                ':descricao'    => $descricao,
                ':id_usuario'   => $idUsuario,
                ':nome_usuario' => $nomeUsuario,
                ':ip'           => $ip,
                ':contexto'     => empty($contexto) ? null : json_encode($contexto, JSON_UNESCAPED_UNICODE),
            ]);
        } catch (Exception $e) {
            // Não quebrar o sistema por falha no log
            // Opcional: logar em arquivo de fallback
            $fallback = dirname(__DIR__) . '/logs/fallback.log';
            @file_put_contents($fallback,
                '[' . date('Y-m-d H:i:s') . "] ERRO AO SALVAR LOG: " . $e->getMessage() . " | $modulo/$acao — $descricao\n",
                FILE_APPEND | LOCK_EX
            );
        }
    }

    /**
     * Busca logs do banco com filtros.
     */
    public static function buscar(array $filtros = [], int $limite = 200, int $offset = 0): array {
        if (!self::$pdo) return [];

        $where  = ['1=1'];
        $params = [];

        if (!empty($filtros['modulo'])) {
            $where[]          = 'modulo = :modulo';
            $params[':modulo']= strtoupper($filtros['modulo']);
        }
        if (!empty($filtros['acao'])) {
            $where[]        = 'acao = :acao';
            $params[':acao']= strtoupper($filtros['acao']);
        }
        if (!empty($filtros['id_usuario'])) {
            $where[]             = 'id_usuario = :id_usuario';
            $params[':id_usuario']= (int)$filtros['id_usuario'];
        }
        if (!empty($filtros['busca'])) {
            $where[]         = "(descricao ILIKE :busca OR nome_usuario ILIKE :busca)";
            $params[':busca']= '%' . $filtros['busca'] . '%';
        }
        if (!empty($filtros['data_inicio'])) {
            $where[]              = 'criado_em >= :data_inicio';
            $params[':data_inicio']= $filtros['data_inicio'] . ' 00:00:00';
        }
        if (!empty($filtros['data_fim'])) {
            $where[]           = 'criado_em <= :data_fim';
            $params[':data_fim']= $filtros['data_fim'] . ' 23:59:59';
        }

        $sql = "
            SELECT
                l.id,
                TO_CHAR(l.criado_em AT TIME ZONE 'America/Fortaleza', 'DD/MM/YYYY HH24:MI:SS') AS data_fmt,
                l.criado_em,
                l.modulo,
                l.acao,
                l.descricao,
                l.id_usuario,
                l.nome_usuario,
                l.ip,
                l.contexto
            FROM logs_auditoria l
            WHERE " . implode(' AND ', $where) . "
            ORDER BY l.criado_em DESC
            LIMIT " . (int)$limite . " OFFSET " . (int)$offset;

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Decodificar o JSON do contexto
            foreach ($rows as &$row) {
                $row['contexto'] = $row['contexto'] ? json_decode($row['contexto'], true) : [];
            }
            return $rows;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Total de registros com os mesmos filtros (para paginação).
     */
    public static function total(array $filtros = []): int {
        if (!self::$pdo) return 0;

        $where  = ['1=1'];
        $params = [];

        if (!empty($filtros['modulo'])) { $where[] = 'modulo = :modulo'; $params[':modulo'] = strtoupper($filtros['modulo']); }
        if (!empty($filtros['acao']))   { $where[] = 'acao = :acao';     $params[':acao']   = strtoupper($filtros['acao']); }
        if (!empty($filtros['busca']))  { $where[] = "(descricao ILIKE :busca OR nome_usuario ILIKE :busca)"; $params[':busca'] = '%' . $filtros['busca'] . '%'; }
        if (!empty($filtros['data_inicio'])) { $where[] = 'criado_em >= :di'; $params[':di'] = $filtros['data_inicio'] . ' 00:00:00'; }
        if (!empty($filtros['data_fim']))    { $where[] = 'criado_em <= :df'; $params[':df'] = $filtros['data_fim']    . ' 23:59:59'; }

        try {
            $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM logs_auditoria WHERE " . implode(' AND ', $where));
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) { return 0; }
    }

    /**
     * Estatísticas resumidas para os cards do dashboard.
     */
    public static function estatisticas(): array {
        if (!self::$pdo) return [];
        try {
            $row = self::$pdo->query("
                SELECT
                    COUNT(*)                                                          AS total,
                    COUNT(*) FILTER (WHERE acao = 'LOGIN_OK')                        AS login_ok,
                    COUNT(*) FILTER (WHERE acao = 'LOGIN_FALHA')                     AS login_falha,
                    COUNT(*) FILTER (WHERE acao IN ('CRIAR','EDITAR','VINCULAR'))     AS modificacoes,
                    COUNT(*) FILTER (WHERE acao IN ('EXCLUIR','DESATIVAR','REJEITAR'))AS remocoes,
                    COUNT(*) FILTER (WHERE criado_em >= NOW() - INTERVAL '24 hours') AS ultimas_24h
                FROM logs_auditoria
            ")->fetch(PDO::FETCH_ASSOC);
            return $row ?: [];
        } catch (Exception $e) { return []; }
    }

    private static function resolverIP(): string {
        foreach (['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) return trim(explode(',', $_SERVER[$k])[0]);
        }
        return '—';
    }
}
?>
