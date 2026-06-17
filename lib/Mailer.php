<?php
class Mailer {
    private string $apiKey;
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

        $this->apiKey        = getenv('RESEND_API_KEY') ?: '';
        $this->remetente     = getenv('SMTP_FROM')      ?: 'onboarding@resend.dev';
        $this->nomeRemetente = getenv('SMTP_NAME')      ?: 'SIMPA - UEMA ProExae';
        $this->configurado   = !empty($this->apiKey);
    }

    public function estaConfigurado(): bool {
        return $this->configurado;
    }

    public function enviar(string $para, string $assunto, string $corpoTexto, string $corpoHtml = ''): true|string {
        if (!$this->configurado) {
            return 'SMTP_NAO_CONFIGURADO';
        }

        $payload = json_encode([
            'from'    => "{$this->nomeRemetente} <{$this->remetente}>",
            'to'      => [$para],
            'subject' => $assunto,
            'text'    => $corpoTexto,
            'html'    => $corpoHtml ?: "<pre>{$corpoTexto}</pre>",
        ]);

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) return "Erro cURL: $curlErr";

        $json = json_decode($response, true);
        if ($httpCode === 200 || $httpCode === 201) return true;

        $msg = $json['message'] ?? $json['name'] ?? $response;
        return "Resend erro ($httpCode): $msg";
    }
}
?>