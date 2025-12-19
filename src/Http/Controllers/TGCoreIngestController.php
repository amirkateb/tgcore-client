<?php

namespace amirkateb\TGCoreClient\Http\Controllers;

use Illuminate\Http\Request;
use amirkateb\TGCoreClient\DTO\TGCoreUpdate;
use amirkateb\TGCoreClient\Dispatcher\TGCoreDispatcher;
use amirkateb\TGCoreClient\Jobs\HandleTGCoreUpdateJob;
use amirkateb\TGCoreClient\Models\TGCoreUpdateReceipt;

class TGCoreIngestController
{
    public function __invoke(Request $request, TGCoreDispatcher $dispatcher)
    {
        $raw = (string) $request->getContent();
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return response()->json(['ok' => true], 200);
        }

        $update = TGCoreUpdate::fromArray($data);

        $botUuid = $update->botUuid() ?: (string) $request->attributes->get('tgcore_client.bot_uuid', '');
        $updateDbId = $update->updateDbId();

        if (!is_string($botUuid) || $botUuid === '' || !is_int($updateDbId) || $updateDbId <= 0) {
            return response()->json(['ok' => true], 200);
        }

        $hash = hash('sha256', $raw);
        $type = $update->type();
        $tgUpdateId = $update->telegramUpdateId();

        $existing = TGCoreUpdateReceipt::query()
            ->where('bot_uuid', $botUuid)
            ->where('update_db_id', $updateDbId)
            ->first();

        if ($existing) {
            return response()->json(['ok' => true, 'duplicate' => true], 200);
        }

        $receipt = TGCoreUpdateReceipt::create([
            'bot_uuid' => $botUuid,
            'update_db_id' => $updateDbId,
            'telegram_update_id' => $tgUpdateId,
            'type' => $type,
            'payload_hash' => $hash,
            'status' => 'accepted',
            'received_at' => now(),
            'handled_at' => null,
            'error' => null,
        ]);

        $queueEnabled = (bool) config('tgcore_client.queue.enabled', true);
        $queueName = (string) config('tgcore_client.queue.name', 'default');
        $queueConn = config('tgcore_client.queue.connection', null);

        if ($queueEnabled) {
            $job = new HandleTGCoreUpdateJob($receipt->id, $data);

            if (is_string($queueConn) && $queueConn !== '') {
                $job->onConnection($queueConn);
            }

            $job->onQueue($queueName);

            dispatch($job);

            return response()->json(['ok' => true, 'queued' => true], 200);
        }

        try {
            $dispatcher->dispatch($update);

            $receipt->update([
                'status' => 'handled',
                'handled_at' => now(),
                'error' => null,
            ]);

            return response()->json(['ok' => true, 'handled' => true], 200);
        } catch (\Throwable $e) {
            $receipt->update([
                'status' => 'failed',
                'handled_at' => now(),
                'error' => $e->getMessage(),
            ]);

            $throw = (bool) config('tgcore_client.behavior.throw_on_handler_exception', false);

            if ($throw) {
                throw $e;
            }

            return response()->json(['ok' => true, 'handled' => false], 200);
        }
    }
}
