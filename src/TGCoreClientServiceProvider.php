<?php

namespace amirkateb\TGCoreClient;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use amirkateb\TGCoreClient\Contracts\BotSecretResolver;
use amirkateb\TGCoreClient\Dispatcher\HandlerRegistry;
use amirkateb\TGCoreClient\Dispatcher\TGCoreDispatcher;
use amirkateb\TGCoreClient\Gateway\TGCoreGatewayClient;
use amirkateb\TGCoreClient\Http\Middleware\VerifyTGCoreSignature;
use amirkateb\TGCoreClient\Resolvers\ConfigBotSecretResolver;
use amirkateb\TGCoreClient\Resolvers\DatabaseBotSecretResolver;

class TGCoreClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tgcore_client.php', 'tgcore_client');

        $this->app->singleton(BotSecretResolver::class, function () {
            $mode = (string) config('tgcore_client.resolver', 'config');

            if ($mode === 'database') {
                return $this->app->make(DatabaseBotSecretResolver::class);
            }

            return $this->app->make(ConfigBotSecretResolver::class);
        });

        $this->app->singleton(HandlerRegistry::class, fn () => new HandlerRegistry());
        $this->app->singleton(TGCoreDispatcher::class, fn () => new TGCoreDispatcher($this->app->make(HandlerRegistry::class)));

        $this->app->singleton(TGCoreGatewayClient::class, fn () => new TGCoreGatewayClient($this->app->make(BotSecretResolver::class)));
    }

    public function boot(Router $router): void
    {
        $router->aliasMiddleware('tgcore-client.verify', VerifyTGCoreSignature::class);

        $this->loadRoutesFrom(__DIR__ . '/../routes/tgcore-client.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/tgcore_client.php' => config_path('tgcore_client.php'),
        ], 'tgcore-client-config');
    }
}
