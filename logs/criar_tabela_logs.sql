-- ============================================================
-- SIMPA — Tabela de Logs de Auditoria
-- Execute este SQL no Supabase > SQL Editor
-- ============================================================

CREATE TABLE IF NOT EXISTS logs_auditoria (
    id          BIGSERIAL PRIMARY KEY,
    criado_em   TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    modulo      VARCHAR(50)  NOT NULL,
    acao        VARCHAR(50)  NOT NULL,
    descricao   TEXT         NOT NULL,
    id_usuario  INT          REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    nome_usuario VARCHAR(255),
    ip          VARCHAR(45),
    contexto    JSONB
);

-- Índices para consultas rápidas
CREATE INDEX IF NOT EXISTS idx_logs_criado_em   ON logs_auditoria (criado_em DESC);
CREATE INDEX IF NOT EXISTS idx_logs_modulo      ON logs_auditoria (modulo);
CREATE INDEX IF NOT EXISTS idx_logs_acao        ON logs_auditoria (acao);
CREATE INDEX IF NOT EXISTS idx_logs_id_usuario  ON logs_auditoria (id_usuario);

-- Comentário na tabela
COMMENT ON TABLE logs_auditoria IS 'Registro de auditoria de todas as ações administrativas do SIMPA';
