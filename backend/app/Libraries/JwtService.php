<?php

namespace App\Libraries;

class JwtService
{
    private string $algo = 'sha256';

    public function createToken(array $claims): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $secret = $this->getSecret();
        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES)),
        ];

        $signature = hash_hmac($this->algo, implode('.', $segments), $secret, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;
        $expected = hash_hmac($this->algo, $headerB64 . '.' . $payloadB64, $this->getSecret(), true);
        $signature = $this->base64UrlDecode($signatureB64);

        if ($signature === false || !hash_equals($expected, $signature)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($payloadB64);
        if ($payloadJson === false) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }

        if (isset($payload['exp']) && (int) $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    private function getSecret(): string
    {
        return (string) env('auth.jwtSecret', 'replace-with-strong-secret');
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'), true);
    }
}
