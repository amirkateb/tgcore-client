<?php

namespace amirkateb\TGCoreClient\Models;

use Illuminate\Database\Eloquent\Model;

class TGCoreUpdateReceipt extends Model
{
    protected $table;

    protected $fillable = [
        'bot_uuid',
        'update_db_id',
        'telegram_update_id',
        'type',
        'payload_hash',
        'status',
        'received_at',
        'handled_at',
        'error',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'handled_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = (string) config('tgcore_client.database.receipts_table', 'tgcore_client_update_receipts');
    }
}
