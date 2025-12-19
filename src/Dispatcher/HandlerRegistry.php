<?php

namespace amirkateb\TGCoreClient\Dispatcher;

class HandlerRegistry
{
    public function typeHandlers(string $type): array
    {
        $types = (array) config('tgcore_client.handlers.types', []);
        $list = $types[$type] ?? [];
        return $this->normalizeList($list);
    }

    public function commandHandlers(string $command): array
    {
        $commands = (array) config('tgcore_client.handlers.commands', []);
        $list = $commands[$command] ?? [];
        return $this->normalizeList($list);
    }

    public function defaultHandlers(): array
    {
        $list = (array) config('tgcore_client.handlers.default', []);
        return $this->normalizeList($list);
    }

    private function normalizeList(mixed $list): array
    {
        if (is_string($list) && $list !== '') {
            return [$list];
        }

        if (!is_array($list)) {
            return [];
        }

        return array_values(array_filter($list, fn ($v) => is_string($v) && $v !== ''));
    }
}
