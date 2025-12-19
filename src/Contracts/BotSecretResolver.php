<?php

namespace amirkateb\TGCoreClient\Contracts;

interface BotSecretResolver
{
    public function resolve(string $botUuid, array $meta = []): ?string;
}
