<?php

class WompiService
{
    private string $publicKey;
    private string $privateKey;
    private string $eventsKey;
    private string $integrityKey;
    private string $checkoutUrl;
    private string $apiUrl;

    public function __construct()
    {
        $this->publicKey    = $_ENV['WOMPI_PUBLIC_KEY']    ?? '';
        $this->privateKey   = $_ENV['WOMPI_PRIVATE_KEY']   ?? '';
        $this->eventsKey    = $_ENV['WOMPI_EVENTS_KEY']    ?? '';
        $this->integrityKey = $_ENV['WOMPI_INTEGRITY_KEY'] ?? '';
        $sandbox            = ($_ENV['WOMPI_ENV'] ?? 'sandbox') !== 'production';

        $this->checkoutUrl = 'https://checkout.wompi.co/p/';
        $this->apiUrl      = $sandbox
            ? 'https://sandbox.wompi.co/v1'
            : 'https://production.wompi.co/v1';
    }

    public function generarReferencia(): string
    {
        return 'SIA-' . strtoupper(bin2hex(random_bytes(8))) . '-' . time();
    }

    public function firmaIntegridad(string $referencia, int $montoCents, string $moneda = 'COP'): string
    {
        return hash('sha256', $referencia . $montoCents . $moneda . $this->integrityKey);
    }

    public function urlCheckout(
        string $referencia,
        int    $montoCents,
        string $redirectUrl,
        string $email    = '',
        string $moneda   = 'COP'
    ): string {
        // http_build_query codifica ':' como '%3A' en los nombres de parámetro,
        // lo que rompe signature:integrity y customer-data:email en Wompi.
        // Se construye la query manualmente para preservar los ':' sin codificar.
        $query  = 'public-key='      . urlencode($this->publicKey);
        $query .= '&currency='       . urlencode($moneda);
        $query .= '&amount-in-cents='. urlencode((string)$montoCents);
        $query .= '&reference='      . urlencode($referencia);
        $query .= '&redirect-url='   . urlencode($redirectUrl);
        $query .= '&signature:integrity=' . urlencode($this->firmaIntegridad($referencia, $montoCents, $moneda));

        if ($email !== '') {
            $query .= '&customer-data:email=' . urlencode($email);
        }

        return $this->checkoutUrl . '?' . $query;
    }

    public function obtenerTransaccion(string $transaccionId): ?array
    {
        $url = $this->apiUrl . '/transactions/' . urlencode($transaccionId);
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => 'Authorization: Bearer ' . $this->privateKey . "\r\nContent-Type: application/json\r\n",
                'timeout' => 10,
            ],
        ]);
        $resp = @file_get_contents($url, false, $ctx);
        if ($resp === false) {
            return null;
        }
        $data = json_decode($resp, true);
        return $data['data'] ?? null;
    }

    public function verificarWebhook(string $payload, string $checksum): bool
    {
        // Wompi no firma con HMAC el payload completo — envía el checksum
        // basado en los campos de la transacción + events_key.
        // Implementamos la verificación oficial:
        // SHA256( transaction.id + transaction.status + transaction.amount_in_cents + transaction.occurred_at + events_key )
        $decoded     = json_decode($payload, true);
        $transaction = $decoded['data']['transaction'] ?? [];
        $cadena = ($transaction['id']              ?? '') .
                  ($transaction['status']          ?? '') .
                  ($transaction['amount_in_cents'] ?? '') .
                  ($decoded['sent_at']             ?? '') .
                  $this->eventsKey;
        return hash_equals(hash('sha256', $cadena), $checksum);
    }

    public function getPublicKey(): string { return $this->publicKey; }
}
