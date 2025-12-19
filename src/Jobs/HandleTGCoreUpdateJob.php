<?php

namespace amirkateb\TGCoreClient\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use amirkateb\TGCoreClient\DTO\TGCoreUpdate;
use amirkateb\TGCoreClient\Dispatcher\TGCoreDispatcher;
use amirkateb\TGCoreClient\Models\TGCoreUpdateReceipt;

class HandleTGCoreUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $receiptId,
        public readonly array $data
    ) {
    }

    public function handle(TGCoreDispatcher $dispatcher): void
    {
        $receipt = TGCoreUpdateReceipt::find($this->receiptId);
        if (!$receipt) {
            return;
        }

        $update = TGCoreUpdate::fromArray($this->data);

        try {
            $dispatcher->dispatch($update);

            $receipt->update([
                'status' => 'handled',
                'handled_at' => now(),
                'error' => null,
            ]);
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
        }
    }
}
