<?php

namespace amirkateb\TGCoreClient\Resolvers;

use amirkateb\TGCoreClient\Contracts\BotSecretResolver;
use amirkateb\TGCoreClient\Models\TGCoreClientBot;

class DatabaseBotSecretResolver implements BotSecretResolver
{
    public function resolve(string $botUuid, array $meta = []): ?string
    {
        $bot = TGCoreClientBot::query()
            ->where('bot_uuid', $botUuid)
            ->where('is_active', true)
            ->first();

        if (!$bot) {
            return null;
        }

        return (string) $bot->secret;
    }
}
