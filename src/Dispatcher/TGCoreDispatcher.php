<?php

namespace amirkateb\TGCoreClient\Dispatcher;

use Illuminate\Support\Facades\App;
use amirkateb\TGCoreClient\Contracts\UpdateHandler;
use amirkateb\TGCoreClient\DTO\TGCoreUpdate;

class TGCoreDispatcher
{
    public function __construct(
        private readonly HandlerRegistry $registry
    ) {
    }

    public function dispatch(TGCoreUpdate $update): array
    {
        $type = $update->type();
        $handlers = $this->registry->typeHandlers($type);

        $cmd = $this->extractCommand($update);
        if ($cmd) {
            $handlers = array_values(array_unique(array_merge($this->registry->commandHandlers($cmd), $handlers)));
        }

        if (!$handlers) {
            $handlers = $this->registry->defaultHandlers();
        }

        $results = [];

        foreach ($handlers as $handlerClass) {
            $h = App::make($handlerClass);

            if ($h instanceof UpdateHandler) {
                $results[] = $h->handle($update);
                continue;
            }

            if (is_callable([$h, 'handle'])) {
                $results[] = $h->handle($update);
                continue;
            }

            $results[] = null;
        }

        return $results;
    }

    private function extractCommand(TGCoreUpdate $update): ?string
    {
        if ($update->type() !== 'message') {
            return null;
        }

        $text = $update->messageText();
        if (!is_string($text) || $text === '') {
            return null;
        }

        $text = trim($text);
        if (!str_starts_with($text, '/')) {
            return null;
        }

        $first = explode(' ', $text, 2)[0];
        $first = explode('@', $first, 2)[0];
        $cmd = ltrim($first, '/');

        return $cmd !== '' ? $cmd : null;
    }
}
