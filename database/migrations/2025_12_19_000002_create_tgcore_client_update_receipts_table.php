<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = (string) config('tgcore_client.database.receipts_table', 'tgcore_client_update_receipts');

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('bot_uuid', 64)->index();
            $table->unsignedBigInteger('update_db_id')->index();
            $table->unsignedBigInteger('telegram_update_id')->nullable()->index();
            $table->string('type', 80)->nullable()->index();
            $table->string('payload_hash', 64)->index();
            $table->string('status', 40)->default('accepted')->index();
            $table->dateTime('received_at')->index();
            $table->dateTime('handled_at')->nullable()->index();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->unique(['bot_uuid', 'update_db_id'], 'tgcore_client_receipts_unique');
        });
    }

    public function down(): void
    {
        $table = (string) config('tgcore_client.database.receipts_table', 'tgcore_client_update_receipts');
        Schema::dropIfExists($table);
    }
};
