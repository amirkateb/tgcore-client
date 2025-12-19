<?php

namespace amirkateb\TGCoreClient\Resolvers;

use amirkateb\TGCoreClient\Contracts\BotSecretResolver;

class ConfigBotSecretResolver implements BotSecretResolver
{
    public function resolve(string $botUuid, array $meta = []): ?string
    {
        $bots = (array) config('tgcore_client.bots', []);
        $secret = $bots[$botUuid]['secret'] ?? $bots[$botUuid] ?? null;

        if (!is_string($secret) || $secret === '') {
            return null;
        }

        return $secret;
    }
}
