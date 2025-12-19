<?php

use Illuminate\Support\Facades\Route;
use amirkateb\TGCoreClient\Http\Controllers\TGCoreIngestController;

$path = (string) config('tgcore_client.route.path', '/tgcore/ingest');
$middleware = (array) config('tgcore_client.route.middleware', ['api', 'tgcore-client.verify']);

Route::post($path, TGCoreIngestController::class)
    ->middleware($middleware)
    ->name('tgcore_client.ingest');
