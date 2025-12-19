<?php

namespace amirkateb\TGCoreClient\DTO;

class TGCoreUpdate
{
    public function __construct(
        public readonly array $meta,
        public readonly array $payload
    ) {
    }

    public static function fromArray(array $data): self
    {
        $meta = is_array($data['tgcore'] ?? null) ? $data['tgcore'] : [];
        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];
        return new self($meta, $payload);
    }

    public function botUuid(): ?string
    {
        $v = $this->meta['bot_uuid'] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
    }

    public function updateDbId(): ?int
    {
        $v = $this->meta['update_db_id'] ?? null;
        return is_numeric($v) ? (int) $v : null;
    }

    public function telegramUpdateId(): ?int
    {
        $v = $this->payload['update_id'] ?? ($this->meta['update_id'] ?? null);
        return is_numeric($v) ? (int) $v : null;
    }

    public function type(): string
    {
        $t = $this->meta['type'] ?? null;
        if (is_string($t) && $t !== '') {
            return $t;
        }

        foreach ([
                     'message',
                     'edited_message',
                     'channel_post',
                     'edited_channel_post',
                     'callback_query',
                     'inline_query',
                     'chosen_inline_result',
                     'chat_member',
                     'my_chat_member',
                     'poll',
                     'poll_answer',
                     'shipping_query',
                     'pre_checkout_query',
                 ] as $k) {
            if (isset($this->payload[$k])) {
                return $k;
            }
        }

        return 'unknown';
    }

    public function isRedrive(): bool
    {
        return (bool) ($this->meta['is_redrive'] ?? false);
    }

    public function messageText(): ?string
    {
        $msg = $this->payload['message'] ?? null;
        if (is_array($msg) && isset($msg['text']) && is_string($msg['text'])) {
            return $msg['text'];
        }
        return null;
    }

    public function callbackData(): ?string
    {
        $cb = $this->payload['callback_query'] ?? null;
        if (is_array($cb) && isset($cb['data']) && is_string($cb['data'])) {
            return $cb['data'];
        }
        return null;
    }

    public function chatId(): ?int
    {
        $t = $this->type();

        if ($t === 'callback_query') {
            $m = $this->payload['callback_query']['message'] ?? null;
            $c = is_array($m) ? ($m['chat']['id'] ?? null) : null;
            return is_numeric($c) ? (int) $c : null;
        }

        $node = $this->payload[$t] ?? null;
        if (is_array($node)) {
            $c = $node['chat']['id'] ?? null;
            return is_numeric($c) ? (int) $c : null;
        }

        return null;
    }

    public function fromId(): ?int
    {
        $t = $this->type();

        if ($t === 'callback_query') {
            $f = $this->payload['callback_query']['from']['id'] ?? null;
            return is_numeric($f) ? (int) $f : null;
        }

        $node = $this->payload[$t] ?? null;
        if (is_array($node)) {
            $f = $node['from']['id'] ?? ($node['sender_chat']['id'] ?? null);
            return is_numeric($f) ? (int) $f : null;
        }

        return null;
    }

    public function messageId(): ?int
    {
        $t = $this->type();

        if ($t === 'callback_query') {
            $m = $this->payload['callback_query']['message'] ?? null;
            $mid = is_array($m) ? ($m['message_id'] ?? null) : null;
            return is_numeric($mid) ? (int) $mid : null;
        }

        $node = $this->payload[$t] ?? null;
        if (is_array($node)) {
            $mid = $node['message_id'] ?? null;
            return is_numeric($mid) ? (int) $mid : null;
        }

        return null;
    }

    public function fileIds(): array
    {
        $out = [];
        $this->collectFiles($this->payload, $out);
        $out = array_values(array_unique(array_filter($out, fn ($v) => is_string($v) && $v !== '')));
        return $out;
    }

    private function collectFiles(array $node, array &$out): void
    {
        foreach (['photo', 'document', 'audio', 'voice', 'video', 'video_note', 'sticker', 'animation'] as $k) {
            if (!isset($node[$k])) {
                continue;
            }

            $v = $node[$k];

            if (is_array($v) && $k === 'photo') {
                foreach ($v as $p) {
                    if (is_array($p) && isset($p['file_id']) && is_string($p['file_id'])) {
                        $out[] = $p['file_id'];
                    }
                }
            } elseif (is_array($v)) {
                if (isset($v['file_id']) && is_string($v['file_id'])) {
                    $out[] = $v['file_id'];
                }
            }
        }

        foreach ($node as $v) {
            if (is_array($v)) {
                $this->collectFiles($v, $out);
            }
        }
    }
}
