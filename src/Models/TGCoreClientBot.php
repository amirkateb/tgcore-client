<?php

namespace amirkateb\TGCoreClient\Models;

use Illuminate\Database\Eloquent\Model;

class TGCoreClientBot extends Model
{
    protected $table;

    protected $fillable = [
        'bot_uuid',
        'name',
        'secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = (string) config('tgcore_client.database.bots_table', 'tgcore_client_bots');
    }
}
