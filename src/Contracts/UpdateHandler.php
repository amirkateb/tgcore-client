<?php

namespace amirkateb\TGCoreClient\Contracts;

use amirkateb\TGCoreClient\DTO\TGCoreUpdate;

interface UpdateHandler
{
    public function handle(TGCoreUpdate $update): mixed;
}
