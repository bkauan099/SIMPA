<?php
/**
 * lib/Mailer.php — Envio SMTP nativo (sem dependências externas)
 * Compatível com Gmail, Outlook, Mailtrap, SendGrid, etc.
 *
 * CONFIGURAÇÃO (arquivo .env na raiz ou variáveis de ambiente do servidor):
 *   SMTP_HOST = smtp.gmail.com
 *   SMTP_PORT = 587
 *   SMTP_USER = seuemail@gmail.com
 *   SMTP_PASS = sua_senha_de_app
 *   SMTP_FROM = seuemail@gmail.com
 *   SMTP_NAME = SIMPA - UEMA
 *
 * Para Gmail: Ative "Autenticação em 2 etapas" e gere uma "Senha de App"
 * em https://myaccount.google.com/apppasswords
 */
class Mailer {
    private string $host;
    private int    $port;
    private string $user;
    private string $pass;
    private string $remetente;
    private string $nomeRemetente;
    private bool   $configurado;

    public function __construct() {
        // Carrega .env se existir
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
                if (str_starts_with(trim($linha), '#') || !str_contains($linha, '=')) continue;
                [$chave, $valor] = explode('=', $linha, 2);
                putenv(trim($chave) . '=' . trim($valor));
            }
        }

        $this->host          = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->port          = (int)(getenv('SMTP_PORT') ?: 587);
        $this->user          = getenv('SMTP_USER') ?: '';
        $this->pass          = getenv('SMTP_PASS') ?: '';
        $this->remetente     = getenv('SMTP_FROM') ?: $this->user;
        $this->nomeRemetente = getenv('SMTP_NAME') ?: 'SIMPA - UEMA';
        $this->configurado   = !empty($this->user) && !empty($this->pass);
    }

    public function estaConfigurado(): bool {
        return $this->configurado;
    }

    /**
     * Envia e-mail via SMTP com STARTTLS.
     * @return true|string  true em sucesso, string de erro em falha
     */
    public function enviar(string $para, string $assunto, string $corpoTexto, string $corpoHtml = ''): true|string {
        if (!$this->configurado) {
            return 'SMTP_NAO_CONFIGURADO';
        }

        $errno = 0; $errstr = '';
        $sock = @fsockopen($this->host, $this->port, $errno, $errstr, 15);
        if (!$sock) {
            return "Não foi possível conectar ao servidor SMTP ({$this->host}:{$this->port}). Verifique SMTP_HOST/SMTP_PORT.";
        }

        try {
            $this->ler($sock);
            $this->cmd($sock, "EHLO localhost");
            $this->cmd($sock, "STARTTLS");
            if (!stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT)) {
                return "Falha ao iniciar TLS.";
            }
            $this->cmd($sock, "EHLO localhost");
            $this->cmd($sock, "AUTH LOGIN");
            $this->cmd($sock, base64_encode($this->user));
            $resp = $this->cmd($sock, base64_encode($this->pass));
            if (!str_starts_with(trim($resp), '235')) {
                return "Autenticação falhou. Verifique SMTP_USER e SMTP_PASS.";
            }

            $this->cmd($sock, "MAIL FROM:<{$this->remetente}>");
            $rc = $this->cmd($sock, "RCPT TO:<{$para}>");
            if (!str_starts_with(trim($rc), '25')) {
                return "Destinatário rejeitado: $para";
            }
            $this->cmd($sock, "DATA");

            // Montar e-mail multipart
            $boundary = 'SIMPA_' . md5(uniqid());
            $msg  = "From: =?UTF-8?B?" . base64_encode($this->nomeRemetente) . "?= <{$this->remetente}>\r\n";
            $msg .= "To: <{$para}>\r\n";
            $msg .= "Subject: =?UTF-8?B?" . base64_encode($assunto) . "?=\r\n";
            $msg .= "MIME-Version: 1.0\r\n";
            $msg .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $msg .= "Date: " . date('r') . "\r\n\r\n";
            $msg .= "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
            $msg .= chunk_split(base64_encode($corpoTexto)) . "\r\n";
            if ($corpoHtml) {
                $msg .= "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
                $msg .= chunk_split(base64_encode($corpoHtml)) . "\r\n";
            }
            $msg .= "--{$boundary}--\r\n.\r\n";

            fwrite($sock, $msg);
            $this->ler($sock);
            $this->cmd($sock, "QUIT");
        } finally {
            fclose($sock);
        }

        return true;
    }

    private function cmd($sock, string $cmd): string {
        fwrite($sock, "$cmd\r\n");
        return $this->ler($sock);
    }
    private function ler($sock): string {
        $resp = '';
        while (!feof($sock) && ($line = fgets($sock, 512))) {
            $resp .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        return $resp;
    }
}
?>
