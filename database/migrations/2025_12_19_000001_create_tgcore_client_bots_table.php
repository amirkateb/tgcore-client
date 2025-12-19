<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = (string) config('tgcore_client.database.bots_table', 'tgcore_client_bots');

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('bot_uuid', 64)->unique();
            $table->string('name', 190)->nullable();
            $table->string('secret', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $table = (string) config('tgcore_client.database.bots_table', 'tgcore_client_bots');
        Schema::dropIfExists($table);
    }
};
