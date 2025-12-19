<?php

namespace amirkateb\TGCoreClient\Gateway;

use Illuminate\Support\Facades\Http;
use amirkateb\TGCoreClient\Contracts\BotSecretResolver;

class TGCoreGatewayClient
{
    public function __construct(
        private readonly BotSecretResolver $resolver
    ) {
    }

    public function sendMessage(string $botUuid, int|string $chatId, string $text, array $options = []): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-message', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $options));
    }

    public function sendPhoto(string $botUuid, int|string $chatId, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-photo', array_merge(['chat_id' => $chatId], $data));
    }

    public function sendDocument(string $botUuid, int|string $chatId, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-document', array_merge(['chat_id' => $chatId], $data));
    }

    public function sendVoice(string $botUuid, int|string $chatId, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-voice', array_merge(['chat_id' => $chatId], $data));
    }

    public function sendAudio(string $botUuid, int|string $chatId, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-audio', array_merge(['chat_id' => $chatId], $data));
    }

    public function sendVideo(string $botUuid, int|string $chatId, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-video', array_merge(['chat_id' => $chatId], $data));
    }

    public function sendLocation(string $botUuid, int|string $chatId, float $latitude, float $longitude, array $options = []): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-location', array_merge([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], $options));
    }

    public function sendChatAction(string $botUuid, int|string $chatId, string $action, array $options = []): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/send-chat-action', array_merge([
            'chat_id' => $chatId,
            'action' => $action,
        ], $options));
    }

    public function editMessageText(string $botUuid, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/edit-message-text', $data);
    }

    public function answerCallbackQuery(string $botUuid, array $data): array
    {
        return $this->post($botUuid, '/bots/' . $botUuid . '/answer-callback-query', $data);
    }

    private function post(string $botUuid, string $path, array $payload): array
    {
        $base = (string) config('tgcore_client.gateway.base_url', '');
        $prefix = (string) config('tgcore_client.gateway.path_prefix', '/api/tgcore/consumer');

        $base = rtrim($base, '/');
        $prefix = '/' . trim($prefix, '/');
        $path = '/' . ltrim($path, '/');

        if ($base === '') {
            return ['ok' => false, 'error' => 'gateway_base_url_missing'];
        }

        $secret = $this->resolver->resolve($botUuid, []);
        if (!is_string($secret) || $secret === '') {
            return ['ok' => false, 'error' => 'bot_secret_missing'];
        }

        $url = $base . $prefix . $path;

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $ts = time();
        $sig = 'sha256=' . hash_hmac('sha256', $ts . "\n" . $body, $secret);

        $timeout = (int) config('tgcore_client.gateway.timeout_seconds', 15);
        $connect = (int) config('tgcore_client.gateway.connect_timeout_seconds', 7);

        try {
            $res = Http::timeout($timeout)
                ->connectTimeout($connect)
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'X-TGCore-Timestamp' => (string) $ts,
                    'X-TGCore-Signature' => $sig,
                    'X-TGCore-Bot-UUID' => $botUuid,
                ])
                ->withBody($body, 'application/json')
                ->post($url);

            return [
                'ok' => $res->ok() && (bool) ($res->json('ok') ?? false),
                'http_status' => $res->status(),
                'response' => $res->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
