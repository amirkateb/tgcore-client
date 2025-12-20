<?php

namespace amirkateb\TGCoreClient\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use amirkateb\TGCoreClient\Contracts\BotSecretResolver;

class TGCoreGatewayClient
{
    public function __construct(
        private readonly BotSecretResolver $resolver
    ) {
    }

    public function sendMessage(string $botUuid, int|string $chatId, string $text, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-message', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $options));
    }

    public function sendChatAction(string $botUuid, int|string $chatId, string $action, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-chat-action', array_merge([
            'chat_id' => $chatId,
            'action' => $action,
        ], $options));
    }

    public function editMessageText(string $botUuid, array $data): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/edit-message-text', $data);
    }

    public function answerCallbackQuery(string $botUuid, array $data): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/answer-callback-query', $data);
    }

    public function sendLocation(string $botUuid, int|string $chatId, float $latitude, float $longitude, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-location', array_merge([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], $options));
    }

    public function sendPhoto(string $botUuid, int|string $chatId, string $photo, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-photo', array_merge([
            'chat_id' => $chatId,
            'photo' => $photo,
        ], $options));
    }

    public function sendPhotoUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-photo', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'photo' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendDocument(string $botUuid, int|string $chatId, string $document, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-document', array_merge([
            'chat_id' => $chatId,
            'document' => $document,
        ], $options));
    }

    public function sendDocumentUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-document', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'document' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendVoice(string $botUuid, int|string $chatId, string $voice, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-voice', array_merge([
            'chat_id' => $chatId,
            'voice' => $voice,
        ], $options));
    }

    public function sendVoiceUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-voice', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'voice' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendAudio(string $botUuid, int|string $chatId, string $audio, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-audio', array_merge([
            'chat_id' => $chatId,
            'audio' => $audio,
        ], $options));
    }

    public function sendAudioUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-audio', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'audio' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendVideo(string $botUuid, int|string $chatId, string $video, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-video', array_merge([
            'chat_id' => $chatId,
            'video' => $video,
        ], $options));
    }

    public function sendVideoUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-video', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'video' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendSticker(string $botUuid, int|string $chatId, string $sticker, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-sticker', array_merge([
            'chat_id' => $chatId,
            'sticker' => $sticker,
        ], $options));
    }

    public function sendStickerUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-sticker', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'sticker' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendAnimation(string $botUuid, int|string $chatId, string $animation, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-animation', array_merge([
            'chat_id' => $chatId,
            'animation' => $animation,
        ], $options));
    }

    public function sendAnimationUpload(string $botUuid, int|string $chatId, string $filePath, ?string $fileName = null, array $options = []): array
    {
        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-animation', array_merge([
            'chat_id' => $chatId,
        ], $options), [
            'animation' => ['path' => $filePath, 'name' => $fileName],
        ]);
    }

    public function sendMediaGroup(string $botUuid, int|string $chatId, array $media, array $options = []): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-media-group', array_merge([
            'chat_id' => $chatId,
            'media' => $media,
        ], $options));
    }

    public function sendMediaGroupUpload(string $botUuid, int|string $chatId, array $media, array $attachments, array $options = []): array
    {
        $files = [];
        foreach ($attachments as $field => $pathOrInfo) {
            if (is_string($pathOrInfo)) {
                $files[(string) $field] = ['path' => $pathOrInfo, 'name' => null];
                continue;
            }

            if (is_array($pathOrInfo)) {
                $files[(string) $field] = ['path' => (string) ($pathOrInfo['path'] ?? ''), 'name' => $pathOrInfo['name'] ?? null];
            }
        }

        $fields = array_merge([
            'chat_id' => $chatId,
            'media' => json_encode($media, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ], $options);

        return $this->postMultipart($botUuid, '/bots/' . $botUuid . '/send-media-group', $fields, $files);
    }

    public function sendInvoice(string $botUuid, array $data): array
    {
        return $this->postJson($botUuid, '/bots/' . $botUuid . '/send-invoice', $data);
    }

    private function postJson(string $botUuid, string $path, array $payload): array
    {
        $limitRes = $this->enforceGatewayRateLimit($botUuid);
        if ($limitRes !== null) {
            return $limitRes;
        }

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
        if (!is_string($body) || $body === '') {
            return ['ok' => false, 'error' => 'json_encode_failed'];
        }

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

    private function postMultipart(string $botUuid, string $path, array $fields, array $files): array
    {
        $limitRes = $this->enforceGatewayRateLimit($botUuid);
        if ($limitRes !== null) {
            return $limitRes;
        }

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

        $files = $this->normalizeFiles($files);

        $canonical = $this->canonicalJson($fields);
        if (!is_string($canonical) || $canonical === '') {
            return ['ok' => false, 'error' => 'canonical_json_failed'];
        }

        $contentHash = $this->filesContentHash($files);

        $ts = time();
        $sig = 'sha256=' . hash_hmac('sha256', $ts . "\n" . $contentHash . "\n" . $canonical, $secret);

        $timeout = (int) config('tgcore_client.gateway.timeout_seconds', 15);
        $connect = (int) config('tgcore_client.gateway.connect_timeout_seconds', 7);

        $handles = [];

        try {
            $req = Http::timeout($timeout)->connectTimeout($connect)->withHeaders([
                'X-TGCore-Timestamp' => (string) $ts,
                'X-TGCore-Signature' => $sig,
                'X-TGCore-Bot-UUID' => $botUuid,
            ]);

            ksort($files);

            foreach ($files as $field => $info) {
                $p = (string) ($info['path'] ?? '');
                $n = (string) ($info['name'] ?? '');

                if ($p === '' || !is_file($p)) {
                    return ['ok' => false, 'error' => 'file_missing:' . $field];
                }

                if ($n === '') {
                    $n = basename($p);
                }

                $h = fopen($p, 'r');
                if ($h === false) {
                    return ['ok' => false, 'error' => 'file_open_failed:' . $field];
                }

                $handles[] = $h;
                $req = $req->attach((string) $field, $h, $n);
            }

            $res = $req->post($url, $fields);

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
        } finally {
            foreach ($handles as $h) {
                if (is_resource($h)) {
                    fclose($h);
                }
            }
        }
    }

    private function enforceGatewayRateLimit(string $botUuid): ?array
    {
        $limit = (int) config('tgcore_client.rate_limits.gateway_per_minute', 120);
        if ($limit <= 0) {
            return null;
        }

        $key = 'tgcore:gateway:client:' . $botUuid;

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return [
                'ok' => false,
                'error' => 'rate_limited_client',
                'retry_after_seconds' => RateLimiter::availableIn($key),
            ];
        }

        RateLimiter::hit($key, 60);

        return null;
    }

    private function normalizeFiles(array $files): array
    {
        $out = [];

        foreach ($files as $k => $v) {
            if (is_string($v)) {
                $out[(string) $k] = ['path' => $v, 'name' => null];
                continue;
            }

            if (is_array($v)) {
                $out[(string) $k] = [
                    'path' => (string) ($v['path'] ?? ''),
                    'name' => $v['name'] ?? null,
                ];
            }
        }

        return $out;
    }

    private function filesContentHash(array $files): string
    {
        ksort($files);

        $buf = '';
        foreach ($files as $field => $info) {
            $p = (string) ($info['path'] ?? '');
            $size = is_file($p) ? (int) filesize($p) : 0;
            $h = is_file($p) ? hash_file('sha256', $p) : '';
            $buf .= (string) $field . ':' . $h . ':' . $size . ';';
        }

        return hash('sha256', $buf);
    }

    private function canonicalJson(array $data): string
    {
        $normalized = $this->normalize($data);
        $j = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return is_string($j) ? $j : '';
    }

    private function normalize(mixed $v): mixed
    {
        if (!is_array($v)) {
            return $v;
        }

        $isList = array_keys($v) === range(0, count($v) - 1);

        if ($isList) {
            return array_map(fn ($x) => $this->normalize($x), $v);
        }

        ksort($v);

        $out = [];
        foreach ($v as $k => $x) {
            $out[(string) $k] = $this->normalize($x);
        }

        return $out;
    }
}